# Cluster 1: grounded-continent-placement

owned file scope: `scripts/game/visuals/BackdropEarth.gd`
dependencies: none
parallel: true

## Acceptance criteria

- The grounded configuration names exactly one continent mesh from `models/stylized_earth_in_clouds.glb`, and a debug-build warning is emitted if that mesh is absent from the instantiated model.
- After `configure_for_map` on a surface map, the chosen continent's terrain apex sits flush at the playable plane height (harness `backdrop_earth_center_y == 0`) with no floating gap between board and globe surface.
- The globe rig pose places the globe body close enough to the board that its horizon lies inside the normal gameplay camera's view frustum (not sunk below the plane nor pushed far behind the board).
- In the grounded configuration the earth spin never starts: the earth body's world transform sampled at two times several seconds apart is identical.
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent and the final rig position/scale/rotation.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]`
- Full test: `["python3", "-c", "\nimport glob, json, subprocess, sys\nfails = []\nfor s in sorted(glob.glob('tests/scenarios/*.json')):\n    subprocess.run(['godot', '--headless', '--path', '.', 'res://scenes/Main.tscn', '--', '--harness=res://' + s])\n    try:\n        ok = json.load(open('.gen/harness/' + json.load(open(s))['id'] + '/result.json'))['status'] == 'pass'\n    except Exception:\n        ok = False\n    print(s, 'PASS' if ok else 'FAIL')\n    if not ok:\n        fails.append(s)\nsys.exit(1 if fails else 0)\n"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Implementation notes (prior-iteration evidence, not contract): the first grounded pose logged
`pos=(-21.2, -83.2, -51.76)` with `scale≈1.0` (`grounded_scale=2.6` not applied) and
`center_y=-83.2`; the produced pose must satisfy the flush-at-y=0 and visible-horizon
criteria above. A later harness run (2026-08-22 22:27) passes with `center_y≈0`, but the
existing windowed screenshot predates that pose and shows no globe — fresh windowed
evidence is still required.
