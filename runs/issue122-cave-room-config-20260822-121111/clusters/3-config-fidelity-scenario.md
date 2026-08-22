# Cluster 3: config-fidelity-scenario

- Files: `scripts/testing/HarnessValues.gd`, `tests/scenarios/cave_room_config_fidelity.json`
- Dependencies: 1
- Parallel: false

New focused scenario proving Done-when #4: the loaded cave config matches what
the map file says, exactly, with no silent rounding. The harness `cave` value
source currently exposes discovery/count fields only, so it must additionally
expose the loaded room values (minRadius / maxRadius / minSpacing) from the
live cave configuration; assertions compare each against the fractional number
written in the chosen stock map (map_6: minRadius 1.5, maxRadius 3.0,
minSpacing 2.5). Scenario result is read from
`.gen/harness/cave_room_config_fidelity/result.json`.

## Acceptance criteria

- A focused headless cave scenario loads a map whose file declares fractional room values and asserts, through harness-readable state, that each loaded value (minRadius, maxRadius, minSpacing) equals the exact fractional number in the map file; the scenario finishes with `status: pass`, exit code 0, and every expectation green.
- The same scenario's raw engine stdout contains no `Parse Error` lines and no new resource-load failures attributable to the changed files (baseline icon-import noise excluded).

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_room_config_fidelity.json"]`
- Full test: `["bash", "-lc", "for s in carve_stops_at_discovered_cave cave_decline_seals_reveal_unseals cave_discovery_chance cave_discovery_long_carve cave_discovery_pending_placement cave_pending_seals_entrance_instantly cave_reveal_only_unseals_carved_blocks declined_cave_torches_extinguish; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "60"]`
