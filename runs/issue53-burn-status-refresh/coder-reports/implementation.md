# Coder report: implementation

## Changed files
- `scripts/game/status/BurnStatus.gd` — modified (`_reset` preserves `_pending_float` on refresh; debug `[BURN] refresh` log line)
- `tests/scenarios/burn_status_refresh_pending_damage.json` — new headless regression scenario

## Criteria
- Refreshing burn preserves accumulated fractional damage carry — Done
- Re-applying burn never delivers less total than unrefreshed expiry under identical payload/timing — Done
- Fresh burn starts with no carry and pays exactly its configured per-tick schedule — Done
- Debug-build [BURN] refresh log line naming preserved pending amount + new tick schedule — Done
- Headless scenario applies two overlapping burns through EffectsManager.apply_burn and asserts total >= single-application baseline figure — Done
- Same scenario asserts refreshed burn terminates after refreshed duration and flushes remaining fractional damage at expiry — Done

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit 0 (project import; run twice: cold import + post-fix typecheck pass)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/burn_status_refresh_pending_damage.json` — RED before fix (timeout at action 17: actual damage_by_type.fire = 4.0 vs required >= 5), GREEN after fix (exit 0, status=pass, final fire total exactly 5)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/fire_burn_on.json` — exit 0, status=pass, all 9 expectations pass, damage_by_type.fire = 242 (window 224–256 unchanged)

## Notes
- Fix is minimal: `_reset()` snapshots `_pending_float` when called on an already-running burn (`_ticks_total > 0`) and restores it after recomputing the schedule; `_begin` path still starts at 0 because a fresh node has `_ticks_total == 0`.
- The `[BURN] refresh` print fires only on genuine refreshes (not first `_begin`), so `fire_burn_on`'s out-of-band log check ("[BURN] begin ..." lines) is unaffected.
- RED evidence preserved in `.gen/harness/burn_status_refresh_pending_damage/` history: pre-fix run logged tick sequence `1(acc=1.5)` then refresh restarting from acc 0; post-fix log shows `[BURN] refresh ... preserved_pending=0.5, ticks=4, per=0.75`.
- Gotcha for tester: AgentHarness.materialize_engine_out_log skips rewriting `<id>.out.log` if it already contains this run's marker string; a stale log from an earlier run of the same scenario id will fail fresh log expectations. Delete `.gen/harness/_logs/<id>.out.log` between runs if log-source assertions change.
