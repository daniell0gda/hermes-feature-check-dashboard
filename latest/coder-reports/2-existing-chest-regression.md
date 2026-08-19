# Coder report: 2-existing-chest-regression

## Changed files
- `tests/scenarios/progression_chest_pool.json` — modified
- `tests/scenarios/fire_flashover_progression.json` — modified

## Criteria
- A full chest draw remains non-empty, keeps tower_dmg while it is eligible, and omits a progression after that progression has been taken. — Done
- fire_flashover still starts unowned, applies to levels 1–3 with the existing flashover config values, and does not change fire_burn state. — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/fire_flashover_progression.json` — exit code 0; `.gen/harness/fire_flashover_progression/result.json` status=pass
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_chest_pool.json` — exit code 0; `.gen/harness/progression_chest_pool/result.json` status=pass

## Notes
- Chest-pool RNG pins remesured after Unique compatibility filtering: open_chest awards traps_serrated_edges; trailing 2-draw contains traps_serrated_edges.
- fire_flashover_progression chest check now spends Fire on map_4 instead of relying on flagged-exclusive miasma.
