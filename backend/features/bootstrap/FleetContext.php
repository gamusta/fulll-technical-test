<?php

declare(strict_types=1);

namespace Fulll\Tests\Behat;

use Behat\Behat\Context\Context;
use Behat\Step\Given;
use Behat\Step\Then;
use Behat\Step\When;
use Fulll\Backend\App\Command\CreateFleet;
use Fulll\Backend\App\Command\CreateFleetHandler;
use Fulll\Backend\App\Command\ParkVehicle;
use Fulll\Backend\App\Command\ParkVehicleHandler;
use Fulll\Backend\App\Command\RegisterVehicle;
use Fulll\Backend\App\Command\RegisterVehicleHandler;
use Fulll\Backend\App\Query\GetVehicleLocation;
use Fulll\Backend\App\Query\GetVehicleLocationHandler;
use Fulll\Backend\App\Query\IsVehicleRegistered;
use Fulll\Backend\App\Query\IsVehicleRegisteredHandler;
use Fulll\Backend\Domain\FleetRepository;
use Fulll\Backend\Domain\Location;
use Fulll\Backend\Domain\VehicleAlreadyParkedAtLocation;
use Fulll\Backend\Domain\VehicleAlreadyRegistered;
use Fulll\Backend\Infra\InMemoryFleetRepository;

final class FleetContext implements Context
{
    private FleetRepository $fleets;
    private string $myFleetId;
    private string $otherFleetId;
    private string $plateNumber;
    private Location $location;
    private ?\Throwable $error = null;

    public function __construct()
    {
        $this->fleets = new InMemoryFleetRepository();
    }

    #[Given('my fleet')]
    public function myFleet(): void
    {
        $this->myFleetId = $this->createFleet('me');
    }

    #[Given('the fleet of another user')]
    public function theFleetOfAnotherUser(): void
    {
        $this->otherFleetId = $this->createFleet('another-user');
    }

    #[Given('a vehicle')]
    public function aVehicle(): void
    {
        $this->plateNumber = 'AB-123-CD';
    }

    #[Given('a location')]
    public function aLocation(): void
    {
        $this->location = new Location(48.8566, 2.3522);
    }

    #[Given('I have registered this vehicle into my fleet')]
    #[When('I register this vehicle into my fleet')]
    public function registerIntoMyFleet(): void
    {
        $this->register($this->myFleetId);
    }

    #[Given("this vehicle has been registered into the other user's fleet")]
    public function registerIntoOtherFleet(): void
    {
        $this->register($this->otherFleetId);
    }

    #[When('I try to register this vehicle into my fleet')]
    public function tryToRegisterIntoMyFleet(): void
    {
        try {
            $this->register($this->myFleetId);
        } catch (VehicleAlreadyRegistered $e) {
            $this->error = $e;
        }
    }

    #[Given('my vehicle has been parked into this location')]
    #[When('I park my vehicle at this location')]
    public function park(): void
    {
        (new ParkVehicleHandler($this->fleets))->handle(new ParkVehicle(
            $this->myFleetId,
            $this->plateNumber,
            $this->location->latitude,
            $this->location->longitude,
            $this->location->altitude,
        ));
    }

    #[When('I try to park my vehicle at this location')]
    public function tryToPark(): void
    {
        try {
            $this->park();
        } catch (VehicleAlreadyParkedAtLocation $e) {
            $this->error = $e;
        }
    }

    #[Then('this vehicle should be part of my vehicle fleet')]
    public function vehicleShouldBePartOfMyFleet(): void
    {
        $registered = (new IsVehicleRegisteredHandler($this->fleets))
            ->handle(new IsVehicleRegistered($this->myFleetId, $this->plateNumber));

        self::check($registered, 'Vehicle should be part of my fleet.');
    }

    #[Then('I should be informed this this vehicle has already been registered into my fleet')]
    public function informedAlreadyRegistered(): void
    {
        self::check($this->error instanceof VehicleAlreadyRegistered, 'Expected VehicleAlreadyRegistered.');
    }

    #[Then('the known location of my vehicle should verify this location')]
    public function knownLocationShouldVerifyThisLocation(): void
    {
        $location = (new GetVehicleLocationHandler($this->fleets))
            ->handle(new GetVehicleLocation($this->myFleetId, $this->plateNumber));

        self::check($location?->equals($this->location) === true, 'Known location does not match.');
    }

    #[Then('I should be informed that my vehicle is already parked at this location')]
    public function informedAlreadyParked(): void
    {
        self::check($this->error instanceof VehicleAlreadyParkedAtLocation, 'Expected VehicleAlreadyParkedAtLocation.');
    }

    private function createFleet(string $userId): string
    {
        return (new CreateFleetHandler($this->fleets))->handle(new CreateFleet($userId));
    }

    private function register(string $fleetId): void
    {
        (new RegisterVehicleHandler($this->fleets))->handle(new RegisterVehicle($fleetId, $this->plateNumber));
    }

    private static function check(bool $condition, string $message): void
    {
        if (!$condition) {
            throw new \RuntimeException($message);
        }
    }
}
