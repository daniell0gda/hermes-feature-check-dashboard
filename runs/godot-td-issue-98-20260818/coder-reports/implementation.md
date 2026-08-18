# Coder report: implementation

## Changed files
- `scripts/game/CaveSystem.gd` — modified
- `scripts/utils/CaveUtils.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `CAVE_SYSTEM_README.md` — modified
- `tests/scenarios/cave_discovery_chance.json` — new

## Criteria
- When configured discovery chance is at least 1.0 and max caves is not reached, a cave is discovered on the first eligible check after the configured cooldown. — Pending
- When configured discovery chance is 0.0, carving through multiple cooldown intervals discovers no caves. — Pending
- When discovery chance is 0.0 and spawner chance is 1.0, carving through multiple cooldown intervals still discovers no caves. — Pending
- When a discovery roll succeeds but no cave is created, the next discovery attempt does not require carving another full cooldown of tiles. — Pending
- After the same cooldown, a later eligible check uses a lower discovery chance than the first eligible check once more tiles have been carved this run and at least one cave has already been discovered. — Pending
- CAVE_SYSTEM_README documents that discovery chance starts relatively high and decreases with tiles carved this run and caves already discovered, and no longer says more carving raises the chance. — Done
- Debug-build [CAVE] log line per eligible discovery check (configured discovery chance, effective chance, tiles carved, caves found, result) — Pending
- Debug-build [CAVE] log line per successful roll that does not create a cave (reason) — Pending
- Debug-build [CAVE] log line per cave created after a successful roll (cave id, position) — Pending
- Debug-build discovery logs name the discovery-chance field distinctly from the spawner-chance field. — Pending

## Commands and results
- `["godot", "--version"]` — exit code 0; Godot 4.4.1
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; import/typecheck completed
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cave_discovery_chance.json"]` — exit code 1; harness run reached scenario but failed (output truncated by runner)
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/underground_diversion_proof.json"]` — exit code 1; full scenario failed (output truncated by runner)

## Notes
- Initial focused run was blocked by missing scenario; added the planned scenario. Current failure needs checker review with complete runner output/harness evidence.
- No status.md/changes.md existed in the assigned `.gen` directory, so no status classification or journal entry was written.
- Discovery cooldown overflow is preserved with a loop; effective chance is distinct from spawner chance and debug logs are prefixed `[CAVE]`.

## Verification
- Import/typecheck gate passed.
- Focused and full gameplay verification did not pass.
