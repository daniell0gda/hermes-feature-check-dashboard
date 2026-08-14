# Issue #62 — Windowed final-clear fix

## Outcome

Fixed the remaining `final_clear_probe` failure. Fresh map_1 headless and windowed probes now pass through the real final-wave producer path. No victory state or phase is assigned by the harness; `Game._handle_wave_completion()` still performs the normal post-wave, finished-state, checkpoint, UI, and `StatsManager` completion flow.

## Root cause

`debug_emit_final_clear()` synchronously completed each intermediate wave by emitting `SpawnerSystem.all_spawners_clear`, then called the public `next_wave()` owner. With `GameState.auto_next` still enabled, the first intermediate clear callback legitimately invoked `_consume_next_wave_transition("auto_next")` itself. The loop then called `next_wave()` while the newly spawned wave was still `PHASE_ACTIVE_WAVE` and incomplete, so `_consume_next_wave_transition()` correctly returned false. The observed failure was therefore the deterministic interaction between synchronous probe timing and the real auto-next transition owner, not an Xvfb or OpenGL issue.

## Fix

In `scripts/game/Game.gd`, `debug_emit_final_clear()` now disables `GameState.auto_next` for the duration of this synchronous harness probe. Manual `next_wave()` remains the sole transition owner while the probe completes intermediate waves through the actual `SpawnerSystem.all_spawners_clear` signal. Normal gameplay configuration and auto-next behavior are unchanged outside this debug probe.

Docker/Xvfb readiness changes in `Dockerfile` and `docker-entrypoint.sh` were preserved unchanged.

## Fresh verification

All project commands ran through `run_project_cmd` with project `godot-td`, workspace `godot-td/issue-62`.

1. Headless focused probe:

```text
godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_headless.json --harness-run=windowed-victory-fix-headless
```

Runner exit `0`. Fresh result:
`.gen/harness/issue_62_full_headless-windowed-victory-fix-headless/result.json`

Structured result: `status=pass`, final-clear action `ok=true`, phase `finished`, game state `finished`, completion time `1.253`, current wave `4`, and all expectations passed. Headless screenshots were correctly skipped as non-visual evidence.

2. Windowed OpenGL/Xvfb visual probe:

```text
godot --path . --rendering-method gl_compatibility --audio-driver Dummy res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_full_visual.json --harness-run=windowed-victory-fix-windowed
```

Runner exit `0`. Fresh result:
`.gen/harness/issue_62_full_visual-windowed-victory-fix-windowed/result.json`

Structured result: `status=pass`; final-clear action index `14` has `ok=true`, phase `finished`, game state `finished`, completion time `15.85`; all three expectations passed (`checkpoint_phase=finished`, `game_state=finished`, `Save safe`). Renderer output confirmed OpenGL compatibility on Mesa llvmpipe. Five fresh 1920x1080 PNGs were captured:

- `shots/saving.png` — visible `Saving` indicator and active wave content.
- `shots/save_failed.png` — visible `Save failed` indicator.
- `shots/save_recovered.png` — visible recovered `Save safe` state.
- `shots/naptime.png` — fresh inter-wave/naptime checkpoint image.
- `shots/victory.png` — visible victory overlay with all waves complete and finished state.

All five images were inspected independently; each is marked captured/non-static in the structured result.

## Diagnostics

Known unrelated/pre-existing diagnostics remain and were not hidden or changed: missing UI nodes (`Tower1`, `IconBoss`), duplicate `layer_changed` connection during map reinitialization, renderer/ObjectDB/RID teardown leak messages, and windowed V-Sync/Forward+-only rendering warnings. No targeted parse error, failed resource load, or harness assertion failure occurred in either fresh probe.

`git diff --check` passed. No Git/GitHub lifecycle operation, commit, push, merge, or issue mutation was performed.

## Files

Created:

- `.gen/coder-reports/windowed-victory-fix.md`

Modified for this fix:

- `scripts/game/Game.gd`

Pre-existing issue-62 worktree changes, including `Dockerfile` and `docker-entrypoint.sh`, were retained and not reverted.

The approved project worker was released after the final project command.
