# Cluster 1: siege-breaker-progression-data

- owned file scope: `scripts/progression/cannon_tower.json`, `scripts/progression/managers/CannonTowerProgressionManager.gd`
- dependencies: none
- parallel: true

## Acceptance criteria

- A new progression entry named `siege_breaker` exists in the Cannon progression file with type Unique, exactly 3 levels, and tower compatibility limited to `cannon`; the progression system reports it eligible only under that compatibility and its level never exceeds 3.
- Each of the three `siege_breaker` levels carries a player-readable description stating its armor-penalty figure (35% / 20% / 0% remaining penalty), so the progression modal shows a meaningful label at every level.
- With `siege_breaker` unowned, the progression config exposes the baseline armor penalty of 0.5; at levels 1, 2 and 3 it exposes 0.35, 0.20 and 0.0 respectively, matching the level data in the JSON.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Game.tscn", "--", "--harness=res://tests/scenarios/siege_breaker_progression.json"]`
- Full test: `["bash", "-lc", "set -e; mkdir -p .gen/harness/_logs; for f in tests/scenarios/*.json; do id=$(python3 -c \"import json,sys;print(json.load(open('$f'))['id'])\"); echo \"== $id\"; godot --headless --path . res://scenes/Game.tscn -- --harness=res://$f > .gen/harness/_logs/$id.log 2>&1 || { cat .gen/harness/_logs/$id.log; exit 1; }; python3 -c \"import json;r=json.load(open('.gen/harness/$id/result.json'));sys.exit(0 if r.get('status')=='pass' else 1)\" || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

manual_testing: required
