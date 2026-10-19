<?php

declare(strict_types=1);

namespace Fulll\Backend\App\Query;

use Fulll\Backend\Domain\FleetRepository;
use Fulll\Backend\Domain\Location;

final readonly class GetVehicleLocationHandler
{
    public function __construct(private FleetRepository $fleets) {}

    public function handle(GetVehicleLocation $query): ?Location
    {
        return $this->fleets->get($query->fleetId)->locationOf($query->plateNumber);
    }
}
