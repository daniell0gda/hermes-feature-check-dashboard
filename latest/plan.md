# Acceptance Plan: perk-sundering-bolts

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/sundering_bolts_progression.json"]`
- Full test: `["sh", "-lc", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/sundering_bolts_progression.json && godot --headless --path . res://tests/tower/test_tower_armor_damage.tscn && godot --headless --path . res://tests/enemy/test_enemy_armor_damage.tscn && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_ballista.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`

## Clusters

1. sundering-bolts-perk-definition — files: `scripts/progression/global.json`, `autoload/ProgressionManager.gd` — depends on: none
- The global progression catalog defines a `sundering_bolts` perk with exactly 3 levels whose level values are 0.1, 0.2 and 0.35, and it is eligible for chest draws under the standard eligibility rules.
- With the perk unowned, the ProgressionManager config query returns a disabled config; at level N it reports enabled with that level's ratio (0.1 / 0.2 / 0.35), and re-applying or replaying levels 1..N neither compounds nor collapses the ratio.
- After `reset_for_new_game()`, the sundering_bolts config is disabled again.
2. sunder-application-in-take-damage — files: `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — depends on: 1
- A hit from any damaging tower while sundering_bolts is owned at level L applies additional armor damage equal to L's ratio (10% / 20% / 35%) of the hit's final post-modifier HP damage, on the same hit.
- The conversion base is the hit's final damage after all other damage-modifying perks and effectiveness multipliers, not the static towers.xml base value (a hit whose final damage was modified by another perk yields armor damage proportional to the modified amount).
- When the hit's final damage is 0 (Porter, damage=0), no additional armor damage is applied regardless of perk level.
- Ballista's flat armor_dmg of 20 still applies and stacks additively with the perk-derived armor damage on the same hit.
- With the perk unowned, hits behave exactly as before: no perk-derived armor damage is added.
- Debug-build [SUNDERING_BOLTS] log line per applied sunder event, naming the perk level, the final hit damage used as the base, and the derived armor-damage amount.
3. sundering-bolts-harness-scenario — files: `tests/scenarios/sundering_bolts_progression.json` — depends on: 1, 2
- A focused AgentHarness scenario drives a representative tower (Ballista) through the shared take_damage path and asserts the resulting enemy armor values for the no-perk baseline and for each of the three perk levels (10% / 20% / 35%), including the additive stack with Ballista's flat armor_dmg.

## Criteria

- The global progression catalog defines a `sundering_bolts` perk with exactly 3 levels whose level values are 0.1, 0.2 and 0.35, and it is eligible for chest draws under the standard eligibility rules.
- With the perk unowned, the ProgressionManager config query returns a disabled config; at level N it reports enabled with that level's ratio (0.1 / 0.2 / 0.35), and re-applying or replaying levels 1..N neither compounds nor collapses the ratio.
- After `reset_for_new_game()`, the sundering_bolts config is disabled again.
- A hit from any damaging tower while sundering_bolts is owned at level L applies additional armor damage equal to L's ratio (10% / 20% / 35%) of the hit's final post-modifier HP damage, on the same hit.
- The conversion base is the hit's final damage after all other damage-modifying perks and effectiveness multipliers, not the static towers.xml base value (a hit whose final damage was modified by another perk yields armor damage proportional to the modified amount).
- When the hit's final damage is 0 (Porter, damage=0), no additional armor damage is applied regardless of perk level.
- Ballista's flat armor_dmg of 20 still applies and stacks additively with the perk-derived armor damage on the same hit.
- With the perk unowned, hits behave exactly as before: no perk-derived armor damage is added.
- Debug-build [SUNDERING_BOLTS] log line per applied sunder event, naming the perk level, the final hit damage used as the base, and the derived armor-damage amount.
- A focused AgentHarness scenario drives a representative tower (Ballista) through the shared take_damage path and asserts the resulting enemy armor values for the no-perk baseline and for each of the three perk levels (10% / 20% / 35%), including the additive stack with Ballista's flat armor_dmg.
- Existing armor bar reflects the drain with no new VFX required (existing armor bar UI already tracks the armor stat the perk drains).

manual_testing: required

Manual testing note: visible armor-bar drain after sundered hits, windowed evidence via the runner, screenshots to `.gen/screenshots/`.
