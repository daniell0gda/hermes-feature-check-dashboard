# Feature Check — In Progress

Status: running
Phase: done
Iteration: —
Last update: 2026-08-09T15:54:59.872Z

## Progress

## ✅ Done

- **Focused test**: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/floodgate_hole_dig_reconnect.json`
- **Full test**: companion regression `floodgate_hole_range_band.json` (same band geometry; no single native full-suite entry)
- **Typecheck/Build**: `godot --headless --path . --editor --quit-after 300`
- `place_hole` → `HolePlacementModule.attempt_hole_placement` via `_hole_placement_module()`; returns `{ok, detail}`
- Off-path / unaffordable refuse with reason; no hole/spend on refuse
- Successful dig spends cost and runs complete-add notify path
- `remove_hole` → `remove_hole_complete` / `notify_hole_removed`; missing hole refuses with reason
- Scenario: Floodgate before player dig; dig inside HOLE_RANGE (prefer 2–3); floodgate damage > 0
- REFERENCE.md covers both actions
- Fresh focused harness pass + editor quit-after 300 exit 0

## ⬜ Pending


## ❌ Impossible



## Last node

reporter
