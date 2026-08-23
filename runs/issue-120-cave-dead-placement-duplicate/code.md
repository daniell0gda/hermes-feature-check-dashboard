# Coder report: 1-remove-dead-cave-placement-finder\n\n# Coder report: 1-remove-dead-cave-placement-finder

## Changed files
- `scripts/game/CaveSystem.gd` — modified (removed dead `_find_suitable_cave_position`, lines ~270–298)

## Criteria
- Case-sensitive search for `_find_suitable_cave_position` under `scripts/` returns zero matches — Done
- Only runtime placement lookup remains `CaveUtils.find_suitable_cave_position`; no second same-named routine — Done (`grep -rn "find_suitable_cave_position" scripts/` shows only the CaveUtils definition + the single call site at CaveSystem.gd:245)
- `cave_discovery_chance` scenario passes — Done
- `cave_discovery_long_carve` scenario passes — Pending (pre-existing failure, see Notes)
- `cave_discovery_pending_placement` scenario passes — Done
- Headless editor parse gate completes without script parse or class-cache errors — Done

## Commands and results
- `grep -rn "_find_suitable_cave_position" scripts/` (host-side read-only check) — exit 1 / no matches after removal
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_discovery_chance.json` via run_project_cmd — exit code 0; `[Harness] status=pass exit=0`; result at `.gen/harness/cave_discovery_chance/result.json`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_discovery_pending_placement.json` via run_project_cmd — exit code 0; `[Harness] status=pass exit=0`; result at `.gen/harness/cave_discovery_pending_placement/result.json`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_discovery_long_carve.json` via run_project_cmd — exit code 1; status=fail; expectation `carved_tiles >= 1000.0` got actual 961 (all other cave expectations pass)
- Baseline check: stashed the change and re-ran `cave_discovery_long_carve` on unmodified HEAD — identical failure (carved_tiles=961, deterministic seeded run, seed 20260820). Change restored afterwards.
- `godot --headless --path . --editor --quit-after 300` via run_project_cmd — exit code 0; zero script parse errors and zero class-cache errors; only pre-existing asset import noise (glb import failures for tower models, missing icon png imports)

## Notes
- Removal is pure dead code: `_find_suitable_cave_position` had no callers; the live path already uses `CaveUtils.find_suitable_cave_position` (CaveSystem.gd line 245).
- The focused `sh -c for ... done` wrapper was rejected by the runner profile allowlist ("sh" not allowed); each scenario was invoked individually as a tokenized godot command with identical semantics.
- Pre-existing failure to flag to the tester: `cave_discovery_long_carve` expects `carved_tiles >= 1000` but deterministically yields 961 on both modified and unmodified code. Not caused by this change. Possibly map_6 carve area is smaller than the scenario threshold assumes, or carve_rectangle overlap counting changed upstream.
- Harness logs also land under `.gen/harness/_logs/`.
\n