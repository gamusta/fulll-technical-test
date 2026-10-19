#!/bin/sh
# End-to-end check of the CLI against Postgres: each call is a separate process,
# so this proves state is persisted and reloaded between commands.
set -eu
cd "$(dirname "$0")/.."

fail() { echo "FAIL: $*" >&2; exit 1; }

ID=$(./fleet create smoke-user)
[ -n "$ID" ] || fail "create returned no fleet id"

./fleet register-vehicle "$ID" AA-123
./fleet register-vehicle "$ID" BB-456
./fleet localize-vehicle "$ID" AA-123 48.8 2.3
./fleet localize-vehicle "$ID" BB-456 45.7 4.8 170
./fleet localize-vehicle "$ID" BB-456 -33.9 -70.6 -12.5

# Full float precision must survive a reload, otherwise the "same location" rule is bypassed.
./fleet register-vehicle "$ID" CC-789
./fleet localize-vehicle "$ID" CC-789 48.123456789012345 2.123456789012345
if ./fleet localize-vehicle "$ID" CC-789 48.123456789012345 2.123456789012345 2>/dev/null; then
    fail "parking twice at the same precise location should fail"
fi

if ./fleet localize-vehicle "$ID" CC-789 1 2 1e309 2>/dev/null; then
    fail "an infinite altitude should be rejected"
fi

if ERROR=$(./fleet localize-vehicle "$ID" AA-123 48.8 2.3 2>&1); then
    fail "parking twice at the same location should fail"
fi
echo "$ERROR" | grep -q "already parked" || fail "unexpected error: $ERROR"

if ./fleet register-vehicle "$ID" BB-456 2>/dev/null; then
    fail "registering twice should fail (BB-456 must still be persisted)"
fi

echo "CLI smoke test passed"
