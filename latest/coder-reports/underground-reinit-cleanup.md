# Coder report: underground-reinit-cleanup

## Changed files
- `scripts/game/UndergroundSystem.gd` — modified
- `scripts/game/Game.gd` — modified (keep `DamageGroup` in `_clear_world`)

## Criteria
- After Underground re-init, Underground has no leftover live child whose name starts with `Chest_Cave` from the previous init. — Done
- After Underground re-init, Underground has no leftover live child whose name starts with `Cave_` from the previous init. — Done
- After Underground re-init, Underground has no leftover live child whose name starts with `CaveDarkness_` from the previous init. — Done
- After Underground re-init, Underground still has a live `DamageGroup` child. — Done
- Debug-build [UNDERGROUND] log line per cleanup sweep naming freed children — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_cleanup_leaks_cave_nodes.json` — exit code 0; status=pass; leftover prefixes 0 after re-init; DamageGroup alive; `[UNDERGROUND] cleanup sweep freed:` includes `Chest_Cave1`, `Cave_1`, `CaveDarkness_1`

## Notes
- Keep-list is `DamageGroup` only. Recreates DamageGroup if it is missing or already queued.
- See `implementation.md` for the RED observation and runner coordinates.
