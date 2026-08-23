# Cluster 2: backdrop-regression-and-harness-contract

owned file scope: `tests/scenarios/backdrop_earth_visible.json`, `tests/scenarios/backdrop_earth_glint.json`
dependencies: 1
parallel: false

## Acceptance criteria

- The focused harness scenarios `backdrop_earth_visible` and `backdrop_earth_glint` both pass headless with all their expectations green (present, grounded, flush placement, rotation invariance).
- From the normal gameplay camera in the windowed build, other continents, ocean, cloud banks, and the atmosphere rim remain visible; only the previously hidden mesh prefixes stay hidden, with no visual regressions to the backdrop look.
- A windowed run of `backdrop_earth_visible` produces a surface screenshot from the default gameplay camera showing the map sitting on the chosen continent with surrounding continent terrain blending in scale and color into the map field.

## Verification commands

All via `run_project_cmd`, project=poke-defense-godot,
workspace=poke-defense-godot/issue-earth-continent-map-integration:

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]`
- Full test: `["python3", "-c", "\nimport glob, json, subprocess, sys\nnames = ['backdrop_earth_visible', 'backdrop_earth_glint', 'menu_backdrop_map', 'smoke_placement', 'removed_tower_kinds_no_crash']\nfails = []\nfor n in names:\n    s = 'tests/scenarios/%s.json' % n\n    subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + s])\n    try:\n        ok = json.load(open('.gen/harness/' + n + '/result.json'))['status'] == 'pass'\n    except Exception:\n        ok = False\n    print(n, 'PASS' if ok else 'FAIL')\n    if not ok:\n        fails.append(n)\nsys.exit(1 if fails else 0)\n"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Manual testing note: the two windowed/screenshot criteria require a manual-tester pass with
windowed screenshots saved under `.gen/` plus rotation-invariance evidence (second shot
several seconds later); headless runs alone do not satisfy them. Screenshots taken before
the current rig-pose fix are stale and must be re-captured from a build whose harness
result is green.
