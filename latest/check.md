# Check report: req-136-padding-closable-panels-close-button (iteration 4, fresh verification)

classification: pass

## Verification (all via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-padding-closable-panels-close-button)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `git status --short` | 0 | clean tree: M .gitignore, TitledPanel.gd, test_titled_panel_close_corner.gd |
| Typecheck | `godot --headless --path . --check-only --script res://scripts/ui/hud/TitledPanel.gd` | 0 | clean |
| Focused test | `res://tests/ui/test_titled_panel_close_corner.tscn` | 0 | 32 ok, 0 failed; all 11 groups completed |
| Full suite 1/3 | `res://tests/ui/test_enemy_armor_bar.tscn` | 0 | 35 ok, 0 failed |
| Full suite 2/3 | `res://tests/ui/test_enemy_health_bar_boss_icon.tscn` | 0 | 18 ok, 0 failed |
| Full suite 3/3 | `res://tests/ui/test_enemy_health_bar_oiled_icon.tscn` | 0 | 8 ok, 0 failed |

All commands were run fresh this iteration through the approved runner (`run_project_cmd`);
no host-shell project commands and no host `godot` invocation. Pre-existing HudTheme.tres /
UI.tscn invalid-UID warnings fall back to text paths — legacy noise, not this change's scope.

## Criterion-by-criterion

1. Closable panel reserves padding; no content rect intersects CloseChip at any size — PASS.
   `_reserve_content_padding()` duplicates the frame's panel stylebox and widens
   `content_margin_right` by `CLOSE_CORNER_SIZE.x`. Fresh focused run asserts margin ≥ 90
   (got 136) and "no leaf content control intersects the CloseChip rect" at 620x480.
2. Padding only when closable / scene chip present — PASS. Gated on `_close_chip() != null`;
   fresh assertions: "a non-closable panel keeps its full-width content area (20 < 90)",
   "and its full-rect anchors are untouched", and "a scene-placed CloseChip reserves the same
   content margin (136)".
3. Chip flush top-right inside the frame, exactly one `close_requested` per press — PASS.
   Fresh assertions: "it sits flush in the top-right corner (offset 0.0, 0.0)",
   "the ✕ sits inside the full-size frame, not floating outside it",
   "pressing it emits close_requested once (got 1)".
4. UpgPanel tower-details content clear at multiple sizes — PASS. Fresh assertions at both
   380x420 and 620x480: header/badge/stat rows/buttons clear of the chip (real UI.tscn
   instantiated).
5. Manage Towers + Options clear after layout — PASS. Fresh headless assertions for
   ManageTowersPanel.tscn and OptionsScreen.tscn ("keeps every visible content control clear
   of the CloseChip"), plus checker visual inspection of fresh post-fix windowed screenshots
   (20:51 UTC): panel_manage_towers.png — ✕ inside the frame art, no content overlap;
   panel_options.png — ✕ is the frame's own corner ornament, no content beneath it.
6. Debug `[TITLED_PANEL]` log naming panel + inset — PASS. Observed in this iteration's fresh
   output: "[TITLED_PANEL] ManageTowersPanel reserves 90px of right padding for the close
   corner" and "[TITLED_PANEL] UpgPanel reserves 90px of right padding for the close corner".

## Changed-file quality

Diff scope: `scripts/ui/hud/TitledPanel.gd`, `tests/ui/test_titled_panel_close_corner.gd`,
one-line `.gitignore` addition. Checked against /opt/data/coding_rules.md and CLAUDE.md:
typed variables throughout, small focused helpers with doc comments, guard clauses,
debug-gated `[TITLED_PANEL]` log per project convention, surgical diff. No violations in
new/changed code.

New tests do not overlap existing coverage: the five added test groups assert padding
reservation, non-closable layout preservation, scene-placed-chip reservation, real-scene
rect checks, and UpgPanel checks — no prior suite asserted these behaviors on these paths.

Quality notes: the open entry `stray-test-report-file` was already resolved in a prior
iteration (report moved to `res://.gen/test-reports/titled_panel_close_corner.txt`; root
file gitignored) — confirmed still resolved by inspecting the current diff. No new entries.

## Blockers / unverified items

None.
