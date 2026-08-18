# Coder report: harness-chest-reinit-scenario

## Changed files
- `scripts/testing/HarnessValues.gd` — modified
- `scripts/game/CaveSystem.gd` — modified
- `scripts/game/UndergroundSystem.gd` — modified (`force_reinit`)
- `tests/scenarios/underground_cleanup_leaks_cave_nodes.json` — new

## Criteria
- The harness can read the live `Chest_Cave*` child count under Underground. — Done
- A focused harness scenario creates a cave chest fixture, forces map re-init (load_map twice or equivalent Underground re-init), and ends with exactly one live `Chest_Cave*` child. — Done
- After the chest-fixture re-init, a tower can still be placed on the loaded map. — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_cleanup_leaks_cave_nodes.json` — exit code 0; status=pass; `cave.chest_cave_count==1`; `stats.net_cost_by_type.generic==20`; `place_tower` ok

## Notes
- Fixture: `CaveSystem.force_create_cave_chest_fixture()` creates `Chest_Cave1`, `Cave_1`, `CaveDarkness_1`.
- Re-init seam: `UndergroundSystem.force_reinit()` because a second `load_map` goes through `_clear_world` and hides the `_cleanup` leak.
- Live counts ignore `is_queued_for_deletion()`.
