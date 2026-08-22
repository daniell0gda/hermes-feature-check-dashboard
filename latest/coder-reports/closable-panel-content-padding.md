# Coder report: closable-panel-content-padding

## Changed files
- `scripts/ui/hud/TitledPanel.gd` — mod: `_reserve_content_padding()` widens a duplicated frame stylebox's right (+90px) and top (+103px) content margins whenever a CloseChip exists; `[TITLED_PANEL]` debug log names the panel and inset; CloseChip flush top-right inside full-size frame art.
- `scripts/ui/UI.gd` — mod: UpgPanel gets `is_closable = true` behavior via scene flag wiring — connects `close_requested` to new `_on_upg_panel_close_requested()` which drops the selection.
- `scenes/UI.tscn` — mod: `Root/UpgPanel` sets `is_closable = true`.
- `tests/ui/test_titled_panel_close_corner.gd` + `.tscn` — mod/new tests: chip presence/theme/size/flush placement, single close_requested, frame variation swap, stylebox margin reservation, non-closable panel unchanged, Manage Towers / Options / real UI.tscn UpgPanel leaf-rect overlap checks at two sizes.
- `.gitignore` — mod: legacy repo-root test report path ignored.

## Criteria
All six cluster criteria — Done (see `.gen/status.md`, already marked Done by prior iterations).

## Commands and results
- Focused `godot --headless --path . res://tests/ui/test_titled_panel_close_corner.tscn` — exit 0; 33 ok / 0 failed (11 of 11 groups ran to completion).
- Full `test_enemy_armor_bar.tscn` — exit 0; 35 ok / 0 failed.
- Full `test_enemy_health_bar_boss_icon.tscn` — exit 0; 18 ok / 0 failed.
- Full `test_enemy_health_bar_oiled_icon.tscn` — exit 0; 8 ok / 0 failed.
- Typecheck/import `godot --headless --path . --import` — exit 0.
- Manual windowed harness `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json` — exit 0, status=pass; fresh screenshots captured 21:31 UTC in `.gen/harness/hud_other_panels/shots/`.

## Notes
- Vision inspection of the four fresh shots: ui_feels_broken: no on manage_towers, options, pause_menu, tower_details. No content touches or overlaps any ✕; chips sit flush inside the frame art corner.
- The tower details (UpgPanel) panel DOES carry a ✕ by design in this change (is_closable=true on UpgPanel; close drops selection). A reviewer noting "tower details shouldn't have an x" should read scenes/UI.tscn diff first — it is intentional per this issue's fix.
- Pre-existing noise, unrelated to this change: HudTheme.tres invalid-UID warnings (text-path fallback works), GLB load failures for stylized_earth_in_clouds.glb / portal_fantasy_arch.glb / ruined_house.glb in fresh import caches, GLES leak-at-exit errors after harness quit.
- Gotcha carried from earlier iterations: set `is_closable` BEFORE add_child for scene-instantiated panels (chip is grown in `_ready()`); scene scripts extend TitledPanel by path so class identity checks must use `get_base_script()`.
