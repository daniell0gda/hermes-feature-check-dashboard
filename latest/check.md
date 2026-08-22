# Check report: req-136-padding-closable-panels-close-button (iteration 3, fresh verification)

classification: pass

## Verification (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-padding-closable-panels-close-button)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | Godot 4.4.1.stable.official.49a5bc7b6 |
| Typecheck | `--headless --path . --check-only --script res://scripts/ui/hud/TitledPanel.gd` | 0 | clean |
| Focused test | `res://tests/ui/test_titled_panel_close_corner.tscn` | 0 | 32 ok, 0 failed |
| Full suite 1/3 | `res://tests/ui/test_enemy_armor_bar.tscn` | 0 | 35 ok, 0 failed |
| Full suite 2/3 | `res://tests/ui/test_enemy_health_bar_boss_icon.tscn` | 0 | 18 ok, 0 failed |
| Full suite 3/3 | `res://tests/ui/test_enemy_health_bar_oiled_icon.tscn` | 0 | 8 ok, 0 failed |

All commands re-run fresh this iteration through the approved runner (`run_project_cmd`);
no host-shell project commands were used and no host `godot` was invoked. The focused
suite passing proves imports resolve on this tree (pre-existing HudTheme.tres / UI.tscn
invalid-UID warnings fall back to text paths — legacy noise, not this change's scope).

## Manual evidence (manual_testing: required)

Windowed screenshots at `.gen/harness/hud_other_panels/shots/{panel_manage_towers,panel_options,panel_pause_menu,panel_tower_details}.png`.
Checker re-inspected panel_manage_towers.png (✕ present in the panel's top-right,
clear breathing room, no content overlap) and panel_options.png (no corner ✕ on the
rendered Options screen — no corner overlap possible). Headless assertions cover the
rect math for all closable panels including the scene-placed-chip path.

## Criterion-by-criterion

1. Closable TitledPanel reserves padding; no content rect intersects CloseChip at any
   size — PASS: `_reserve_content_padding()` widens the frame stylebox's
   `content_margin_right` by `CLOSE_CORNER_SIZE.x`; focused test asserts the margin
   (got 136 ≥ 90) and leaf-control rect non-intersection at multiple sizes.
2. Padding only when closable / scene chip present; plain panel unchanged — PASS:
   gated on `_close_chip() != null`; tests assert a plain panel keeps
   `content_margin_right < 90` (got 20) and full-rect anchors untouched, and a
   scene-placed chip reserves the same margin.
3. Chip flush top-right, exactly one `close_requested` per press — PASS: assertions
   "it sits flush in the top-right corner (offset 0.0, 0.0)" and "pressing it emits
   close_requested once (got 1)".
4. UpgPanel tower-details content clear — PASS: real `scenes/UI.tscn` instantiated,
   is_closable set pre-_ready, header/badge/note populated, asserted clear at both
   380x420 and 620x480.
5. Manage Towers + Options clear after layout — PASS: real `.tscn` instances asserted
   headless ("ManageTowersPanel.tscn keeps every visible content control clear",
   "OptionsScreen.tscn ... clear"); screenshots visually re-verified by the checker.
6. Debug `[TITLED_PANEL]` log naming panel + reserved inset — PASS: observed in this
   iteration's fresh run output for built panels plus named instances
   ("[TITLED_PANEL] UpgPanel reserves 90px of right padding for the close corner").

## Changed-file quality (TitledPanel.gd, test_titled_panel_close_corner.gd, .gitignore)

- No violations in new/changed code against /opt/data/coding_rules.md and CLAUDE.md:
  typed variables throughout, small focused functions, guard clauses, debug-only
  `[TITLED_PANEL]` log per project convention, surgical diff limited to the cluster
  files plus a one-line gitignore addition for the legacy scratch report name.
- New tests do not overlap existing coverage: they add distinct padding,
  non-closable-layout, scene-placed-chip, real-scene, and UpgPanel assertions to the
  dedicated test file; no other suite asserts this behavior on these code paths.
- Prior quality-note (stray root test-report file) remains resolved; report now goes
  to `res://.gen/test-reports/titled_panel_close_corner.txt`.

## Blockers / unverified items

None.
