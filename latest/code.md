# Coder report: 1-remove-dead-cave-placement-finder\n\n# Coder report: 1-remove-dead-cave-placement-finder

## Changed files
- `scripts/game/CaveSystem.gd` — mod (carried from iteration 1; already on disk, unchanged this revision)
- `tests/scenarios/cave_discovery_long_carve.json` — mod (this revision)

## Criteria
- `_find_suitable_cave_position` search under scripts/ returns zero matches — Done
- Only runtime placement lookup is `CaveUtils.find_suitable_cave_position` — Done
- `cave_discovery_chance` scenario passes fresh run — Done
- `cave_discovery_long_carve` scenario passes fresh run — Done
- `cave_discovery_pending_placement` scenario passes fresh run — Done
- Headless editor parse gate clean — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_discovery_chance.json` — exit 0; `[Harness] status=pass exit=0`; all 5 expectations pass. Result `.gen/harness/cave_discovery_chance/result.json`.
- same invocation for `cave_discovery_long_carve.json` — exit 0; `[Harness] status=pass exit=0`; all 5 expectations pass, carved_tiles actual = 1600 (>= 1000). Result `.gen/harness/cave_discovery_long_carve/result.json`.
- same invocation for `cave_discovery_pending_placement.json` — exit 0; `[Harness] status=pass exit=0`. Result `.gen/harness/cave_discovery_pending_placement/result.json`.
- `godot --headless --path . --editor --quit-after 300` — exit 0; no script parse or class-cache errors.
- Preflight/postflight `grep -rn "_find_suitable_cave_position" scripts/` — no matches.

## Notes
Root cause of the previously failing `cave_discovery_long_carve`: the scenario carved 5x5 rects centred at +/-7.5 world units, whose +X/+Z edge is exactly 10.0. `UndergroundUtils.world_to_grid` maps that to grid index 40 on map_6's 40x40 grid (valid indices 0-39), so `UndergroundSystem._carve_area_exceeds_grid` rejected every rect touching the positive edges — only 961 of 1600 tiles were carved. Deterministic at seed 20260820 and identical on unmodified HEAD (confirmed by iteration-1 checker), so it was a scenario geometry bug, not a code bug.

Fix: nudged edge rect centres from ±7.5 to ±7.45 (and adjacent centres to ±2.483 / ±2.483 so tiles stay aligned); all four 4x4 sweeps now cover every cell of the 40x40 grid with in-grid carves. The >=1000 threshold was kept as-is — the scenario now genuinely carves the whole grid (1600/1600). A note documenting the geometry rationale was appended to the scenario's `notes`.

Gotchas for tester:
- Runner rejects `sh` wrappers; each harness scenario must be invoked individually as a tokenized godot command (same semantics).
- The scenario JSON keeps its original one-step-per-line formatting; diff shows only coordinate changes plus one added note line.
- Pre-existing asset-import warnings (invalid UIDs, missing GLBs) are noise present on HEAD too.
\n