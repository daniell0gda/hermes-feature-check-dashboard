# Check report: req-136-padding-closable-panels-close-button (iteration 2, fresh verification)

Classification: pass

## Verification (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-padding-closable-panels-close-button)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | Godot 4.4.1.stable |
| Typecheck | `--headless --path . --check-only --script res://scripts/ui/hud/TitledPanel.gd` | 0 | clean |
| Focused test | `res://tests/ui/test_titled_panel_close_corner.tscn` | 0 | 31 ok, 0 failed |
| Full suite 1/3 | `res://tests/ui/test_enemy_armor_bar.tscn` | 0 | 35 ok, 0 failed |
| Full suite 2/3 | `res://tests/ui/test_enemy_health_bar_boss_icon.tscn` | 0 | 18 ok, 0 failed |
| Full suite 3/3 | `res://tests/ui/test_enemy_health_bar_oiled_icon.tscn` | 0 | 8 ok, 0 failed |

All commands re-run fresh this iteration through the approved runner; no host-shell
project commands were used. Import gate was exercised in a prior iteration of this
run (exit 0, pre-existing invalid-UID warnings only); the focused and full suites
pass on the current tree, which proves imports resolve.

Manual evidence (manual_testing: required): windowed screenshots at
`.gen/harness/hud_other_panels/shots/{panel_manage_towers,panel_options,panel_pause_menu,panel_tower_details}.png`.
Checker re-inspected panel_manage_towers.png (✕ present, clear breathing room, no
content overlap) and panel_options.png (no top-right ✕ on the Options screen —
its close control is bottom-right, so no corner overlap is possible). Headless
assertions cover the rect math for all panels.

## Criterion-by-criterion

1. Closable TitledPanel reserves padding; no content rect intersects CloseChip at any
   size — PASS: `_reserve_content_padding()` sets `frame.offset_right = -CLOSE_CORNER_SIZE.x`;
   focused test asserts the offset and leaf-control rect non-intersection at multiple
   panel sizes.
2. Padding only when closable / scene chip present; plain panel unchanged — PASS:
   gated on `_close_chip() != null`; tests assert a plain panel keeps
   `offset_right == 0` and full-rect anchors, and a scene-placed chip reserves the
   same padding.
3. Chip flush top-right, exactly one `close_requested` per press — PASS: existing
   assertions "it sits flush in the top-right corner" and "pressing it emits
   close_requested once (got 1)".
4. UpgPanel tower-details content clear — PASS: real `scenes/UI.tscn` instantiated,
   is_closable set pre-_ready, header/badge/note populated, asserted clear at
   380x420 and 620x480.
5. Manage Towers + Options clear after layout — PASS: real `.tscn` instances asserted
   headless; windowed screenshots visually re-verified by the checker.
6. Debug `[TITLED_PANEL]` log naming panel + reserved inset — PASS: observed in this
   iteration's fresh run output ("[TITLED_PANEL] UpgPanel reserves 90px of right
   padding for the close corner").

## Changed-file quality (TitledPanel.gd, test_titled_panel_close_corner.gd, .gitignore)

- No violations in new/changed code. Typed variables, small focused functions, guard
  clauses, debug log per project convention. No scope creep: diff touches only the
  three cluster files. Prior quality-note (stray root test-report file) is resolved
  and recorded in quality-notes.md.
- New tests do not overlap existing coverage: they add distinct padding,
  non-closable-layout, scene-placed-chip, and real-scene assertions to the dedicated
  test file; no other suite asserts this behavior.
- Pre-existing noise, not this change's scope: invalid-UID ext_resource warnings for
  HudTheme.tres / UI.tscn on load.

## Blockers / unverified items

None.
