# Coder report: implementation

## Changed files
- `scripts/game/CaveSystem.gd` — modified
- `scripts/utils/CaveUtils.gd` — modified
- `tests/scenarios/cave_discovery_chance.json` — modified

## Criteria
- When configured discovery chance is at least 1.0 and max caves is not reached, a cave is discovered on the first eligible check after the configured cooldown. — Done
- When configured discovery chance is 0.0, carving through multiple cooldown intervals discovers no caves. — Pending
- When discovery chance is 0.0 and spawner chance is 1.0, carving through multiple cooldown intervals still discovers no caves. — Pending
- When a discovery roll succeeds but no cave is created, the next discovery attempt does not require carving another full cooldown of tiles. — Done
- After the same cooldown, a later eligible check uses a lower discovery chance than the first eligible check once more tiles have been carved this run and at least one cave has already been discovered. — Done
- CAVE_SYSTEM_README documents that discovery chance starts relatively high and decreases with tiles carved this run and caves already discovered, and no longer says more carving raises the chance. — Done
- Debug-build [CAVE] log line per eligible discovery check (configured discovery chance, effective chance, tiles carved, caves found, result) — Done
- Debug-build [CAVE] log line per successful roll that does not create a cave (reason) — Done
- Debug-build [CAVE] log line per cave created after a successful roll (cave id, position) — Done
- Debug-build discovery logs name the discovery-chance field distinctly from the spawner-chance field. — Done

## Commands and results
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; import/typecheck passed
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_discovery_chance.json"]` — exit code 0; status=pass, all expectations passed
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_diversion_proof.json"]` — exit code 0; status=pass

## Notes
- The focused scenario initially carved outside map bounds and returned zero; its coordinates were corrected to reachable map coordinates.
- Effective chance remains 1.0 for the first configured 1.0 check, then decreases after caves/tiles accumulate.
- Failed placement schedules a retry on the next carve event without consuming another full cooldown.
- `HarnessValues.gd` and README changes pre-existed this revision and were not modified in this iteration.

## Verification
- Import/typecheck, focused harness, and full harness all passed through the approved runner.
- No dashboard events published.
