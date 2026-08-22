# Coder report: 1-cave-carved-path-torches\n\n# Coder report: 1-cave-carved-path-torches

See `implementation.md` in this directory for the same cluster results.

## Changed files
- none this iteration (workspace already implemented; re-verified)

## Criteria
- While a dangerous cave is pending confirmation, that cave's interior has zero active torches, including when its room overlaps already-carved path. — Done
- After a discovered cave is confirmed open, that cave's carved path has at least one active torch. — Done
- After the connected 2-by-18 and 18-by-2 cross carve on that confirmed-open cave, sampled points along the full length of the north, south, east, and west arms at about 2-unit spacing each have at least one active torch within 2.5 world units on XZ. — Done
- After a later carve adds a new corridor connected to that same open cave, sampled points along the full length of the new corridor at about 2-unit spacing each have at least one active torch within 2.5 world units on XZ. — Done
- After a dangerous cave is declined, that cave's interior has zero active torches, including when its room overlaps already-carved path. — Done
- An isolated declined dangerous cave with no overlapping later corridor carve has zero active interior torches. — Done
- Debug-build [TORCH] log line per cave-path torch update — Done

## Commands and results
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_carved_path_torches.json"]` — exit code 0; result.json status `pass` at `2026-08-21T17:01:56`
\n\n# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/game/underground/TorchManager.gd` — removed torch-position decimation; `MAX_TORCHES` raised 100→1000 as a sanity ceiling only, pool expands on demand.

## Criteria
- Confirmed-open cave full carved network lit (2-unit samples, 2.5 XZ radius on both cross arms) — Done
- New connected corridor lit along its full extent — Done
- Zero unlit carved cells in the open-cave network (`unlit_carved_in_cave == 0`) — Done
- Pending dangerous cave interior has zero active torches — Done
- Declined dangerous cave interior has zero active torches (regression cave 9102) — Done
- Torch pool does not cap coverage (no decimation; active=148 after cross carve, 154 after later corridor) — Done
- Debug-build `[TORCH]` log line per cave-path update with active count — Done

## Commands and results
- `[godot --version]` via project runner — exit 0; `4.4.1.stable.official.49a5bc7b6`
- Focused: `[godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_carved_path_torches.json]` — exit 0; `[Harness] status=pass exit=0`; all 7 expectations pass (count_in_cave 9101 actual 10 ≥ 1; count_near samples ok; unlit_carved_in_cave 0; 9102/9103 counts 0; out.log contains `[TORCH]`); result at `.gen/harness/cave_carved_path_torches/result.json`.
- Full: `[... --harness=res://tests/scenarios/declined_cave_torches_extinguish.json]` — exit 0; `[Harness] status=pass exit=0`; declined cave extinguished.
- Typecheck/build: `[godot --headless --path . --editor --quit-after 300]` — exit 0; clean editor import/scan.

## Notes
- This iteration's only production change: removed `optimize_torch_placement` decimation so large carves are never silently thinned. Cross-carve now logs `[TORCH] cave-path update active=148` (was capped at 100). Resolves quality-note `max-torches-decimation`.
- Pre-existing HudTheme `wood_panel.png` missing-texture warnings still print; unrelated, harness passes.
- Headless screenshots skipped by harness design; visual/manual lighting check remains for the tester per `.gen/ui_scenario.md`.
\n