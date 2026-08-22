# Check report: req-136-padding-closable-panels-close-button

Classification: pass

## Verification (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-padding-closable-panels-close-button)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | Godot 4.4.1.stable |
| Typecheck | `--check-only --script res://scripts/ui/hud/TitledPanel.gd` | 0 | clean |
| Import gate | `--headless --path . --import` | 0 | complete (pre-existing invalid-UID warnings only) |
| Focused test | `res://tests/ui/test_titled_panel_close_corner.tscn` | 0 | 31 ok, 0 failed |
| Full suite 1/3 | `res://tests/ui/test_enemy_armor_bar.tscn` | 0 | 35 ok, 0 failed |
| Full suite 2/3 | `res://tests/ui/test_enemy_health_bar_boss_icon.tscn` | 0 | 18 ok, 0 failed |
| Full suite 3/3 | `res://tests/ui/test_enemy_health_bar_oiled_icon.tscn` | 0 | 8 ok, 0 failed |

Manual evidence (manual_testing: required): windowed harness screenshots exist at
`.gen/harness/hud_other_panels/shots/{panel_manage_towers,panel_options,panel_pause_menu,panel_tower_details}.png`.
Visual inspection of panel_manage_towers.png and panel_options.png confirms no content touches or
overlaps the painted ✕; tower details carries no ✕ by design. Headless assertions cover the rect math.

## Criterion-by-criterion

1. Padding reserved so no content intersects CloseChip — PASS: `_reserve_content_padding()` sets
   `frame.offset_right = -CLOSE_CORNER_SIZE.x`; focused test asserts offset and leaf-control rect
   non-intersection at multiple sizes ("no leaf content control intersects the CloseChip rect").
2. Only when closable / scene chip present — PASS: reservation gated on `_close_chip() != null`;
   tests assert plain panel keeps full-width frame (`offset_right == 0`, full-rect anchors intact)
   and scene-placed chip reserves identical padding.
3. Chip flush top-right, exactly one close_requested — PASS: existing assertions "sits flush in the
   top-right corner" and "pressing it emits close_requested once (got 1)".
4. UpgPanel tower-details content clear — PASS: real scenes/UI.tscn instantiated, is_closable set
   pre-_ready, header/badge/note populated, asserted clear at 380x420 and 620x480.
5. Manage Towers + Options clear after layout — PASS: real .tscn instances asserted headless plus
   windowed screenshots visually verified.
6. Debug `[TITLED_PANEL]` log naming panel + inset — PASS: observed in fresh run output
   ("[TITLED_PANEL] UpgPanel reserves 90px of right padding for the close corner").

## Quality findings (changed files: TitledPanel.gd, test_titled_panel_close_corner.gd, .gitignore)

- No violations in new/changed code. Typed variables throughout, small functions, guard clauses,
  debug log per CLAUDE.md convention. Prior quality-note (stray root test-report file) resolved:
  report now under `.gen/test-reports/`, legacy file deleted and gitignored — resolution appended to quality-notes.md.
- Pre-existing noise, not this change's scope: HudTheme.tres/UI.tscn invalid-UID warnings on load.
- New tests do not overlap existing coverage: they add distinct padding/non-closable-layout/
  real-scene assertions to the same dedicated test file.

## Blockers / unverified items

None. All gates green through the runner; no host-shell project commands used.
