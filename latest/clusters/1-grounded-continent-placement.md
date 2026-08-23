# Cluster 1: grounded-continent-placement

owned file scope: `scripts/game/visuals/BackdropEarth.gd`
dependencies: none
parallel: true

## Acceptance criteria

- The grounded configuration names exactly one continent mesh from `models/stylized_earth_in_clouds.glb`, and a debug-build warning is emitted if that mesh is absent from the instantiated model.
- After `configure_for_map` on a surface map, the earth rig's applied uniform scale equals `grounded_scale * world_radius / NATIVE_EARTH_RADIUS` (the configured value, not unit scale), observable in the grounding log line.
- After `configure_for_map` on a surface map, the chosen continent's terrain apex sits flush at the playable plane height (harness `backdrop_earth_center_y == 0`) with no floating gap between board and globe surface.
- The globe rig pose places the globe body close enough to the board that its horizon lies inside the normal gameplay camera's view frustum (not sunk below the plane nor pushed far behind the board).
- In the grounded configuration the earth spin never starts: the earth body's world transform sampled at two times several seconds apart is identical.
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent and the final rig position/scale/rotation.

## Verification commands

All via `run_project_cmd`, project=poke-defense-godot,
workspace=poke-defense-godot/issue-earth-continent-map-integration:

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]`
- Full test: `["python3", "-c", "\nimport glob, json, subprocess, sys\nnames = ['backdrop_earth_visible', 'backdrop_earth_glint', 'menu_backdrop_map', 'smoke_placement', 'removed_tower_kinds_no_crash']\nfails = []\nfor n in names:\n    s = 'tests/scenarios/%s.json' % n\n    subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + s])\n    try:\n        ok = json.load(open('.gen/harness/' + n + '/result.json'))['status'] == 'pass'\n    except Exception:\n        ok = False\n    print(n, 'PASS' if ok else 'FAIL')\n    if not ok:\n        fails.append(n)\nsys.exit(1 if fails else 0)\n"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Notes for implementor: current check evidence shows center_y = -81.768 ≠ 0 with rig scale
logged ≈ 1.0 instead of the configured ~41.6 — the pose contradicts the documented
flush-at-y=0 contract; reconcile scale/pose before rerunning. The horizon-in-frustum
criterion additionally requires windowed screenshot evidence via the manual-testing gate
(see cluster 2).
