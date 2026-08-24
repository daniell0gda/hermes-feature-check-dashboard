# Acceptance Plan: req-124-cave-carved-path-torches-r5

## Verification

- Focused test: `run_project_cmd(godot-td, issue-cave-carved-path-torches): ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]`
- Full test: `run_project_cmd(godot-td, issue-cave-carved-path-torches): ["godot", "--headless", "--path", ".", "res://tests/caves/test_torch_budget_scaling.tscn"]`
- Typecheck/build: `run_project_cmd(godot-td, issue-cave-carved-path-torches): ["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`

## Clusters

1. wall-mount-and-spacing — files: `scripts/game/underground/TorchPlacer.gd`, `tests/caves/test_torch_budget_scaling.gd` — depends on: none
- TORCH_SPACING equals 4.
- On a straight carved corridor, consecutive spacing-pass torch grid cells are separated by exactly TORCH_SPACING unique corridor cells (stride counted on unique cells, not on raw wall-face entries).
- For every torch position returned by calculate_torch_positions, the horizontal offset from its grid cell center lies along exactly one cardinal axis with magnitude WALL_OFFSET, and the neighbouring cell in that direction is solid rock (a real wall from the voxel grid).
- Torch positions produced by the coverage-repair pass satisfy the same real-wall rule: their offsets are single-axis cardinal WALL_OFFSET mounts onto adjacent solid cells, never diagonal or zero-offset mid-corridor sticks.
- With no max_torches cap, torch count scales with live grid dimensions (no hardcoded maximum); at a large carved grid the placer still returns torches for the whole path.
- Debug-build [TORCH_PLACER] log line per coverage-repair torch added, naming the uncovered target cell and the chosen wall direction (debug builds only).
2. coverage-and-light-regression — files: `scripts/game/underground/TorchPlacer.gd`, `tests/scenarios/carve_curved_torches_coverage.json`, `scripts/game/underground/Torch.gd` — depends on: 1
- After carving an L-shaped side-to-side path with a bend, every required corridor cell is lit: the harness reports uncovered_corridor_cells == 0 with active torch count > 0.
- Torch lighting constants are unchanged from the pre-change baseline: Torch.LIGHT_RADIUS, light energy, omni range/attenuation, and colour are identical to the values on branch commit f161e1d before this fix.
3. windowed-manual-proof — files: `.gen/ui_scenario.md` — depends on: 2
- Manual windowed run (no --headless): underground screenshots of a curved side-to-side carve show every torch hugging a wall face, with no stick floating mid-corridor or hovering in front of a wall; ui_feels_broken: no.

manual_testing: required

## Criteria

- TORCH_SPACING equals 4.
- On a straight carved corridor, consecutive spacing-pass torch grid cells are separated by exactly TORCH_SPACING unique corridor cells (stride counted on unique cells, not on raw wall-face entries).
- For every torch position returned by calculate_torch_positions, the horizontal offset from its grid cell center lies along exactly one cardinal axis with magnitude WALL_OFFSET, and the neighbouring cell in that direction is solid rock (a real wall from the voxel grid).
- Torch positions produced by the coverage-repair pass satisfy the same real-wall rule: their offsets are single-axis cardinal WALL_OFFSET mounts onto adjacent solid cells, never diagonal or zero-offset mid-corridor sticks.
- With no max_torches cap, torch count scales with live grid dimensions (no hardcoded maximum); at a large carved grid the placer still returns torches for the whole path.
- Debug-build [TORCH_PLACER] log line per coverage-repair torch added, naming the uncovered target cell and the chosen wall direction (debug builds only).
- After carving an L-shaped side-to-side path with a bend, every required corridor cell is lit: the harness reports uncovered_corridor_cells == 0 with active torch count > 0.
- Torch lighting constants are unchanged from the pre-change baseline: Torch.LIGHT_RADIUS, light energy, omni range/attenuation, and colour are identical to the values on branch commit f161e1d before this fix.
- Manual windowed run (no --headless): underground screenshots of a curved side-to-side carve show every torch hugging a wall face, with no stick floating mid-corridor or hovering in front of a wall; ui_feels_broken: no.
