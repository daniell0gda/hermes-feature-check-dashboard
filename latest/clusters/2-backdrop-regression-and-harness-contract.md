# Cluster 2: backdrop-regression-and-harness-contract

owned file scope: `tests/scenarios/backdrop_earth_visible.json`, `tests/scenarios/backdrop_earth_glint.json`
dependencies: 1
parallel: false

## Acceptance criteria

- The focused harness scenarios `backdrop_earth_visible` and `backdrop_earth_glint` both pass headless with all their expectations green (present, grounded, flush placement, rotation invariance).
- From the normal gameplay camera in the windowed build, other continents, ocean, cloud banks, and the atmosphere rim remain visible; only the previously hidden mesh prefixes stay hidden, with no visual regressions to the backdrop look.
- A windowed run of `backdrop_earth_visible` produces a surface screenshot from the default gameplay camera showing the map sitting on the chosen continent with surrounding continent terrain blending in scale and color into the map field.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]`
- Full test: `["python3", "-c", "\nimport glob, json, subprocess, sys\nfails = []\nfor s in sorted(glob.glob('tests/scenarios/*.json')):\n    subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + s])\n    try:\n        ok = json.load(open('.gen/harness/' + json.load(open(s))['id'] + '/result.json'))['status'] == 'pass'\n    except Exception:\n        ok = False\n    print(s, 'PASS' if ok else 'FAIL')\n    if not ok:\n        fails.append(s)\nsys.exit(1 if fails else 0)\n"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Manual testing note: the two windowed/screenshot criteria require a manual-tester pass with
windowed screenshots saved under `.gen/` (e.g. `.gen/manual-report.md`); headless runs alone
do not satisfy them. Existing screenshots taken before the current rig pose are stale and
must be re-captured from a build whose harness result is green.
