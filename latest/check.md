# Check report: req-136-padding-closable-panels-close-button (iteration 6, fresh verification)

classification: pass

## Verification (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-padding-closable-panels-close-button)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `git status --short` | 0 | feature diff present as expected |
| Typecheck/import | `godot --headless --path . --import` | 0 | clean import |
| Focused test | `godot --headless --path . res://tests/ui/test_titled_panel_close_corner.tscn` | 0 | 33 ok, 0 failed; all 11 groups ran |
| Full suite 1/3 | `res://tests/ui/test_enemy_armor_bar.tscn` | 0 | 35 ok, 0 failed |
| Full suite 2/3 | `res://tests/ui/test_enemy_health_bar_boss_icon.tscn` | 0 | 18 ok, 0 failed |
| Full suite 3/3 | `res://tests/ui/test_enemy_health_bar_oiled_icon.tscn` | 0 | 8 ok, 0 failed |

All commands fresh this iteration through `run_project_cmd`; no host-shell Godot.
Pre-existing HudTheme.tres / UI.tscn invalid-UID warnings fall back to text paths —
legacy noise, not this change.

## Criterion-by-criterion

1. Closable TitledPanel reserves horizontal padding; no content control intersects the
   CloseChip rect at any size — PASS. `_reserve_content_padding()` duplicates the frame
   stylebox and widens right/top content margins by CLOSE_CORNER_SIZE. Fresh run asserts
   right margin ≥ 90 (got 136), top ≥ 103-equivalent inset (got 76), and "no leaf content
   control intersects the CloseChip rect".
2. Padding only when `is_closable` / scene-placed chip exists — PASS. Gated on
   `_close_chip() != null`; fresh assertions: "a non-closable panel keeps its full-width
   content area (20 < 90)", anchors untouched, scene-placed chip reserves same margin (136).
3. Chip flush inside frame's top-right corner; exactly one `close_requested` per press —
   PASS. Fresh: "sits flush in the top-right corner (offset 0.0, 0.0)", "✕ sits inside the
   full-size frame, not floating outside it", "pressing it emits close_requested once (got 1)".
4. Tower details (UpgPanel) content clear — PASS. Fresh assertions on real UI.tscn at
   380x420 and 620x480: header/badge/stat rows/buttons all clear of the chip.
5. Manage Towers + Options clear after layout — PASS. Fresh headless assertions for both
   scenes ("keeps every visible content control clear of the CloseChip"), plus checker
   vision inspection of fresh windowed shots (2026-08-23 03:53 UTC,
   `.gen/harness/hud_other_panels/shots/`, post-fix): tower_details — ✕ flush inside frame
   art, no overlap; manage_towers — ✕ flush, no overlap; pause_menu — ✕ clear of "Paused"
   menu; options — frame's own metal X corner ornament present, no *content* overlaps the
   corner region. ui_feels_broken: no on all four shots.
6. Debug `[TITLED_PANEL]` log naming panel + reserved inset — PASS. Observed in fresh
   output for ManageTowersPanel, Panel (Options), UpgPanel and synthetic panels:
   "[TITLED_PANEL] <name> reserves 90x103px of top-right padding for the close corner",
   debug-gated by `OS.is_debug_build()`.

## Changed-file quality

Diff scope: `scripts/ui/hud/TitledPanel.gd` (+35), `scripts/ui/UI.gd` (+12),
`scenes/UI.tscn` (one line), `tests/ui/test_titled_panel_close_corner.gd` (+186),
`.gitignore` (one line). Checked against `/opt/data/coding_rules.md` and project CLAUDE.md:
typed variables throughout, small focused helpers (`_reserve_content_padding`,
`_close_chip`), guard clauses with ≤2 nesting, debug-gated `[TAG]` log per convention,
surgical diff with doc comments explaining why stylebox margins are widened rather than
shrinking the frame. No scope creep; untracked files are `.gen/` workflow artifacts only.

Quality notes: open entry `manual-test-padding-closable-panels-close-button (iteration 1)`
re-checked against the fresher 03:53 UTC shots — conclusion unchanged, appended a RESOLVED
marker. No new cross-cutting violations found.

## Manual path (plan-required)

Windowed harness `hud_other_panels.json` evidence is fresh (2026-08-23 03:53 UTC, after the
padding fix): four screenshots in `.gen/harness/hud_other_panels/shots/`, vision-inspected
by the checker this iteration. ui_feels_broken: no on every final screenshot.

## Blockers / unverified items

None. All six criteria hold under fresh automated + visual verification.
