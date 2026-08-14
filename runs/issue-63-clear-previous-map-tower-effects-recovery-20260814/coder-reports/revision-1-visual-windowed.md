# Revision 1 — visual windowed evidence

## Scope
Moved the screenshot checkpoint after Map-B fire tower placement and positive fire activity, then added a narrow testing-only `set_debug_map` action to synchronize the existing debug selector to `map_1` for visible map identity. No production teardown changes.

## Changed files
- `scripts/testing/HarnessActions.gd`
- `tests/scenarios/issue_63_clear_previous_map_tower_effects.json`

## Verification
- Focused headless runner: exit `0`, timed out `false`, harness `status=pass`.
- Immutable headless result retained at `.gen/harness/issue_63_clear_previous_map_tower_effects/revision-1-headless/result.json` (SHA-256 `906ee87d3beb28bc8c6d459fb8e9f82ac47d2625143a00ab77153d6b73628b4c`).
- Windowed OpenGL-compatible runner: exit `0`, timed out `false`, harness `status=pass`.
- Fresh PNG inspected at `.gen/harness/issue_63_clear_previous_map_tower_effects/shots/issue_63_map_reload_boundary.png`; it shows Map 1, an active fire tower, and no stale Porter VFX.

## Retained versus temporary

Retained the checkpoint relocation and selector synchronization because they directly address the prior idle Map-6 screenshot. No temporary production probes were added.

## Stop condition

Satisfied: fresh windowed evidence now visibly targets post-reload Map B and fresh tower activity.
