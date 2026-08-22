# Check report: panels-closable-x-escape (issue #133) — iteration 1

Classification: **pass**

## Commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-panels-closable-x-escape)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `godot --version` | 0 | 4.4.1.stable |
| Typecheck/build | `godot --headless --path . --import` | 0 | import clean |
| Focused test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/panels_closable_x_escape.json` | 0 | `[Harness] status=pass exit=0`; fresh `.gen/harness/panels_closable_x_escape/result.json` status=pass, 9/9 expectations true |
| Full test | `godot --headless --windowed --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json` | 0 | `[Harness] status=pass exit=0`; fresh result.json status=pass, 1/1 expectations true (`game_state == paused` after Esc-with-nothing-open) |
| Unit test | `godot --headless --path . res://tests/ui/test_titled_panel_close_corner.tscn` | 0 | 22 ok, 0 failed |

## Acceptance criteria evidence

1. **Every non-Menu panel shows an X close button; pressing hides the panel** — Done.
   Tower details panel: `is_closable = true` on UpgPanel in scenes/UI.tscn; TitledPanel grows the corner CloseChip and emits `close_requested`, connected to new `UI.close_tower_details()` (scripts/ui/UI.gd). Manage Towers and Options already carry wired corner X buttons. Asserted by tests/ui/test_titled_panel_close_corner.gd (chip exists, flush top-right, press emits close_requested once) and by the focused scenario driving each close path.
2. **Escape closes topmost open non-Menu panel; never opens/closes Menu** — Done.
   `UI._unhandled_input` routes Esc through `_closable_panels_topmost_first()` (options > manage towers > tower details), sets input handled, returns before pause-menu branch. Scenario drives `escape_pressed()` (same branch) after opening each panel; `[PANELS] <panel> close` lines observed in run log for each.
3. **After close (X or Escape), gameplay input works, not left paused** — Done.
   `_close_panel` sets `tree.paused=false` and `GameState._set_game_state("playing")`. Scenario asserts `paused == false` and `game_state != "paused"` / `== "playing"` after closes — passing expectations in result.json.
4. **Escape with no panel open shows Menu (pause) panel** — Done.
   Fall-through to `show_pause_menu()`. Full-suite hud_other_panels run shows `[PANELS] pause menu open` and its expectation asserts `game_state == "paused"` (pass).
5. **Re-opening a panel closed via X or Escape works with correct content** — Done.
   Scenario re-selects the tower after both `close_tower_details` and `escape_pressed` paths and waits for `get_upgrade_panel_text contains "Tower"` — passing wait_for_condition steps.
6. **Debug-build [PANELS] log line per open/close event naming panel and direction** — Done.
   `UI._panels_log` gated on `OS.is_debug_build()`; transitions-only for tower details to avoid poll spam. All six open/close lines asserted via `log` source in result.json.
7. **Headless AgentHarness scenario passes with status pass, exit code 0, for every covered panel** — Done.
   Fresh `.gen/harness/panels_closable_x_escape/result.json`: status=pass, exit 0, 9/9 expectations pass covering tower details, Manage Towers, Options.

## Changed-file quality findings

- scripts/ui/UI.gd, scripts/ui/ManageTowersPopup.gd, scripts/ui/OptionsScreen.gd, scenes/UI.tscn, tests/scenarios/panels_closable_x_escape.json, tests/ui/test_titled_panel_close_corner.gd reviewed against /opt/data/coding_rules.md and worktree CLAUDE.md. Typed variables, guard clauses, debug-gated [TAG] logging, surgical diff — no demoting violations.
- Advisory only (see quality-notes.md): options-close log line is emitted twice per close (UI._panels_log + OptionsScreen._on_close local print); Escape loop body in `_unhandled_input` duplicates `escape_pressed()` logic instead of delegating.

## Test overlap check

New files: panels_closable_x_escape.json (no existing scenario covers panel close/Esc routing) and test_titled_panel_close_corner.gd (pre-existing from earlier work; covers TitledPanel chip mechanics only, no overlap with routing). No duplicate coverage found in tests/scenarios/ or tests/ui/.

## Blockers

None.

## Unverified items

- Plan marks `manual_testing: required` (player-facing UI). Headless + windowed harness runs assert state/log, not pixels; no `.gen/manual-report.md` present from the manual tester profile. No acceptance criterion textually requires screenshot evidence, so no criterion is demoted, but the manual-testing pass remains outstanding for the leader to schedule.
