# C2 — Porter reproduction and AgentHarness regression

- **parallel:** false
- **depends_on:** `01-gameplay-teardown`
- **blocked_by:** C1's final cleanup/telemetry contract
- **owns:** `tests/scenarios/issue_63_clear_previous_map_tower_effects.json` and, only if required by the established contract, the smallest files under `scripts/testing/` needed to expose an observable action-level field.
- **forbidden overlap:** do not edit gameplay/effect/tower source files, `.gen/plan.md`, or other clusters.

## Work

1. Start map A with deterministic seed and enough money; create the required hole/exit and place a Porter plus at least one ordinary effect-producing tower/control tower.
2. Trigger a wave and wait until a pre-reload checkpoint proves Porter activity exists: target/charge or dissolve-start event, target id/generation, and any relevant effect/projectile/action counters. Do not use tower count alone.
3. Call the harness `load_map` action to switch to map B through `debug_load_map`, wait longer than the Porter dissolve/timer window, and record a post-reload cleanup checkpoint.
4. Assert exact cleanup: no old tower instance/target/effect/projectile/tween remains; stale tower/effect action counters do not increase after reload; no old attribution applies damage/status/teleport in map B. Include a repeated wait/checkpoint to prove the result is stable, not a timing coincidence.
5. Place a new map-B control tower (and Porter if placement permits), trigger a fresh wave, and assert a new-generation action occurs. This proves the game still simulates and distinguishes old activity from new-map activity.
6. Add a named screenshot action at the visual boundary for C3. Keep headless behavior valid when the screenshot action reports its expected headless skip.

## Acceptance evidence

- Scenario notes document the known failure reproduction and exact map-A/map-B sequence.
- Pre-reload checkpoint contains a positive Porter action/effect delta.
- Post-reload checkpoint contains generation/instance attribution and zero stale deltas after the wait; a final `status=pass` without these records is insufficient.
- Map-B control checkpoint contains a positive fresh action with a different generation/instance.
- Scenario uses immutable checkpoint labels and does not overwrite prior evidence.

## Schema gap rule

If existing `HarnessValues`/`StatsManager` cannot expose a required action-level distinction, add only the smallest typed read-only observable in `scripts/testing/` and document the field in the scenario notes. Do not infer stale behavior from aggregate damage or zero counts. If the schema cannot be made sufficient without production changes, return the concrete missing field/action to C1 rather than overlapping ownership.

## Required report

Record the scenario path, timeline/checkpoint labels, exact assertions, and actual focused run result in the assigned coder report. Do not publish dashboard events.

## Status

Planning only; no scenario or harness files changed.
