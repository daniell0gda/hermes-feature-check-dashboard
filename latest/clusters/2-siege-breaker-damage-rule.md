# Cluster 2: siege-breaker-damage-rule

- owned file scope: `scripts/game/actors/enemy/parts/EnemyHealthController.gd`
- dependencies: 1
- parallel: false

## Acceptance criteria

- With `siege_breaker` unowned, a scripted hit attributed to tower type `cannon` against an enemy with armor remaining deals HP damage reduced by the baseline factor 0.5 (existing behavior unchanged).
- With `siege_breaker` at level 1, the same Cannon-attributed hit against an armored enemy deals HP damage reduced by 0.35 instead of 0.5.
- With `siege_breaker` at level 2, the same Cannon-attributed hit deals HP damage reduced by 0.20 instead of 0.5.
- With `siege_breaker` at level 3, a Cannon-attributed hit against an enemy with armor remaining deals its full HP damage (no armor penalty applied).
- At every `siege_breaker` level, a hit attributed to any non-Cannon tower type against an enemy with armor remaining still deals HP damage reduced by the baseline 0.5 factor.
- `siege_breaker` never changes the enemy's armor value: after equal Cannon hits with the perk owned vs unowned, the armor remaining is identical and other towers' hits drain armor at the normal rate (the existing armor-regression scenario `enemy_armor_ballista` still passes unchanged).
- Debug-build `[SiegeBreaker]` log line per reduced-penalty application on a Cannon hit, naming the enemy id and current perk level.

## Verification

- Focused test: `["python3", "-c", "import glob, json, os, subprocess, sys\nfailed = []\nfor f in sorted(glob.glob('tests/scenarios/siege_breaker_*.json')):\n    sid = json.load(open(f))['id']\n    r = subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + f])\n    res = json.load(open(os.path.join('.gen', 'harness', sid, 'result.json')))\n    ok = r.returncode == 0 and res.get('status') == 'pass'\n    print(sid, 'PASS' if ok else 'FAIL')\n    if not ok:\n        failed.append(sid)\nsys.exit(1 if failed else 0)"]`
- Full test: `["python3", "-c", "import glob, json, os, subprocess, sys\nfailed = []\nfor f in sorted(glob.glob('tests/scenarios/*.json')):\n    sid = json.load(open(f))['id']\n    r = subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + f])\n    res = json.load(open(os.path.join('.gen', 'harness', sid, 'result.json')))\n    ok = r.returncode == 0 and res.get('status') == 'pass'\n    print(sid, 'PASS' if ok else 'FAIL')\n    if not ok:\n        failed.append(sid)\nsys.exit(1 if failed else 0)"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

manual_testing: required
