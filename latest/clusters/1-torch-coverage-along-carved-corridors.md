# Cluster 1: torch-coverage-along-carved-corridors

files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`
dependencies: none
parallel: true

## Acceptance criteria
- After carving a 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius.
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).

## Verification commands
- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"]
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]
