# Coder report: implementation (revision-code-1)

## Changed files
No new source changes this revision. The r1 worktree implementation was
re-based onto 9d54964 and re-verified in full; the durable changes remain:
- `scripts/ui/PointerCursor.gd` (new) — project-wide autoload assigning
  `CURSOR_POINTING_HAND` to every `BaseButton` via both a `node_added` hook and
  a deferred whole-tree sweep (node_added alone missed 39/52 scene-file buttons).
- `project.godot` (mod) — PointerCursor autoload registration.
- `scripts/testing/HarnessValues.gd` (mod) — `ui_control` value source exposing
  `cursor_shape` / `exists`; master's `nature` source preserved.
- `scripts/testing/HarnessActions.gd` (mod) — `hover_ui` action.
- `tests/scenarios/ui_pointer_cursor.json` (new) — focused harness scenario.

## Criteria
All 6 cluster criteria — Done (verified this revision, see below).

## Commands and results (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-pointer-cursor-on-clickable-surfaces)
- Editor import/build `godot --headless --path . --editor --quit-after 300`
  — exit 0; clean first-scan, PointerCursor autoload loaded.
- Focused headless `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/ui_pointer_cursor.json` — exit 0,
  `[Harness] status=pass exit=0`. result.json: 8/8 expectations pass —
  4 clickables cursor_shape==2 (CURSOR_POINTING_HAND), 2 non-clickables ==0,
  exists==true, game_state==playing.
- Regression `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/smoke_placement.json` — exit 0,
  `[Harness] status=pass exit=0`.
- Windowed focused (same scenario, no --headless) — exit 0, status=pass;
  screenshot action outcome=captured saved=true →
  `.gen/harness/ui_pointer_cursor/shots/hover_pointer_on_speed_btn.png`
  (1920x1080, 1,882,959 bytes).

## Notes
- All gates re-ran fresh this revision after the rebase; no code edits were
  needed — the r1 two-mechanism autoload design holds on 9d54964.
- Exit-time RID/ObjectDB leak warnings are pre-existing engine teardown noise
  also present on baseline scenarios; not introduced by this feature.
- Pre-existing dirty `logs/balance/*.csv` is harness runtime output, unrelated.
- manual_testing remains open for the manual-tester profile (fullscreen hover
  pass + ui_feels_broken sanity check); Godot screenshots do not render the OS
  cursor, so evidence pair = programmatic assertion + hover screenshot.
