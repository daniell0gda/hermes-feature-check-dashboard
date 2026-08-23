# Check report: req-136-padding-closable-panels-close-button (iteration 5, fresh verification)

classification: pass

## Verification (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-padding-closable-panels-close-button)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `git status --short` | 0 | dirty tree as expected (feature diff present) |
| Typecheck/import | `godot --headless --path . --import` | 0 | clean import |
| Focused test | `res://tests/ui/test_titled_panel_close_corner.tscn` | 0 | 33 ok, 0 failed; all 11 groups completed |
| Full suite 1/3 | `res://tests/ui/test_enemy_armor_bar.tscn` | 0 | 35 ok, 0 failed |
| Full suite 2/3 | `res://tests/ui/test_enemy_health_bar_boss_icon.tscn` | 0 | 18 ok, 0 failed |
| Full suite 3/3 | `res://tests/ui/test_enemy_health_bar_oiled_icon.tscn` | 0 | 8 ok, 0 failed |

All commands ran fresh this iteration through `run_project_cmd`; no host-shell Godot.
Pre-existing HudTheme.tres / UI.tscn invalid-UID warnings fall back to text paths — legacy
noise, not this change.

## Criterion-by-criterion

1. Closable panel reserves padding; no content intersects the CloseChip at any size — PASS.
   `_reserve_content_padding()` duplicates the frame stylebox and widens right/top content
   margins by CLOSE_CORNER_SIZE. Fresh run asserts margin ≥ 90 (got 136), top ≥ 103 (got 149),
   and "no leaf content control intersects the CloseChip rect".
2. Padding only when closable / scene chip present — PASS. Gated on `_close_chip() != null`;
   fresh assertions: "a non-closable panel keeps its full-width content area (20 < 90)",
   anchors untouched, and a scene-placed chip reserves the same margin (136).
3. Chip flush inside frame top-right; exactly one close_requested per press — PASS. Fresh:
   "sits flush in the top-right corner (offset 0.0, 0.0)", "✕ sits inside the full-size frame,
   not floating outside it", "pressing it emits close_requested once (got 1)".
4. UpgPanel tower details clear — PASS. Fresh assertions at 380x420 and 620x480 on real
   UI.tscn: header/badge/stat rows/buttons clear of the chip.
5. Manage Towers + Options clear — PASS. Fresh headless assertions for ManageTowersPanel.tscn
   and OptionsScreen.tscn, plus checker vision inspection of fresh post-fix windowed shots
   (21:31 UTC, `.gen/harness/hud_other_panels/shots/`): panel_tower_details.png — ✕ flush
   inside frame art, no content overlap; panel_manage_towers.png — no overlap, ✕ flush;
   panel_options.png — no content overlap (the corner ✕ there sits slightly inset as part of
   the frame's own ornament; criterion requires content clearance, which holds);
   panel_pause_menu.png — no overlap. ui_feels_broken: no on all four shots.
6. Debug `[TITLED_PANEL]` log naming panel + inset — PASS. Observed in fresh output for
   ManageTowersPanel, Panel (Options) and UpgPanel: "reserves 90x103px of top-right padding".

## Changed-file quality

Diff scope: `scripts/ui/hud/TitledPanel.gd`, `scripts/ui/UI.gd`, `scenes/UI.tscn` (one line),
`tests/ui/test_titled_panel_close_corner.gd`, one-line `.gitignore`. Checked against
/opt/data/coding_rules.md and CLAUDE.md: typed variables throughout, small focused helpers,
guard clauses, debug-gated `[TITLED_PANEL]` log per project convention, surgical diff with
clear doc comments explaining why stylebox margins are widened instead of shrinking the frame.
No violations in new/changed code. New tests do not duplicate existing coverage — no prior
suite asserted these behaviors.

Quality notes: open entry `stray-test-report-file` remains resolved (report under
`res://.gen/test-reports/`, root file gitignored). No new entries this iteration.
Manual testing (windowed harness + vision) recorded in `.gen/quality-notes.md` and
`.gen/manual_testing.md` — required manual path executed with fresh post-fix screenshots.

## Blockers

None. Unverified items: none beyond the inherent pixel-level subjectivity of visual review,
mitigated by fresh screenshots plus automated rect-intersection assertions.
