# Cluster: burn-refresh-preserves-pending-float

- Cluster ID: 1
- Owned file scope: `scripts/game/status/BurnStatus.gd`
- Dependencies: none
- parallel: false

## Acceptance criteria

- Refreshing the burn on an already-burning enemy preserves the accumulated fractional damage carry instead of zeroing it, so the next integer damage tick accounts for it.
- Re-applying burn to an already-burning enemy never delivers less total burn damage than letting the original application run to expiry unrefreshed, under identical timing and payload.
- A fresh burn applied to a previously unburned enemy still starts with no carried-over fractional damage and delivers exactly its configured per-tick schedule.
- Debug-build [BURN] log line per burn refresh naming the preserved pending fractional amount and the new tick schedule.

## Verification commands

- Focused: `run_project_cmd(project="godot-td", workspace="godot-td/issue-burn-status-refresh-loses-pending-damage", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/burn_status_refresh_pending_damage.json"])`
- Full: `run_project_cmd(project="godot-td", workspace="godot-td/issue-burn-status-refresh-loses-pending-damage", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/fire_burn_on.json"])`
- Typecheck/build: `run_project_cmd(project="godot-td", workspace="godot-td/issue-burn-status-refresh-loses-pending-damage", cmd=["godot","--headless","--path",".","--editor","--quit-after","300"])`
