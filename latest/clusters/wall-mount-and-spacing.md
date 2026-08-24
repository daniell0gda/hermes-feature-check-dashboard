# Cluster 1: wall-mount-and-spacing

- owned files: `scripts/game/underground/TorchPlacer.gd`, `tests/caves/test_torch_budget_scaling.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- TORCH_SPACING equals 4.
- On a straight carved corridor, consecutive spacing-pass torch grid cells are separated by exactly TORCH_SPACING unique corridor cells (stride counted on unique cells, not on raw wall-face entries).
- For every torch position returned by calculate_torch_positions, the horizontal offset from its grid cell center lies along exactly one cardinal axis with magnitude WALL_OFFSET, and the neighbouring cell in that direction is solid rock (a real wall from the voxel grid).
- Torch positions produced by the coverage-repair pass satisfy the same real-wall rule: their offsets are single-axis cardinal WALL_OFFSET mounts onto adjacent solid cells, never diagonal or zero-offset mid-corridor sticks.
- With no max_torches cap, torch count scales with live grid dimensions (no hardcoded maximum); at a large carved grid the placer still returns torches for the whole path.
- Debug-build [TORCH_PLACER] log line per coverage-repair torch added, naming the uncovered target cell and the chosen wall direction (debug builds only).

## Verification commands

- Focused test: run_project_cmd(godot-td, issue-cave-carved-path-torches): ["godot", "--headless", "--path", ".", "res://tests/caves/test_torch_budget_scaling.tscn"]
- Full test: run_project_cmd(godot-td, issue-cave-carved-path-torches): ["godot", "--headless", "--path", ".", "res://tests/caves/test_torch_budget_scaling.tscn"]
- Typecheck/build: run_project_cmd(godot-td, issue-cave-carved-path-torches): ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]
