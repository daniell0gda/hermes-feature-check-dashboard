# Acceptance Plan: perk-static-breach

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/static_breach_thresholds.json"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/static_breach_*.json tests/scenarios/enemy_armor_ballista.json tests/scenarios/progression_pick.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`

## Clusters

1. static-breach-perk-definition — files: `scripts/progression/electric_tower.json`, `scripts/progression/managers/ElectricTowerProgressionManager.gd`, `autoload/ProgressionManager.gd` — depends on: none
- The `static_breach` progression exists in the Electric tower progression pool, is Electric-tower-compatible only, and offers exactly 3 levels.
- With `static_breach` at levels 1/2/3, the exposed breach threshold is 5/4/3 hits respectively; when the perk is not owned it is disabled.
- A chest draw restricted to non-Electric towers never offers `static_breach`.
2. static-breach-charge-runtime — files: `scripts/game/actors/Projectile.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `autoload/ProgressionManager.gd` — depends on: 1
- Each direct or chained Electric hit on an enemy adds exactly one charge to that enemy; non-Electric damage sources add no charge.
- When a hit raises an enemy's charge to the active threshold, that hit sets the enemy's remaining armor to zero before its HP damage is applied.
- Reaching the threshold consumes the stack so subsequent hits start counting from zero again rather than breaching every hit.
- An enemy that leaves all tower range / loses target lock for the documented reset duration has its charge cleared; a hit after the duration starts counting from zero.
- Charges are tracked per enemy: two enemies hit different numbers of times keep independent counts, and breaching one does not affect the other.
- A debug-build log line with a stable filterable `[StaticBreach]` marker records each breach event (enemy id, level, threshold) and each charge reset event (enemy id).
3. static-breach-vfx — files: `scripts/game/actors/effects/EffectsManager.gd`, new effect script following `scripts/game/actors/effects/OverheatGlowVFX.gd` pattern, `scripts/utils/HighlightShaderUtils.gd` — depends on: 2
- While an enemy carries at least one stacked charge, it shows a visible stacking-charge highlight built through the existing HighlightShaderUtils preset-highlight factory pattern, and the visual clears when the charge resets.
- The triggering breach hit produces a distinct shatter flash on the enemy, distinct from the persistent charge highlight, reusing the shield-crack asset where one exists.
4. static-breach-game-test-coverage — files: `tests/scenarios/static_breach_thresholds.json`, `tests/scenarios/static_breach_isolation.json`, `tests/scenarios/static_breach_scope.json`, `tests/scenarios/static_breach_vfx.json` — depends on: 1, 2, 3
- A focused harness scenario proves the threshold behavior: fewer than the threshold hits leave armor intact, and the threshold hit zeroes armor, at each of the three levels.
- A focused harness scenario proves per-enemy isolation and the reset-duration behavior using scripted hits and timed waits.
- A focused harness scenario proves Electric-only scope: scripted non-Electric hits accumulate no charges and never breach armor while the perk is owned.
- A windowed harness scenario captures the stacking-charge indicator and the shatter flash, asserting the corresponding state transitions in the same run.

## Criteria

- The `static_breach` progression exists in the Electric tower progression pool, is Electric-tower-compatible only, and offers exactly 3 levels.
- With `static_breach` at levels 1/2/3, the exposed breach threshold is 5/4/3 hits respectively; when the perk is not owned it is disabled.
- A chest draw restricted to non-Electric towers never offers `static_breach`.
- Each direct or chained Electric hit on an enemy adds exactly one charge to that enemy; non-Electric damage sources add no charge.
- When a hit raises an enemy's charge to the active threshold, that hit sets the enemy's remaining armor to zero before its HP damage is applied.
- Reaching the threshold consumes the stack so subsequent hits start counting from zero again rather than breaching every hit.
- An enemy that leaves all tower range / loses target lock for the documented reset duration has its charge cleared; a hit after the duration starts counting from zero.
- Charges are tracked per enemy: two enemies hit different numbers of times keep independent counts, and breaching one does not affect the other.
- A debug-build log line with a stable filterable `[StaticBreach]` marker records each breach event (enemy id, level, threshold) and each charge reset event (enemy id).
- While an enemy carries at least one stacked charge, it shows a visible stacking-charge highlight built through the existing HighlightShaderUtils preset-highlight factory pattern, and the visual clears when the charge resets.
- The triggering breach hit produces a distinct shatter flash on the enemy, distinct from the persistent charge highlight, reusing the shield-crack asset where one exists.
- A focused harness scenario proves the threshold behavior: fewer than the threshold hits leave armor intact, and the threshold hit zeroes armor, at each of the three levels.
- A focused harness scenario proves per-enemy isolation and the reset-duration behavior using scripted hits and timed waits.
- A focused harness scenario proves Electric-only scope: scripted non-Electric hits accumulate no charges and never breach armor while the perk is owned.
- A windowed harness scenario captures the stacking-charge indicator and the shatter flash, asserting the corresponding state transitions in the same run.

manual_testing: required
