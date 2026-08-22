# Cluster 1: attunement-perk-and-effectiveness

parallel: false
depends_on: none

## Owned files
- `scripts/progression/global.json`
- `scripts/config/Balance.gd`
- `autoload/ProgressionManager.gd`
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd`

## Acceptance criteria
- A new Unique progression named `elemental_attunement` exists in the global progression pool (`scripts/progression/global.json`), is eligible for chest/cave draws under the same rules as other Uniques, and reaches level 1 after being applied through the progression manager.
- With no attunement owned, every attacker/defender pair resolves exactly as before the change: fire/fire stays 0.5x, water/water stays 0.5x, electric/electric keeps its 0.3x self-resistance, and no other entry in the effectiveness path shifts.
- Owning the fire attunement makes fire-attributed damage resolve as super-effective (2.0x) against Water-typed and Electric-typed enemies, while fire damage against Fire-typed enemies keeps its 0.5x self-resistance.
- Owning the water attunement makes water-attributed damage resolve as super-effective (2.0x) against Fire-typed and Electric-typed enemies, while water damage against Water-typed enemies keeps its 0.5x self-resistance.
- Owning the electric attunement makes electric-attributed damage resolve as super-effective (2.0x) against Fire-typed and Water-typed enemies, while electric damage against Electric-typed enemies keeps its 0.3x self-resistance.
- Owning `elemental_attunement` grants extended coverage for exactly one chosen element (fire, water, or electric); the two unchosen elements' towers gain no new multiplier, and the perk never removes any self-resistance.
- The attunement perk can be selected from the player-facing progression pick flow (it appears as a choosable option and choosing it applies the perk), matching how existing Unique perks are presented.
- Debug-build `[ELEMENTAL_ATTUNEMENT]` log line per application event, naming which element was chosen.

## Verification
- Focused: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/elemental_attunement.json"])`
- Typecheck/build: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","--editor","--quit-after","300"])`
