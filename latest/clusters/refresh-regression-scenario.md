# Cluster: refresh-regression-scenario

- Cluster ID: 2
- Owned file scope: `tests/scenarios/burn_status_refresh_pending_damage.json`
- Dependencies: 1
- parallel: false

## Acceptance criteria

- A headless harness scenario applies two overlapping burn payloads to the same enemy through the same public apply entry point a tower uses and asserts total delivered burn damage is monotonically non-decreasing relative to a single-application baseline run of the same seed and timing.
- The same scenario asserts the refreshed burn still terminates after its refreshed duration and flushes any remaining fractional damage at expiry rather than dropping it.

## Verification commands

- Focused: `run_project_cmd(project="godot-td", workspace="godot-td/issue-burn-status-refresh-loses-pending-damage", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/burn_status_refresh_pending_damage.json"])`
- Full: `run_project_cmd(project="godot-td", workspace="godot-td/issue-burn-status-refresh-loses-pending-damage", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/fire_burn_on.json"])`
- Typecheck/build: `run_project_cmd(project="godot-td", workspace="godot-td/issue-burn-status-refresh-loses-pending-damage", cmd=["godot","--headless","--path",".","--editor","--quit-after","300"])`
