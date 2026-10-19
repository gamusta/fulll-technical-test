<?php

declare(strict_types=1);

namespace Fulll\Backend\App\Command;

use Fulll\Backend\Domain\FleetRepository;

final readonly class RegisterVehicleHandler
{
    public function __construct(private FleetRepository $fleets) {}

    public function handle(RegisterVehicle $command): void
    {
        $fleet = $this->fleets->get($command->fleetId);
        $fleet->registerVehicle($command->plateNumber);
        $this->fleets->save($fleet);
    }
}
