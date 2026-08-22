# Cluster 1: siege-breaker-progression-data

- owned file scope: `scripts/progression/cannon_tower.json`, `scripts/progression/managers/CannonTowerProgressionManager.gd`
- dependencies: none
- parallel: true

## Acceptance criteria

- A new progression entry named `siege_breaker` exists in the Cannon progression file with type Unique, exactly 3 levels, and tower compatibility limited to `cannon`; the progression system reports it eligible while unowned and its level never exceeds 3.
- Each of the three `siege_breaker` levels carries a player-readable description stating its armor-penalty figure (35% / 20% / 0% remaining penalty), so the progression modal shows a meaningful label at every level.
- With `siege_breaker` unowned, the progression config exposes the baseline armor penalty of 0.5; at levels 1, 2 and 3 it exposes 0.35, 0.20 and 0.0 respectively, matching the level data in the JSON.
- Taking `siege_breaker` levels then starting a fresh game resets its level to 0 and restores the baseline 0.5 penalty (save/load and reset behave like every other progression).

## Verification

- Focused test: `["python3", "-c", "import glob, json, os, subprocess, sys\nfailed = []\nfor f in sorted(glob.glob('tests/scenarios/siege_breaker_*.json')):\n    sid = json.load(open(f))['id']\n    r = subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + f])\n    res = json.load(open(os.path.join('.gen', 'harness', sid, 'result.json')))\n    ok = r.returncode == 0 and res.get('status') == 'pass'\n    print(sid, 'PASS' if ok else 'FAIL')\n    if not ok:\n        failed.append(sid)\nsys.exit(1 if failed else 0)"]`
- Full test: `["python3", "-c", "import glob, json, os, subprocess, sys\nfailed = []\nfor f in sorted(glob.glob('tests/scenarios/*.json')):\n    sid = json.load(open(f))['id']\n    r = subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + f])\n    res = json.load(open(os.path.join('.gen', 'harness', sid, 'result.json')))\n    ok = r.returncode == 0 and res.get('status') == 'pass'\n    print(sid, 'PASS' if ok else 'FAIL')\n    if not ok:\n        failed.append(sid)\nsys.exit(1 if failed else 0)"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

manual_testing: required
