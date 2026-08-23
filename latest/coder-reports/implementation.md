# Coder report: implementation (spawned-bar-auto-fade)

## Changed files
- `scripts/ui/EnemyHealthBar.gd` — mod
- `tests/ui/test_enemy_armor_bar.gd` — mod
- `tests/ui/verify_enemy_bar_spawn_fade.gd` — new
- `tests/ui/verify_enemy_bar_spawn_faze.tscn` → actual: `tests/ui/verify_enemy_bar_spawn_fade.tscn` — new
- `scripts/game/actors/Enemy.gd` — mod, comment-only rationale

## Criteria (all Done)
- Spawned full-health bar shown immediately after setup with hide timer armed to FADE_OUT_DELAY — Done
- After FADE_OUT_DELAY with no damage bar stops showing, fades, hides itself once fully faded — Done
- First damage re-shows a faded bar; damaged bar is not auto-hidden — Done
- Both direct and deferred setup paths arm the hide timer identically — Done
- Debug [ENEMYHEALTHBAR] "auto_hide"/"show" log lines with hp context on spawned-path transitions — Done (`_log_visibility_transition`, deduped via `last_boss_icon_debug_line`)
- Enemy.gd menu-backdrop special case kept with updated recorded rationale; menu-backdrop test passes — Done

## Commands and results
- `["godot","--headless","--path",".","res://tests/ui/test_enemy_armor_bar.tscn","--log-file",".gen/coder_focused.log"]` — exit code 0; `=== enemy_armor_bar: 41 ok, 0 failed ===` (8 test functions incl. new `_test_spawned_bar_fades_and_reappears_on_first_damage`)
- `["godot","--headless","--path",".","res://tests/menu/test_menu_backdrop_camera.tscn","--log-file",".gen/coder_menu.log"]` — exit code 0; `=== menu_backdrop_camera: 12 ok, 0 failed ===` (exit-time dummy-renderer RID leak warnings are pre-existing harness teardown noise)
- `["godot","--headless","--path",".","--check-only","-s","res://scripts/ui/EnemyHealthBar.gd"]` — exit code 0; clean parse

## Notes
- All work ran through run_project_cmd (project poke-defense-godot, workspace poke-defense-godot/issue-health-bar-never-auto-hides); no host-shell Godot.
- Key mechanism: `show_health_bar()` resets `hide_timer = 0`, so arming must happen AFTER it in both `setup()` and `_deferred_setup()`; otherwise the auto-hide branch (`current_health >= max_health and hide_timer > 0`) never fires for a spawned undamaged bar (the reported bug).
- Windowed real-frame verification evidence from prior iteration preserved in `.gen/verify_spawn_fade.log` ([VERIFY RESULT] PASSED, bar fully faded after ~3.5s of real frames).
