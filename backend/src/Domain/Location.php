<?php

declare(strict_types=1);

namespace Fulll\Backend\Domain;

final readonly class Location
{
    public function __construct(
        public float $latitude,
        public float $longitude,
        public ?float $altitude = null,
    ) {
        if (!($latitude >= -90.0 && $latitude <= 90.0)) {
            throw new \InvalidArgumentException(sprintf('Latitude must be between -90 and 90, got %s.', $latitude));
        }

        if (!($longitude >= -180.0 && $longitude <= 180.0)) {
            throw new \InvalidArgumentException(sprintf('Longitude must be between -180 and 180, got %s.', $longitude));
        }
    }

    public function equals(self $other): bool
    {
        return $this->latitude === $other->latitude
            && $this->longitude === $other->longitude
            && $this->altitude === $other->altitude;
    }
}
