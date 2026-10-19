<?php

declare(strict_types=1);

namespace Fulll\Backend\Infra;

use Fulll\Backend\Domain\Fleet;
use Fulll\Backend\Domain\FleetNotFound;
use Fulll\Backend\Domain\FleetRepository;

final class InMemoryFleetRepository implements FleetRepository
{
    /** @var array<string, Fleet> */
    private array $fleets = [];

    public function get(string $id): Fleet
    {
        if (!isset($this->fleets[$id])) {
            throw new FleetNotFound(sprintf('Fleet "%s" not found.', $id));
        }

        return clone $this->fleets[$id];
    }

    public function save(Fleet $fleet): void
    {
        $this->fleets[$fleet->id()] = clone $fleet;
    }
}
