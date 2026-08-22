# Cluster 1: attunement-perk-and-effectiveness

parallel: false
depends_on: none

## Owned files
- `scripts/progression/global.json`
- `scripts/config/Balance.gd`
- `autoload/ProgressionManager.gd`

## Acceptance criteria
- A new Unique progression named `elemental_attunement` exists in the global progression pool, is eligible for chest/cave draws under the same rules as other Uniques, and reaches level 1 after being applied through the progression manager.
- Selecting the fire attunement makes fire-attributed damage resolve as super-effective (2.0x) against Water-typed enemies, while fire damage against Fire-typed enemies keeps its 0.5x self-resistance.
- Selecting the water attunement makes water-attributed damage resolve as super-effective (2.0x) against Water-typed enemies, while water damage against Water-typed enemies keeps its 0.5x self-resistance.
- Selecting the electric attunement makes electric-attributed damage resolve as super-effective (2.0x) against Electric-typed enemies, while electric damage against Electric-typed enemies keeps its 0.5x self-resistance.
- With no attunement selected, every attacker/defender pair in the type effectiveness table resolves exactly as before the change (fire/fire, water/water, electric/electric all stay 0.5x; no other entry shifts).
- The attunement perk can be selected from the player-facing progression pick flow (it appears as a choosable option and choosing it applies the perk), matching how existing Unique perks are presented.
- Debug-build `[ELEMENTAL_ATTUNEMENT]` log line per application event, naming which element was chosen.

## Verification
- Focused: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/elemental_attunement.json"])`
- Typecheck/build: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","--editor","--quit-after","300"])`
