<?php

declare(strict_types=1);

namespace Fulll\Backend\Infra;

use Fulll\Backend\Domain\Fleet;
use Fulll\Backend\Domain\FleetNotFound;
use Fulll\Backend\Domain\FleetRepository;
use Fulll\Backend\Domain\Location;

final readonly class PdoFleetRepository implements FleetRepository
{
    public function __construct(private \PDO $pdo)
    {
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }

    /**
     * Connection parameters come from the standard libpq variables (PGHOST, PGUSER, PGPASSWORD, ...).
     */
    public static function fromEnvironment(): self
    {
        return new self(new \PDO('pgsql:'));
    }

    public function get(string $id): Fleet
    {
        $statement = $this->pdo->prepare('SELECT user_id FROM fleets WHERE id = ?');
        $statement->execute([$id]);
        $userId = $statement->fetchColumn();

        if (!is_string($userId)) {
            throw new FleetNotFound(sprintf('Fleet "%s" not found.', $id));
        }

        $statement = $this->pdo->prepare(
            'SELECT plate_number, latitude, longitude, altitude FROM fleet_vehicles WHERE fleet_id = ?',
        );
        $statement->execute([$id]);

        $vehicles = [];
        /** @var array{plate_number: string, latitude: float|string|null, longitude: float|string|null, altitude: float|string|null} $row */
        foreach ($statement->fetchAll(\PDO::FETCH_ASSOC) as $row) {
            $vehicles[$row['plate_number']] = $row['latitude'] === null || $row['longitude'] === null
                ? null
                : new Location(
                    (float) $row['latitude'],
                    (float) $row['longitude'],
                    $row['altitude'] === null ? null : (float) $row['altitude'],
                );
        }

        return new Fleet($id, $userId, $vehicles);
    }

    /**
     * Upserts the fleet and all its vehicles in one transaction. Vehicles are never deleted
     * (the domain has no "unregister" operation).
     */
    public function save(Fleet $fleet): void
    {
        $this->pdo->beginTransaction();

        try {
            $this->pdo
                ->prepare('INSERT INTO fleets (id, user_id) VALUES (?, ?) ON CONFLICT (id) DO NOTHING')
                ->execute([$fleet->id(), $fleet->userId()]);

            $statement = $this->pdo->prepare(
                'INSERT INTO fleet_vehicles (fleet_id, plate_number, latitude, longitude, altitude)
                VALUES (?, ?, ?, ?, ?)
                ON CONFLICT (fleet_id, plate_number) DO UPDATE
                SET latitude = EXCLUDED.latitude, longitude = EXCLUDED.longitude, altitude = EXCLUDED.altitude',
            );

            foreach ($fleet->vehicles() as $plateNumber => $location) {
                $statement->execute([
                    $fleet->id(),
                    $plateNumber,
                    self::toSql($location?->latitude),
                    self::toSql($location?->longitude),
                    self::toSql($location?->altitude),
                ]);
            }

            $this->pdo->commit();
        } catch (\Throwable $e) {
            $this->pdo->rollBack();

            throw $e;
        }
    }

    /**
     * PDO casts floats with the `precision` ini setting (14 digits), which would round coordinates and break
     * Location equality after a reload. var_export() uses the shortest exact representation instead.
     */
    private static function toSql(?float $value): ?string
    {
        return $value === null ? null : var_export($value, true);
    }
}
