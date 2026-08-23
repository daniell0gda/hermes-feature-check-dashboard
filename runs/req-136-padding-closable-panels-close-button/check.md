# Check report: req-136-padding-closable-panels-close-button

classification: pass

## Verdict

All six cluster criteria verified Done on the current working tree via fresh
`run_project_cmd` runs (project=godot-td,
workspace=poke-defense-godot/issue-padding-closable-panels-close-button). No
build/test failures, no missing evidence, no quality violations.

## Verification commands (all through run_project_cmd)

| Command | Exit | Result |
|---|---|---|
| `godot --headless --path . res://tests/ui/test_titled_panel_close_corner.tscn` | 0 | titled_panel_close_corner: 32 ok / 0 failed, 11/11 groups; `[TITLED_PANEL] UpgPanel / ManageTowersPanel / Panel (Options) reserves 90x103px...` logs observed |
| `godot --headless --path . res://tests/ui/test_enemy_armor_bar.tscn` | 0 | enemy_armor_bar: 35 ok / 0 failed |
| `godot --headless --path . res://tests/ui/test_enemy_health_bar_boss_icon.tscn` | 0 | enemy_health_bar_boss_icon: 18 ok / 0 failed |
| `godot --headless --path . res://tests/ui/test_enemy_health_bar_oiled_icon.tscn` | 0 | enemy_health_bar_oiled_icon: 8 ok / 0 failed |
| `godot --headless --path . --import` | 0 | import clean |
| `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json` | 0 | `[Harness] status=pass exit=0`; four fresh screenshots written to `.gen/harness/hud_other_panels/shots/` at 04:34–04:35 UTC Aug 23 (post-20:00-UTC fix) |

Known benign noise (pre-existing, not regressions): invalid UID warnings in
HudTheme.tres/UI.tscn (text-path fallback works); a few unimported GLBs
(backdrop earth, portal arch, ruined house); GLES3 teardown leak errors after
the harness's forced quit.

## Acceptance criteria evidence

1. Closable TitledPanel reserves horizontal padding so no content rect
   intersects the CloseChip rect at any panel size — DONE.
   test_titled_panel_close_corner asserts no leaf-content overlap at multiple
   sizes ("no leaf content control intersects the CloseChip rect", UpgPanel at
   380x420 and 620x480).
2. Padding applies only when closable / scene-placed CloseChip exists — DONE.
   "a non-closable panel keeps its full-width content area (20 < 90)" plus
   full-rect-anchor assertions; scene-placed-chip case covered separately.
3. CloseChip flush INSIDE frame top-right; exactly one close_requested per
   press — DONE. "it sits flush in the top-right corner (offset 0.0, 0.0)",
   "the ✕ sits inside the full-size frame, not floating outside it", "pressing
   it emits close_requested once (got 1)". Visually confirmed in fresh shots.
4. UpgPanel tower-details content clear of chip — DONE. Headless rect
   assertions at two sizes + vision check of fresh panel_tower_details.png:
   header/badge/stat rows/buttons all clear of the ✕, ✕ flush inside corner.
   ui_feels_broken: no.
5. Manage Towers and Options clear of chip — DONE. Real-scene tests pass;
   vision check of fresh panel_manage_towers.png and panel_options.png: no
   content overlaps the ✕; ✕ fully enclosed in frame corner art.
   ui_feels_broken: no on both. (Options zoom-in confirmed the embossed X is
   flush inside the metal corner bracket with clear space to all content; the
   functional chip identity is proven by the run log's reservation line.)
6. Debug `[TITLED_PANEL]` log naming panel and inset — DONE. Observed live for
   UpgPanel, ManageTowersPanel and Panel (Options): "reserves 90x103px of
   top-right padding for the close corner".

Manual testing (required): satisfied by this checker's own windowed harness run
plus independent vision inspection of the four fresh post-fix screenshots —
ui_feels_broken: no on all four (tower_details, pause_menu, manage_towers,
options). Pause menu (non-closable) layout unchanged, cleanly layered.

## Changed-file quality findings

Diff vs HEAD: `.gitignore`, `scenes/UI.tscn`, `scripts/ui/UI.gd`,
`scripts/ui/hud/TitledPanel.gd`, `tests/ui/test_titled_panel_close_corner.gd`.
Scope matches the cluster's file set; no unrelated changes, no scope creep.
TitledPanel.gd follows project CLAUDE.md rules (typed vars, small focused
functions, guard clauses, debug-build tagged logging, duplicated stylebox
instead of mutating shared theme resource). Test report now written under
`.gen/test-reports/` — prior quality-note resolved. Both prior quality-note
open entries carry RESOLVED markers; nothing new appended.

## Blockers

None.

## Unverified items

None. The visual overlap claim was re-proven by this checker's own fresh
windowed run and screenshot inspection (not solely from coder/manual reports).
