CREATE TABLE IF NOT EXISTS fleets (
    id TEXT PRIMARY KEY,
    user_id TEXT NOT NULL
);

CREATE TABLE IF NOT EXISTS fleet_vehicles (
    fleet_id TEXT NOT NULL REFERENCES fleets (id),
    plate_number TEXT NOT NULL,
    latitude DOUBLE PRECISION,
    longitude DOUBLE PRECISION,
    altitude DOUBLE PRECISION,
    PRIMARY KEY (fleet_id, plate_number),
    CHECK ((latitude IS NULL) = (longitude IS NULL))
);
