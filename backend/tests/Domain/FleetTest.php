<?php

declare(strict_types=1);

namespace Fulll\Backend\Tests\Domain;

use Fulll\Backend\Domain\Fleet;
use Fulll\Backend\Domain\Location;
use Fulll\Backend\Domain\VehicleNotRegistered;
use PHPUnit\Framework\TestCase;

final class FleetTest extends TestCase
{
    public function testRegisteredVehicleHasNoLocationUntilParked(): void
    {
        $fleet = new Fleet('fleet-1', 'user-1');
        $fleet->registerVehicle('AA-123');

        self::assertTrue($fleet->hasVehicle('AA-123'));
        self::assertNull($fleet->locationOf('AA-123'));
    }

    public function testCannotParkUnregisteredVehicle(): void
    {
        $this->expectException(VehicleNotRegistered::class);

        (new Fleet('fleet-1', 'user-1'))->parkVehicle('AA-123', new Location(48.8, 2.3));
    }

    public function testVehicleCanBeParkedAgainAtAnotherLocation(): void
    {
        $fleet = new Fleet('fleet-1', 'user-1');
        $fleet->registerVehicle('AA-123');
        $fleet->parkVehicle('AA-123', new Location(48.8, 2.3));
        $fleet->parkVehicle('AA-123', new Location(45.7, 4.8));

        self::assertEquals(new Location(45.7, 4.8), $fleet->locationOf('AA-123'));
    }

    public function testLocationIsTrackedPerFleet(): void
    {
        $mine = new Fleet('fleet-1', 'user-1');
        $other = new Fleet('fleet-2', 'user-2');
        $mine->registerVehicle('AA-123');
        $other->registerVehicle('AA-123');

        $mine->parkVehicle('AA-123', new Location(48.8, 2.3));

        self::assertNull($other->locationOf('AA-123'));
    }
}
