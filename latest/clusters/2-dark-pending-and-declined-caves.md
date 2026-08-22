# Cluster 2: dark-pending-and-declined-caves

files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`, `scripts/game/CaveSystem.gd`
dependencies: none
parallel: true

## Acceptance criteria
- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.

## Verification commands
- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"]
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]
