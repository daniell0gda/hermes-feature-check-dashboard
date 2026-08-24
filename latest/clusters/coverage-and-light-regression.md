# Cluster 2: coverage-and-light-regression

- owned files: `scripts/game/underground/TorchPlacer.gd`, `tests/scenarios/carve_curved_torches_coverage.json`, `scripts/game/underground/Torch.gd`
- dependencies: 1
- parallel: false

## Acceptance criteria

- After carving an L-shaped side-to-side path with a bend, every required corridor cell is lit: the harness reports uncovered_corridor_cells == 0 with active torch count > 0.
- Torch lighting constants are unchanged from the pre-change baseline: Torch.LIGHT_RADIUS, light energy, omni range/attenuation, and colour are identical to the values on branch commit f161e1d before this fix.

## Verification commands

- Focused test: run_project_cmd(godot-td, issue-cave-carved-path-torches): ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]
- Full test: run_project_cmd(godot-td, issue-cave-carved-path-torches): ["godot", "--headless", "--path", ".", "res://tests/caves/test_torch_budget_scaling.tscn"]
- Typecheck/build: run_project_cmd(godot-td, issue-cave-carved-path-torches): ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]
