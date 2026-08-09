# Feature Check — In Progress

Status: running
Phase: awaiting_user_plan_approval
Iteration: 0
Last update: 2026-08-09T16:37:58.668Z

## Progress

## ✅ Done


## ⬜ Pending

- **Focused test**: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/burn_status_refresh_pending_damage.json`
- **Full test**: regression pair `fire_burn_on.json` + `fire_flashover_spread.json` (no single native full-suite entry)
- **Typecheck/Build**: `godot --headless --path . --editor --quit-after 300`
- Refresh never silently drops fractional pending tick damage
- Second overlapping burn never delivers less total burn/fire damage than single-application baseline
- Fresh burn starts clean; still one non-stacking `BurnStatus` per enemy
- Whole-HP ticks + end flush; fire stats attribution preserved
- Focused scenario passes fresh and is load-bearing against the bug
- `fire_burn_on` + `fire_flashover_spread` pass; editor quit-after 300 exits 0

## ❌ Impossible



## Last node

planner
