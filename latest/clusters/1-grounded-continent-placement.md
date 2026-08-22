# Cluster 1: grounded-continent-placement

- Files: `scripts/game/visuals/BackdropEarth.gd`
- Dependencies: none
- Parallel: true

## Acceptance criteria

- The grounded configuration targets exactly one named continent mesh (`Continent_Africa`) from `stylized_earth_in_clouds.glb`, and the chosen mesh name is recorded in code with a runtime warning if the mesh is absent.
- After `configure_for_map` on a surface map, the earth rig pose places the chosen continent flush under the playable field at y=0 with no floating gap, and the globe's body/horizon lies close enough to the board that it is inside the normal gameplay camera's view frustum (no longer sunk a full body radius below the plane nor pushed far behind the board).
- From the normal gameplay camera, the continent terrain fills the space around/underneath the playable field so the map no longer reads as an isolated floating tile against plain sky (windowed screenshot).
- In the windowed screenshot from the normal gameplay camera, the continent terrain around the board blends with the map field in scale and color, with no hard seam between board and globe surface.
- During play in the grounded configuration, the globe does not rotate: the earth body's world transform measured at two times several seconds apart is identical (spin disabled, not merely slowed).
- Debug-build `[BACKDROP EARTH]` log line per grounding event naming the selected continent mesh and the final rig position/scale/rotation.

## Verification

Run via `run_project_cmd` token arrays:

- Focused: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/backdrop_earth_visible.json"]
- Full: ["python3", "-c", "import glob,subprocess,sys\nfor s in sorted(glob.glob('tests/scenarios/*.json')):\n    r=subprocess.call(['godot','--headless','--path','.','res://scenes/Main.tscn','--','--harness=res://'+s])\n    if r!=0: sys.exit(r)\n"]
- Typecheck/build: ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]
