# Acceptance Plan: earth-continent-map-integration

manual_testing: required

## Verification

All commands run via `run_project_cmd` (project=poke-defense-godot,
workspace=poke-defense-godot/issue-earth-continent-map-integration), from the repository root.
Per the check-scope bound in `.gen/request.md`, do NOT run the full 135-scenario suite
(runner 15-min timeout); the "full test" here is the bounded related-scenario set named there.

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]` then `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_glint.json"]`
- Full test: `["python3", "-c", "\nimport glob, json, subprocess, sys\nnames = ['backdrop_earth_visible', 'backdrop_earth_glint', 'menu_backdrop_map', 'smoke_placement', 'removed_tower_kinds_no_crash']\nfails = []\nfor n in names:\n    s = 'tests/scenarios/%s.json' % n\n    subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + s])\n    try:\n        ok = json.load(open('.gen/harness/' + n + '/result.json'))['status'] == 'pass'\n    except Exception:\n        ok = False\n    print(n, 'PASS' if ok else 'FAIL')\n    if not ok:\n        fails.append(n)\nsys.exit(1 if fails else 0)\n"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. grounded-continent-placement — files: `scripts/game/visuals/BackdropEarth.gd` — depends on: none
- The grounded configuration names exactly one continent mesh from `models/stylized_earth_in_clouds.glb`, and a debug-build warning is emitted if that mesh is absent from the instantiated model.
- After `configure_for_map` on a surface map, the earth rig's applied uniform scale equals `grounded_scale * world_radius / NATIVE_EARTH_RADIUS` (the configured value, not unit scale), observable in the grounding log line.
- After `configure_for_map` on a surface map, the chosen continent's terrain apex sits flush at the playable plane height (harness `backdrop_earth_center_y == 0`) with no floating gap between board and globe surface.
- The globe rig pose places the globe body close enough to the board that its horizon lies inside the normal gameplay camera's view frustum (not sunk below the plane nor pushed far behind the board).
- In the grounded configuration the earth spin never starts: the earth body's world transform sampled at two times several seconds apart is identical.
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent and the final rig position/scale/rotation.
2. backdrop-regression-and-harness-contract — files: `tests/scenarios/backdrop_earth_visible.json`, `tests/scenarios/backdrop_earth_glint.json` — depends on: 1
- The focused harness scenarios `backdrop_earth_visible` and `backdrop_earth_glint` both pass headless with all their expectations green (present, grounded, flush placement, rotation invariance).
- From the normal gameplay camera in the windowed build, other continents, ocean, cloud banks, and the atmosphere rim remain visible; only the previously hidden mesh prefixes stay hidden, with no visual regressions to the backdrop look.
- A windowed run of `backdrop_earth_visible` produces a surface screenshot from the default gameplay camera showing the map sitting on the chosen continent with surrounding continent terrain blending in scale and color into the map field.

## Criteria

- The grounded configuration names exactly one continent mesh from `models/stylized_earth_in_clouds.glb`, and a debug-build warning is emitted if that mesh is absent from the instantiated model.
- After `configure_for_map` on a surface map, the earth rig's applied uniform scale equals `grounded_scale * world_radius / NATIVE_EARTH_RADIUS` (the configured value, not unit scale), observable in the grounding log line.
- After `configure_for_map` on a surface map, the chosen continent's terrain apex sits flush at the playable plane height (harness `backdrop_earth_center_y == 0`) with no floating gap between board and globe surface.
- The globe rig pose places the globe body close enough to the board that its horizon lies inside the normal gameplay camera's view frustum (not sunk below the plane nor pushed far behind the board).
- In the grounded configuration the earth spin never starts: the earth body's world transform sampled at two times several seconds apart is identical.
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent and the final rig position/scale/rotation.
- The focused harness scenarios `backdrop_earth_visible` and `backdrop_earth_glint` both pass headless with all their expectations green (present, grounded, flush placement, rotation invariance).
- From the normal gameplay camera in the windowed build, other continents, ocean, cloud banks, and the atmosphere rim remain visible; only the previously hidden mesh prefixes stay hidden, with no visual regressions to the backdrop look.
- A windowed run of `backdrop_earth_visible` produces a surface screenshot from the default gameplay camera showing the map sitting on the chosen continent with surrounding continent terrain blending in scale and color into the map field.
