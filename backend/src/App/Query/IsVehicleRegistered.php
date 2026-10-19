<?php

declare(strict_types=1);

namespace Fulll\Backend\App\Query;

final readonly class IsVehicleRegistered
{
    public function __construct(
        public string $fleetId,
        public string $plateNumber,
    ) {}
}
