# Coder report: 1-cave-carved-path-torches

## Changed files
- `scripts/game/underground/TorchPlacer.gd` — modified: fill uncovered carved cells after wall placement
- `scripts/game/underground/TorchManager.gd` — modified: `count_unlit_carved_cells` + debug `[TORCH]` log
- `scripts/testing/HarnessValues.gd` — modified: `torch.count_near` and `torch.unlit_carved_in_cave`
- `tests/scenarios/cave_carved_path_torches.json` — new: focused scenario covering all six criteria

## Criteria
- After a discovered cave is confirmed open, that cave's carved path has at least one active torch. — Done
- After a later carve adds a new carved corridor connected to that same open cave, the new corridor has at least one active torch once torch placement has updated. — Done
- An open cave that already has lit carved path has no leftover dark carved corridor in the same cave; uncarved or intentionally dark rock stays unlit. — Done
- While a dangerous cave is pending confirmation, that cave's interior has zero active torches. — Done
- After a dangerous cave is declined, that cave's interior has zero active torches. — Done
- Debug-build [TORCH] log line per cave-path torch update — Done

## Commands and results
- `["godot", "--version"]` — exit code 0; `4.4.1.stable.official.49a5bc7b6`
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; registered TorchManager/HarnessValues
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_carved_path_torches.json"]` — exit code 0; `[Harness] status=pass exit=0`; `.gen/harness/cave_carved_path_torches/result.json` status `pass` at `2026-08-21T10:59:30`; expectations: pending false, count_in_cave 8, count_near 4, unlit_carved_in_cave 0, declined count_in_cave 0, log contains `[TORCH]`

## Notes
- RED for leftover dark corridor was `unlit_carved_in_cave` actual 23 before the placer fill pass.
- RED for `[TORCH]` was `log contains` fail (empty/stale out.log); after debug print, log expectation passed.
- Pending/declined zero-torch behavior was already implemented via `cave_locked_grid`; focused scenario now asserts it.
- HudTheme still warns that `res://textures/ui/hud/wood_panel.png` is missing; harness still runs.
- Visual/manual lighting not claimed from this headless run.
