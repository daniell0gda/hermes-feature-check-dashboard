# Cluster 3: siege-breaker-vfx

- owned file scope: `scripts/game/actors/effects/EffectsManager.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd`
- dependencies: 2
- parallel: false

## Acceptance criteria

- A Cannon hit that benefits from the `siege_breaker` armor penalty reduction triggers the existing one-shot armor-crack shatter flash (the StaticBreach breaching-hit flash path, observable as the harness's `static_breach_flash` counter advancing); no new second VFX effect is introduced.

## Verification

- Focused test: `["python3", "-c", "import glob, json, os, subprocess, sys\nfailed = []\nfor f in sorted(glob.glob('tests/scenarios/siege_breaker_*.json')):\n    sid = json.load(open(f))['id']\n    r = subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + f])\n    res = json.load(open(os.path.join('.gen', 'harness', sid, 'result.json')))\n    ok = r.returncode == 0 and res.get('status') == 'pass'\n    print(sid, 'PASS' if ok else 'FAIL')\n    if not ok:\n        failed.append(sid)\nsys.exit(1 if failed else 0)"]`
- Full test: `["python3", "-c", "import glob, json, os, subprocess, sys\nfailed = []\nfor f in sorted(glob.glob('tests/scenarios/*.json')):\n    sid = json.load(open(f))['id']\n    r = subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + f])\n    res = json.load(open(os.path.join('.gen', 'harness', sid, 'result.json')))\n    ok = r.returncode == 0 and res.get('status') == 'pass'\n    print(sid, 'PASS' if ok else 'FAIL')\n    if not ok:\n        failed.append(sid)\nsys.exit(1 if failed else 0)"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

manual_testing: required
