# Acceptance Plan: elemental-attunement

manual_testing: required

## Verification

- Focused test: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/elemental_attunement.json"])`
- Full test: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/floodgate_saltwater_purge.json"])`
- Typecheck/build: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","--editor","--quit-after","300"])`

## Clusters

1. attunement-perk-and-effectiveness — files: `scripts/progression/global.json`, `scripts/config/Balance.gd`, `autoload/ProgressionManager.gd` — depends on: none
- A new Unique progression named `elemental_attunement` exists in the global progression pool, is eligible for chest/cave draws under the same rules as other Uniques, and reaches level 1 after being applied through the progression manager.
- Selecting the fire attunement makes fire-attributed damage resolve as super-effective (2.0x) against Water-typed enemies, while fire damage against Fire-typed enemies keeps its 0.5x self-resistance.
- Selecting the water attunement makes water-attributed damage resolve as super-effective (2.0x) against Water-typed enemies, while water damage against Water-typed enemies keeps its 0.5x self-resistance.
- Selecting the electric attunement makes electric-attributed damage resolve as super-effective (2.0x) against Electric-typed enemies, while electric damage against Electric-typed enemies keeps its 0.5x self-resistance.
- With no attunement selected, every attacker/defender pair in the type effectiveness table resolves exactly as before the change (fire/fire, water/water, electric/electric all stay 0.5x; no other entry shifts).
- The attunement perk can be selected from the player-facing progression pick flow (it appears as a choosable option and choosing it applies the perk), matching how existing Unique perks are presented.
- Debug-build `[ELEMENTAL_ATTUNEMENT]` log line per application event, naming which element was chosen.
2. attunement-gameplay-verification — files: `tests/scenarios/elemental_attunement.json`, `.gen/harness/elemental_attunement/result.json` — depends on: 1
- In a live map, one scripted fire-typed direct hit on a Water-typed enemy removes exactly twice the baseline HP when the fire attunement is owned compared to the same hit without the perk (deterministic harness arithmetic, no projectile flight involved).
- In a live map, one scripted water-typed direct hit on a Water-typed enemy resolves at 2.0x while a scripted water-typed direct hit on a Fire-typed enemy still resolves at its normal multiplier once the water attunement is owned.
- The existing effectiveness-path gameplay regression (`floodgate_saltwater_purge`) still passes end-to-end after the change.

## Criteria

- A new Unique progression named `elemental_attunement` exists in the global progression pool, is eligible for chest/cave draws under the same rules as other Uniques, and reaches level 1 after being applied through the progression manager.
- Selecting the fire attunement makes fire-attributed damage resolve as super-effective (2.0x) against Water-typed enemies, while fire damage against Fire-typed enemies keeps its 0.5x self-resistance.
- Selecting the water attunement makes water-attributed damage resolve as super-effective (2.0x) against Water-typed enemies, while water damage against Water-typed enemies keeps its 0.5x self-resistance.
- Selecting the electric attunement makes electric-attributed damage resolve as super-effective (2.0x) against Electric-typed enemies, while electric damage against Electric-typed enemies keeps its 0.5x self-resistance.
- With no attunement selected, every attacker/defender pair in the type effectiveness table resolves exactly as before the change (fire/fire, water/water, electric/electric all stay 0.5x; no other entry shifts).
- The attunement perk can be selected from the player-facing progression pick flow (it appears as a choosable option and choosing it applies the perk), matching how existing Unique perks are presented.
- Debug-build `[ELEMENTAL_ATTUNEMENT]` log line per application event, naming which element was chosen.
- In a live map, one scripted fire-typed direct hit on a Water-typed enemy removes exactly twice the baseline HP when the fire attunement is owned compared to the same hit without the perk (deterministic harness arithmetic, no projectile flight involved).
- In a live map, one scripted water-typed direct hit on a Water-typed enemy resolves at 2.0x while a scripted water-typed direct hit on a Fire-typed enemy still resolves at its normal multiplier once the water attunement is owned.
- The existing effectiveness-path gameplay regression (`floodgate_saltwater_purge`) still passes end-to-end after the change.
