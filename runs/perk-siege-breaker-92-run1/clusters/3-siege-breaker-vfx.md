# Cluster 3: siege-breaker-vfx

- owned file scope: `scripts/game/actors/effects/EffectsManager.gd`
- dependencies: 2
- parallel: false

## Acceptance criteria

- A Cannon hit that benefits from the `siege_breaker` armor penalty reduction triggers the existing shield-crack/shatter flash effect (the Exposed Plating / static-breach flash path); no new second VFX effect is introduced.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Game.tscn", "--", "--harness=res://tests/scenarios/siege_breaker_progression.json"]`
- Full test: `["bash", "-lc", "set -e; mkdir -p .gen/harness/_logs; for f in tests/scenarios/*.json; do id=$(python3 -c \"import json,sys;print(json.load(open('$f'))['id'])\"); echo \"== $id\"; godot --headless --path . res://scenes/Game.tscn -- --harness=res://$f > .gen/harness/_logs/$id.log 2>&1 || { cat .gen/harness/_logs/$id.log; exit 1; }; python3 -c \"import json;r=json.load(open('.gen/harness/$id/result.json'));sys.exit(0 if r.get('status')=='pass' else 1)\" || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

manual_testing: required
