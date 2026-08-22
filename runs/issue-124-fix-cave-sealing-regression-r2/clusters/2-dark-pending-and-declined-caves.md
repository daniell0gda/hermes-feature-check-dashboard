# Cluster 2: dark-pending-and-declined-caves

- owned files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`, `scripts/game/CaveSystem.gd`
- dependencies: none
- parallel: true

## Acceptance criteria

- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.

## Verification

run_project_cmd tokens (project `godot-td`, workspace `poke-defense-godot/issue-cave-carved-path-torches`):

- Focused tests:
  - ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
  - ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"]
  - ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json"]
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Scan fresh runner stdout/stderr separately from harness status for `Parse Error`, `SCRIPT ERROR`, `Failed loading resource`, `Invalid call`.
