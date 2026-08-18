# Coder report: implementation

## Changed files
- `scripts/game/UndergroundSystem.gd` — modified
- `scripts/game/Game.gd` — modified
- `scripts/game/CaveSystem.gd` — modified
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/underground_cleanup_leaks_cave_nodes.json` — new

## Criteria
- After Underground re-init, Underground has no leftover live child whose name starts with `Chest_Cave` from the previous init. — Done
- After Underground re-init, Underground has no leftover live child whose name starts with `Cave_` from the previous init. — Done
- After Underground re-init, Underground has no leftover live child whose name starts with `CaveDarkness_` from the previous init. — Done
- After Underground re-init, Underground still has a live `DamageGroup` child. — Done
- Debug-build [UNDERGROUND] log line per cleanup sweep naming freed children — Done
- The harness can read the live `Chest_Cave*` child count under Underground. — Done
- A focused harness scenario creates a cave chest fixture, forces map re-init (load_map twice or equivalent Underground re-init), and ends with exactly one live `Chest_Cave*` child. — Done
- After the chest-fixture re-init, a tower can still be placed on the loaded map. — Done

## Commands and results
- `godot --version` (project=godot-td workspace=poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes) — exit code 0; 4.4.1.stable.official.49a5bc7b6
- `godot --headless --path . --editor --quit-after 300` — exit code 0; import/parse completed
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_cleanup_leaks_cave_nodes.json` — exit code 1 (RED); leftover `chest_cave_count` stayed 1 after `force_reinit` before keep-list cleanup
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_cleanup_leaks_cave_nodes.json` — exit code 0; `.gen/harness/underground_cleanup_leaks_cave_nodes/result.json` status=pass; all 5 expectations passed

## Notes
- `load_map` alone cannot observe the `_cleanup()` leak: `Game._clear_world()` already queue_frees most Underground children. The focused scenario uses `UndergroundSystem.force_reinit()` (equivalent Underground re-init).
- `_cleanup()` now frees any Underground child not named `DamageGroup`. Debug builds print `[UNDERGROUND] cleanup sweep freed: ...`.
- `Game._clear_world()` also keeps `DamageGroup` so the first `load_map` does not kill the node created in `Game._ready`.
- Harness `cave` fields: `chest_cave_count`, `cave_marker_count`, `cave_darkness_count`, `damage_group_alive`.
- Full suite (`smoke_placement`) not run here; focused verification only.
- Did not publish dashboard events.
