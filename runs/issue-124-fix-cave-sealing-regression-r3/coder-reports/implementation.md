# Coder report: implementation (iteration 2, 2026-08-22)

## Changed files
- `scripts/testing/HarnessScenario.gd` — mod: new optional scenario flag `suppress_rng_cave_discovery` (parsed, documented in schema header)
- `scripts/testing/AgentHarness.gd` — mod: `suppress_rng_cave_discovery()` accessor exposing the flag to game code
- `scripts/game/CaveSystem.gd` — mod: `_rng_discovery_suppressed()` helper; `on_carving_completed` skips the pending-roll placement and cooldown discovery checks while suppressed (logs `[CAVE] discovery suppressed` per cooldown in debug builds); `prepare_for_save` skips the save-time forced discovery flush while suppressed
- `tests/scenarios/cave_pending_seals_entrance_instantly.json` — mod: one added line `"suppress_rng_cave_discovery": true`. No expectation, wait_for_condition, threshold, timeline step, or note was removed or changed.

Torch work from iteration 1 unchanged: `TorchPlacer.gd`, `TorchManager.gd`, `Torch.gd`, `Game.gd`, `HarnessValues.gd`, `tests/scenarios/cave_carved_path_torches.json`.

## Criteria (cluster 4 focus; 1–3 re-verified)
- No cave other than fixture 9003 discovered/carved/locked/sealed before first route assertion — Done: with suppression, every cooldown logs `[CAVE] discovery suppressed`; only cave 9003 exists in the run (`Current GameState: paused, discovered caves: 1` at pending, and the only post-confirm discovery is none — the earlier iteration-2 draft leaked an RNG cave via the save-time flush, fixed by also gating `prepare_for_save`).
- Initial `has_route_from == true` deterministic — Done: 3 consecutive passes (plus the first fixed run = 4 total passes this iteration), same seed 1.
- Pending question ⇒ `has_route_from == false` while pending — Done (action index 7 met).
- 1s after sealing, cave 9003 `count_in_cave == 0` — Done (index 9 met).
- "yes" restores exact hole→exit route (distance 8.0, 17 waypoints, same as pre-seal) and lighting restored (`[TORCH] cave-path update active=50`) — Done.
- Original assertions unchanged — Done: only the flag line added; diff is +1 line in the JSON.

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json` — exit 0, `status=pass`, run 3 consecutive times after the fix (plus one earlier pass before the save-flush leak was closed). Raw stdout scanned: no `SCRIPT ERROR`, no `Invalid call`, no torch/cave-related `Parse Error`. Only pre-existing HudTheme missing-texture noise (identical on clean HEAD) and benign engine exit-time leak notices.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_carved_path_torches.json` — exit 0, `status=pass`; `[TORCH]` recomputes active=29 → 148 → 154 → 142; declines 9102/9103 dark.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/declined_cave_torches_extinguish.json` — exit 0, `status=pass`.
- `godot --headless --path . --editor --quit-after 300` — exit 0; clean import, no script errors.

## Notes
- Root cause: map_9 `caves.spawn.chance = 0.8` + seed 1 let RNG discoveries carve/seal over the hole↔exit corridor before the scenario's first route assertion; additionally, even with cooldown rolls suppressed, `SaveManager.save_game_progress → CaveSystem.prepare_for_save` force-flushed a discovery roll mid-run. Both entry points are now gated by the scenario flag.
- Fix is at the product/test-fixture boundary (harness scenario opt-in flag honored by CaveSystem), per the cluster's guidance; no scenario assertion touched; map_9.json untouched (other scenarios relying on RNG discovery, e.g. carve_stops_at_discovered_cave, are unaffected because the flag defaults to false).
- Tester handoff: windowed-run screenshot criterion (cluster 3) remains with the manual tester; all headless commands green.
