<?php

declare(strict_types=1);

namespace Fulll\Backend\App\Command;

use Fulll\Backend\Domain\Fleet;
use Fulll\Backend\Domain\FleetRepository;

final readonly class CreateFleetHandler
{
    public function __construct(private FleetRepository $fleets) {}

    public function handle(CreateFleet $command): string
    {
        $fleet = new Fleet(bin2hex(random_bytes(16)), $command->userId);
        $this->fleets->save($fleet);

        return $fleet->id();
    }
}
