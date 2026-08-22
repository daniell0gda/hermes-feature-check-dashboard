# Acceptance Plan: perk-siege-breaker

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Game.tscn", "--", "--harness=res://tests/scenarios/siege_breaker_progression.json"]`
- Full test: `["bash", "-lc", "set -e; mkdir -p .gen/harness/_logs; for f in tests/scenarios/*.json; do id=$(python3 -c \"import json,sys;print(json.load(open('$f'))['id'])\"); echo \"== $id\"; godot --headless --path . res://scenes/Game.tscn -- --harness=res://$f > .gen/harness/_logs/$id.log 2>&1 || { cat .gen/harness/_logs/$id.log; exit 1; }; python3 -c \"import json;r=json.load(open('.gen/harness/$id/result.json'));sys.exit(0 if r.get('status')=='pass' else 1)\" || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. siege-breaker-progression-data — files: `scripts/progression/cannon_tower.json`, `scripts/progression/managers/CannonTowerProgressionManager.gd` — depends on: none
- A new progression entry named `siege_breaker` exists in the Cannon progression file with type Unique, exactly 3 levels, and tower compatibility limited to `cannon`; the progression system reports it eligible only under that compatibility and its level never exceeds 3.
- Each of the three `siege_breaker` levels carries a player-readable description stating its armor-penalty figure (35% / 20% / 0% remaining penalty), so the progression modal shows a meaningful label at every level.
- With `siege_breaker` unowned, the progression config exposes the baseline armor penalty of 0.5; at levels 1, 2 and 3 it exposes 0.35, 0.20 and 0.0 respectively, matching the level data in the JSON.
2. siege-breaker-damage-rule — files: `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — depends on: 1
- With `siege_breaker` unowned, a scripted hit attributed to tower type `cannon` against an enemy with armor remaining deals HP damage reduced by the baseline factor 0.5 (existing behavior unchanged).
- With `siege_breaker` at level 1, the same Cannon-attributed hit against an armored enemy deals HP damage reduced by 0.35 instead of 0.5.
- With `siege_breaker` at level 2, the same Cannon-attributed hit deals HP damage reduced by 0.20 instead of 0.5.
- With `siege_breaker` at level 3, a Cannon-attributed hit against an enemy with armor remaining deals its full HP damage (no armor penalty applied).
- At every `siege_breaker` level, a hit attributed to any non-Cannon tower type against an enemy with armor remaining still deals HP damage reduced by the baseline 0.5 factor.
- `siege_breaker` never changes the enemy's armor value: after equal Cannon hits with the perk owned vs unowned, the armor remaining is identical and other towers' hits drain armor at the normal rate (the existing armor-regression scenario still passes unchanged).
- Debug-build `[SiegeBreaker]` log line per reduced-penalty application, naming the enemy id and current perk level.
3. siege-breaker-vfx — files: `scripts/game/actors/effects/EffectsManager.gd` — depends on: 2
- A Cannon hit that benefits from the `siege_breaker` armor penalty reduction triggers the existing shield-crack/shatter flash effect (the Exposed Plating / static-breach flash path); no new second VFX effect is introduced.

## Criteria

- A new progression entry named `siege_breaker` exists in the Cannon progression file with type Unique, exactly 3 levels, and tower compatibility limited to `cannon`; the progression system reports it eligible only under that compatibility and its level never exceeds 3.
- Each of the three `siege_breaker` levels carries a player-readable description stating its armor-penalty figure (35% / 20% / 0% remaining penalty), so the progression modal shows a meaningful label at every level.
- With `siege_breaker` unowned, the progression config exposes the baseline armor penalty of 0.5; at levels 1, 2 and 3 it exposes 0.35, 0.20 and 0.0 respectively, matching the level data in the JSON.
- With `siege_breaker` unowned, a scripted hit attributed to tower type `cannon` against an enemy with armor remaining deals HP damage reduced by the baseline factor 0.5 (existing behavior unchanged).
- With `siege_breaker` at level 1, the same Cannon-attributed hit against an armored enemy deals HP damage reduced by 0.35 instead of 0.5.
- With `siege_breaker` at level 2, the same Cannon-attributed hit deals HP damage reduced by 0.20 instead of 0.5.
- With `siege_breaker` at level 3, a Cannon-attributed hit against an enemy with armor remaining deals its full HP damage (no armor penalty applied).
- At every `siege_breaker` level, a hit attributed to any non-Cannon tower type against an enemy with armor remaining still deals HP damage reduced by the baseline 0.5 factor.
- `siege_breaker` never changes the enemy's armor value: after equal Cannon hits with the perk owned vs unowned, the armor remaining is identical and other towers' hits drain armor at the normal rate (the existing armor-regression scenario still passes unchanged).
- Debug-build `[SiegeBreaker]` log line per reduced-penalty application, naming the enemy id and current perk level.
- A Cannon hit that benefits from the `siege_breaker` armor penalty reduction triggers the existing shield-crack/shatter flash effect (the Exposed Plating / static-breach flash path); no new second VFX effect is introduced.

manual_testing: required
