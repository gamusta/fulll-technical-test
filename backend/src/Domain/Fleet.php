<?php

declare(strict_types=1);

namespace Fulll\Backend\Domain;

final class Fleet
{
    /**
     * @param array<string, Location|null> $vehicles plate number => last known location (null = not parked yet)
     */
    public function __construct(
        private readonly string $id,
        private readonly string $userId,
        private array $vehicles = [],
    ) {}

    public function id(): string
    {
        return $this->id;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function registerVehicle(string $plateNumber): void
    {
        if ($this->hasVehicle($plateNumber)) {
            throw new VehicleAlreadyRegistered(sprintf('Vehicle "%s" is already registered in fleet "%s".', $plateNumber, $this->id));
        }

        $this->vehicles[$plateNumber] = null;
    }

    public function parkVehicle(string $plateNumber, Location $location): void
    {
        if ($this->locationOf($plateNumber)?->equals($location) === true) {
            throw new VehicleAlreadyParkedAtLocation(sprintf('Vehicle "%s" is already parked at this location.', $plateNumber));
        }

        $this->vehicles[$plateNumber] = $location;
    }

    public function hasVehicle(string $plateNumber): bool
    {
        return array_key_exists($plateNumber, $this->vehicles);
    }

    public function locationOf(string $plateNumber): ?Location
    {
        if (!$this->hasVehicle($plateNumber)) {
            throw new VehicleNotRegistered(sprintf('Vehicle "%s" is not registered in fleet "%s".', $plateNumber, $this->id));
        }

        return $this->vehicles[$plateNumber];
    }

    /**
     * @return array<string, Location|null>
     */
    public function vehicles(): array
    {
        return $this->vehicles;
    }
}
