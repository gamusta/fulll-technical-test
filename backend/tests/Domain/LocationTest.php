<?php

declare(strict_types=1);

namespace Fulll\Backend\Tests\Domain;

use Fulll\Backend\Domain\Location;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

final class LocationTest extends TestCase
{
    public function testEqualWhenAllCoordinatesMatch(): void
    {
        self::assertTrue((new Location(48.8, 2.3, 35.0))->equals(new Location(48.8, 2.3, 35.0)));
    }

    public function testUnknownAltitudeIsNotSeaLevel(): void
    {
        self::assertFalse((new Location(48.8, 2.3))->equals(new Location(48.8, 2.3, 0.0)));
    }

    /**
     * @return array<string, array{float, float}>
     */
    public static function outOfRange(): array
    {
        return [
            'latitude > 90' => [90.1, 0.0],
            'latitude < -90' => [-90.1, 0.0],
            'longitude > 180' => [0.0, 180.1],
            'longitude < -180' => [0.0, -180.1],
            'NaN' => [NAN, 0.0],
        ];
    }

    #[DataProvider('outOfRange')]
    public function testRejectsOutOfRangeCoordinates(float $latitude, float $longitude): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Location($latitude, $longitude);
    }

    /**
     * @return array<string, array{float}>
     */
    public static function nonFiniteAltitudes(): array
    {
        return [
            'NaN' => [NAN],
            '+INF' => [INF],
            '-INF' => [-INF],
        ];
    }

    #[DataProvider('nonFiniteAltitudes')]
    public function testRejectsNonFiniteAltitude(float $altitude): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new Location(48.8, 2.3, $altitude);
    }
}
