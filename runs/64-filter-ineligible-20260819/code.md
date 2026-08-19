# Coder report: 1-chest-compatibility-filter\n\n# Coder report: 1-chest-compatibility-filter

## Changed files
- `autoload/ProgressionManager.gd` — modified
- `scripts/progression/fire_tower.json` — modified
- `scripts/progression/global.json` — modified
- `scripts/progression/electric_tower.json` — modified
- `scripts/progression/balista_tower.json` — modified
- `scripts/progression/generic_tower.json` — modified
- `scripts/progression/ice_tower.json` — modified
- `scripts/progression/cannon_tower.json` — modified
- `scripts/progression/venom_tower.json` — modified
- `scripts/progression/floodgate_tower.json` — modified
- `scripts/progression/rocket_tower.json` — modified
- `tests/scenarios/chest_reward_compatibility.json` — new

## Criteria
- A full chest draw with no Fire tower placed and no Fire-tower unlock or perk available does not include fire_flashover. — Done
- A full chest draw after a Fire tower is placed includes fire_flashover while that reward remains otherwise eligible. — Done
- A full chest draw with no Fire tower placed still includes fire_flashover when a currently available perk or unlock can add a Fire tower. — Done
- Gold and Common rewards stay in chest generation under this compatibility filter, including fire_burn with no Fire tower placed. — Done
- After fire_burn is owned, fire_wildfire_spread is absent from a full chest draw with no Fire tower and present after a Fire tower is placed. — Done
- Unique rewards that declare no tower, element, or mechanic compatibility stay eligible regardless of placed towers. — Done
- When compatibility filtering removes every non-Gold/Common candidate, chest generation still offers a non-empty valid choice set that includes Gold and/or Common rewards. — Done
- Debug-build [PROGRESSION] log line per incompatible chest reward skip — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/chest_reward_compatibility.json` — exit code 0; `.gen/harness/chest_reward_compatibility/result.json` status=pass

## Notes
- See `/workspace/git-workspaces/poke-defense-godot/issue-filter-ineligible-chest-rewards/.gen/coder-reports/implementation.md` for full file list and gotchas.
\n\n# Coder report: 2-existing-chest-regression\n\n# Coder report: 2-existing-chest-regression

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
\n\n# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `autoload/ProgressionManager.gd` — modified
- `scripts/progression/fire_tower.json` — modified
- `scripts/progression/global.json` — modified
- `scripts/progression/electric_tower.json` — modified
- `scripts/progression/balista_tower.json` — modified
- `scripts/progression/generic_tower.json` — modified
- `scripts/progression/ice_tower.json` — modified
- `scripts/progression/cannon_tower.json` — modified
- `scripts/progression/venom_tower.json` — modified
- `scripts/progression/floodgate_tower.json` — modified
- `scripts/progression/rocket_tower.json` — modified
- `tests/scenarios/chest_reward_compatibility.json` — new
- `tests/scenarios/progression_chest_pool.json` — modified
- `tests/scenarios/fire_flashover_progression.json` — modified

## Criteria
- A full chest draw with no Fire tower placed and no Fire-tower unlock or perk available does not include fire_flashover. — Done
- A full chest draw after a Fire tower is placed includes fire_flashover while that reward remains otherwise eligible. — Done
- A full chest draw with no Fire tower placed still includes fire_flashover when a currently available perk or unlock can add a Fire tower. — Done
- Gold and Common rewards stay in chest generation under this compatibility filter, including fire_burn with no Fire tower placed. — Done
- After fire_burn is owned, fire_wildfire_spread is absent from a full chest draw with no Fire tower and present after a Fire tower is placed. — Done
- Unique rewards that declare no tower, element, or mechanic compatibility stay eligible regardless of placed towers. — Done
- When compatibility filtering removes every non-Gold/Common candidate, chest generation still offers a non-empty valid choice set that includes Gold and/or Common rewards. — Done
- Debug-build [PROGRESSION] log line per incompatible chest reward skip — Done
- A full chest draw remains non-empty, keeps tower_dmg while it is eligible, and omits a progression after that progression has been taken. — Done
- fire_flashover still starts unowned, applies to levels 1–3 with the existing flashover config values, and does not change fire_burn state. — Done

## Commands and results
- `godot --version` — exit code 0; 4.4.1.stable.official
- `godot --headless --path . --editor --quit-after 300` — exit code 0; import/parse gate completed
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/chest_reward_compatibility.json` — exit code 0; `.gen/harness/chest_reward_compatibility/result.json` status=pass
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/fire_flashover_progression.json` — exit code 0; `.gen/harness/fire_flashover_progression/result.json` status=pass
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_chest_pool.json` — exit code 0; `.gen/harness/progression_chest_pool/result.json` status=pass

## Notes
- Runner profile used: project=`godot-td` workspace=`poke-defense-godot/issue-filter-ineligible-chest-rewards` (request.md runner key).
- Compatibility is metadata-driven. Commons/Gold skip the filter. Uniques with no compatibility stay eligible (chest_quality).
- Placed coverage comes from `Game/Towers.get_towers()`. Fire map unlock is not coverage.
- `cold_and_hot.unlocks.towers = ["fire"]` is the grant path, gated on rocket_burn so it does not collide with the wildfire criterion.
- Stale `.gen/harness/_logs/<id>.out.log` can hide new skip lines; clear it before a log expectation rerun.
- progression_chest_pool seeded pins remesured after Unique filtering: chest awards traps_serrated_edges.
- Gold is the chest money card in ProgressionModal, not a progression type.
- Not committed. Worker left running for checker.
\n