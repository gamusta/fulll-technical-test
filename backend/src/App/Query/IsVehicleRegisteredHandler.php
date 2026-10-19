<?php

declare(strict_types=1);

namespace Fulll\Backend\App\Query;

use Fulll\Backend\Domain\FleetRepository;

final readonly class IsVehicleRegisteredHandler
{
    public function __construct(private FleetRepository $fleets) {}

    public function handle(IsVehicleRegistered $query): bool
    {
        return $this->fleets->get($query->fleetId)->hasVehicle($query->plateNumber);
    }
}
