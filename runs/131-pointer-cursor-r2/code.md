# Coder report: 1-project-wide-pointer-cursor\n\n# Coder report: 1-project-wide-pointer-cursor

## Changed files
- `project.godot` — mod: registered `PointerCursor="*res://scripts/ui/PointerCursor.gd"` autoload (appended last; autoload order otherwise untouched)
- `scripts/ui/PointerCursor.gd` — new (pre-existing untracked scratch from an earlier probe, finalized): project-wide cursor assigner
- `scripts/testing/HarnessActions.gd` — mod: added `hover_ui` action (warp mouse over a UI Control)
- `scripts/testing/HarnessValues.gd` — mod: added `ui_control` value source (`cursor_shape`, `exists`)
- `tests/scenarios/ui_pointer_cursor.json` — new: focused AgentHarness scenario

## Criteria
- Buttons show pointing-hand on hover, windowed + fullscreen — Done (all BaseButton nodes get CURSOR_POINTING_HAND=2 via autoload; shape is a window property, mode-independent; windowed verified by harness run)
- Other clickable surfaces show pointer — Done (BaseButton base class covers CheckBox/ToggleButton and custom button widgets like TowerShopSlot)
- Non-clickable surfaces keep arrow — Done (autoload touches only BaseButton; asserted HealthBar + UpgPanel Frame = 0)
- Windowed screenshot of hovered button under `.gen/harness/ui_pointer_cursor/shots/` — Done (`hover_pointer_on_speed_btn.png`, 1920x1080, captured)
- Focused `ui_pointer_cursor` scenario passes headless + windowed — Done (status=pass both modes; all 8 expectations pass, asserting effective `mouse_default_cursor_shape` through the new ui_control source)
- `smoke_placement` still passes — Done (status=pass after the change)

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ui_pointer_cursor.json` — exit 0; `[Harness] status=pass exit=0`; 6 cursor_shape expectations pass (4x==2 clickables incl. SpeedBtn/AutoNext/Tower1/PlayBtn, 2x==0 non-clickables), exists+gamestate pass. First RED run (before PointerCursor was registered) failed all ui_control expectations with actual=None.
- `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ui_pointer_cursor.json` (windowed) — exit 0; status=pass, screenshot action outcome=captured, saved `.gen/harness/ui_pointer_cursor/shots/hover_pointer_on_speed_btn.png`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json` — exit 0; result.json status=pass
- `godot --headless --path . --editor --quit-after 300` — exit 0; clean import/script scan, no parse errors (PointerCursor.gd registered in script docs)

## Notes
- The scenario JSON existed but its earlier stored "pass" was from before the harness gained the features it uses: the headless rerun at task start showed `hover_ui` = "unknown action type" (skipped as failed action) and every `ui_control` expectation failing with actual=None because nothing resolved them, plus `PointerCursor` was not registered in project.godot at all. All three gaps are now closed.
- PointerCursor mechanism: `get_tree().node_added` hook for dynamically created buttons + one deferred whole-tree sweep at startup. Measured during probing (see `.gen/pointer_diag.txt`): node_added alone missed 39 of 52 scene-file buttons because scene instancing completes before autoload `_ready`.
- HarnessActions is RefCounted — it has no `get_viewport()`; `hover_ui` warps via `loop.root.warp_mouse(center)` (root Window). First attempt using `self.get_viewport()` was a compile error, caught on the first focused run.
- `logs/balance/map_difficulty.csv` shows modified in git status but is untouched runtime-generated balance output from harness runs (pre-existing dirty state from prior runs in this worktree); not part of this feature.
\n\n# Coder report: implementation\n\n# Coder report: implementation (revision-code-1)

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
\n