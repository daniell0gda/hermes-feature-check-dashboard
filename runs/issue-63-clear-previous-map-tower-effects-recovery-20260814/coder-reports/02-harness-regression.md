# C2 harness regression handoff

## Outcome

Implemented the deterministic Porter map-reload AgentHarness regression for issue #63. The scenario starts map A (`map_6`), places a Porter after creating a legal hole/exit, waits for a positive live Porter target/effect state, loads map B (`map_1`) through the existing `load_map` -> `debug_load_map` seam, checks old-generation telemetry after two waits, and proves a new map-B fire tower produces a fresh action.

## Changed files

- `tests/scenarios/issue_63_clear_previous_map_tower_effects.json`
  - Named timeline/checkpoints: `pre_reload_porter_action`, `post_reload_porter_quiet`, `issue_63_map_reload_boundary`, `post_reload_porter_quiet_repeated`, `map_b_fresh_fire_action`.
  - Asserts Porter active before reload; Porter target/shot/launch/impact/damage counters are zero after both post-reload waits; map generation advanced; map-B fire target action is positive.
- `scripts/testing/AgentHarness.gd`
  - Adds the narrow `telemetry_checkpoint` action and named checkpoint read API. It records map generation, Porter active state, and typed action/projectile/effect counters for Porter, generic, and fire towers without changing production gameplay.
- `scripts/testing/HarnessValues.gd`
  - Adds `harness` value source and read-only `tower.porter_active` observable for checkpoint assertions.

No gameplay/effect/tower production files, plan file, or other cluster artifacts were edited.

## Exact verification

1. Import/editor preflight through the approved runner:

```text
godot --headless --path . --editor --quit-after 300
```

Result: runner `success=true`, exit `0`, Godot `4.4.1.stable.official.49a5bc7b6`. Existing project warnings were reported (missing UID recreation and pre-existing resource/diagnostic warnings).

2. Scenario JSON validation through the approved runner:

```text
python3 -c "import json; json.load(open('tests/scenarios/issue_63_clear_previous_map_tower_effects.json')); print('scenario JSON valid')"
```

Result: exit `0`, `scenario JSON valid`.

3. Focused scenario through the approved runner:

```text
godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json
```

Result: runner `success=true`, exit `0`, duration `10204ms`; harness result `status=pass`, no failed expectations. Checkpoint evidence from `.gen/harness/issue_63_clear_previous_map_tower_effects/result.json`:

- pre reload: `map_generation=1`, `porter_active=true`;
- post reload: `map_generation=2`, `porter_active=false`, Porter target/shot/launch/impact/damage all `0`;
- repeated post-reload checkpoint: same zero Porter counters and `porter_active=false`;
- map B: fire `target_fire=1`, `shot_fire=1`, `launch_fire=1`, proving a fresh map-B action;
- named screenshot `issue_63_map_reload_boundary`: recorded as expected `headless` skip.

The successful run still emits known engine shutdown/resource-leak diagnostics and existing UI/resource warnings; these did not alter the harness pass result. Windowed screenshot inspection remains C3 responsibility.

No dashboard events were published.
