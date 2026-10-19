<?php

declare(strict_types=1);

namespace Fulll\Backend\App\Command;

use Fulll\Backend\Domain\FleetRepository;
use Fulll\Backend\Domain\Location;

final readonly class ParkVehicleHandler
{
    public function __construct(private FleetRepository $fleets) {}

    public function handle(ParkVehicle $command): void
    {
        $fleet = $this->fleets->get($command->fleetId);
        $fleet->parkVehicle(
            $command->plateNumber,
            new Location($command->latitude, $command->longitude, $command->altitude),
        );
        $this->fleets->save($fleet);
    }
}
