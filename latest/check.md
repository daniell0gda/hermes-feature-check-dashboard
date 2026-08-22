# Check report: req-136-padding-closable-panels-close-button (iteration check)

Classification: **fixable**

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-padding-closable-panels-close-button)

- `godot --version` — exit 0 (4.4.1.stable.official.49a5bc7b6)
- `godot --headless --path . --import` — exit 0 (UID warnings only, pre-existing theme UID staleness)
- `godot --headless --path . --check-only --script res://scripts/ui/hud/TitledPanel.gd` — exit 0
- `godot --headless --path . res://tests/ui/test_titled_panel_close_corner.tscn` — exit 0; "29 ok, 0 failed"
- `godot --headless --path . res://tests/ui/test_enemy_armor_bar.tscn` — exit 0; "35 ok, 0 failed"
- `godot --headless --path . res://tests/ui/test_enemy_health_bar_boss_icon.tscn` — exit 0; "18 ok, 0 failed"
- `godot --headless --path . res://tests/ui/test_enemy_health_bar_oiled_icon.tscn` — exit 0; "8 ok, 0 failed"

## Acceptance criteria evidence

1. Closable panel reserves padding so no content control intersects the CloseChip rect — PASS. `_reserve_content_padding()` in `scripts/ui/hud/TitledPanel.gd` pulls `Frame.offset_right` to `-CLOSE_CORNER_SIZE.x`; test asserts offset <= -90 and leaf-rect overlap check passes.
2. Padding applies only when closable / scene-placed chip exists — PASS. Tests: non-closable frame keeps `offset_right == 0` and full-rect anchors; scene-placed CloseChip reserves same padding.
3. CloseChip flush top-right and emits exactly one `close_requested` — PASS ("it sits flush in the top-right corner", "pressing it emits close_requested once (got 1)").
4. UpgPanel-style tower details content clear of chip — NOT PROVEN. The focused test covers synthetic closable panels plus ManageTowersPanel/OptionsScreen scenes, but never instantiates the actual UpgPanel scene; plan marks manual_testing: required and no windowed screenshots were produced. Moved to Pending.
5. Manage Towers + Options keep content clear of chip — PASS (`_test_real_closable_scenes_keep_content_clear_of_the_chip`, both scenes).
6. Debug `[TITLED_PANEL]` log naming panel and inset — PASS (log lines visible in headless run output: "reserves 90px of right padding for the close corner").

## Changed-file quality

- `scripts/ui/hud/TitledPanel.gd`: small, typed, documented helpers; debug-only log per CLAUDE.md convention. No violations found.
- `tests/ui/test_titled_panel_close_corner.gd`: new tests assert real criteria (rect overlap math), not smoke loads. No duplicate coverage of existing tests found in the suite. Minor note (non-blocking): `_content_rects_outside_chip` only fails on overlapping *leaf* controls (childless), so a container that fully contains the chip region but has its own visual could slip through; acceptable for this criterion.

## Scope creep / quality notes

- Untracked stray file `.gen-test-report.txt` at repo root written by the test script into `res://`. Should live under `.gen/` per coding rules; advisory only, not a criterion demotion.
- No prior open quality-notes entries existed.

## Blockers

None. Runner reachable, all gates green. Remaining work is small: add an UpgPanel-scene-based assertion (or produce the required windowed screenshots) for criterion 4, then re-run the focused test.
