# Acceptance Plan: perk-frozen-fracture

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/frozen_fracture_slowed_vs_unslowed.json"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/frozen_fracture_*.json tests/scenarios/enemy_armor_ballista.json tests/scenarios/progression_pick.json tests/scenarios/ice_rate_matched_speeds.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`

## Clusters

1. frozen-fracture-perk-definition — files: `scripts/progression/global.json`, `scripts/progression/managers/CurseProgressionManager.gd` (or a sibling global-Common manager following it), `autoload/ProgressionManager.gd` — depends on: none
- The `frozen_fracture` perk exists in the global Common progression pool as type Common with exactly 3 levels.
- With `frozen_fracture` at levels 1/2/3, the exposed armor-damage bonus is 10%/20%/30% respectively; when the perk is not owned the bonus is disabled (no increase).
- A chest draw that includes the pool offers `frozen_fracture` alongside other Common perks, and its level descriptions state the armor-damage bonus values.
2. frozen-fracture-armor-runtime — files: `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `autoload/ProgressionManager.gd`, `scripts/game/actors/towers/IceTower.gd` (only if slow-source attribution needs exposing) — depends on: 1
- While an enemy is under an active Ice Tower slow, each point of incoming armor damage from any damage source is increased by the perk's level bonus (10%/20%/30%).
- An enemy that is not currently slowed takes unmodified armor damage even when `frozen_fracture` is owned.
- After an enemy's Ice slow expires, subsequent hits take unmodified armor damage again.
- The bonus scales armor damage only; the hit's HP damage is unchanged by the perk.
- A debug-build log line with a stable filterable `[FrozenFracture]` marker records each boosted armor-damage application (enemy id, level, bonus percent).
3. frozen-fracture-game-test-coverage — files: `tests/scenarios/frozen_fracture_slowed_vs_unslowed.json`, `tests/scenarios/frozen_fracture_levels_and_expiry.json` — depends on: 1, 2
- A focused game-test scenario compares armor remaining after identical armor-damage hits on a slowed versus an unslowed enemy with the perk active, asserting the slowed enemy lost exactly the level-multiplied amount more.
- A focused game-test scenario asserts the three perk levels produce 10%/20%/30% extra armor damage respectively and that behavior reverts to baseline once the slow expires.

## Criteria

- The `frozen_fracture` perk exists in the global Common progression pool as type Common with exactly 3 levels.
- With `frozen_fracture` at levels 1/2/3, the exposed armor-damage bonus is 10%/20%/30% respectively; when the perk is not owned the bonus is disabled (no increase).
- A chest draw that includes the pool offers `frozen_fracture` alongside other Common perks, and its level descriptions state the armor-damage bonus values.
- While an enemy is under an active Ice Tower slow, each point of incoming armor damage from any damage source is increased by the perk's level bonus (10%/20%/30%).
- An enemy that is not currently slowed takes unmodified armor damage even when `frozen_fracture` is owned.
- After an enemy's Ice slow expires, subsequent hits take unmodified armor damage again.
- The bonus scales armor damage only; the hit's HP damage is unchanged by the perk.
- A debug-build log line with a stable filterable `[FrozenFracture]` marker records each boosted armor-damage application (enemy id, level, bonus percent).
- A focused game-test scenario compares armor remaining after identical armor-damage hits on a slowed versus an unslowed enemy with the perk active, asserting the slowed enemy lost exactly the level-multiplied amount more.
- A focused game-test scenario asserts the three perk levels produce 10%/20%/30% extra armor damage respectively and that behavior reverts to baseline once the slow expires.

## Notes

- manual_testing: optional
- Pure numeric/stat modifier perk; issue explicitly states no new VFX, and no player-visible story exists that a still screenshot could prove beyond the standard progression draw UI, so no `ui_scenario.md` is written.
