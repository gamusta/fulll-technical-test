<?php

declare(strict_types=1);

namespace Fulll\Backend\Domain;

interface FleetRepository
{
    /**
     * @throws FleetNotFound
     */
    public function get(string $id): Fleet;

    public function save(Fleet $fleet): void;
}
