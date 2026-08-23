## ✅ Done
- Decision recorded in `scripts/ui/EnemyHealthBar.gd`: keep the fade — `setup()` and `_deferred_setup()` arm `hide_timer = FADE_OUT_DELAY` after `show_health_bar()` (issue #112 decision in code comments)
- Spawned bar fades out after FADE_OUT_DELAY and reappears on first damage, verified on a windowed run (`tests/ui/verify_enemy_bar_spawn_fade.tscn`: VERIFY RESULT PASSED, real frames) and asserted in `tests/ui/test_enemy_armor_bar.gd` (`_test_spawned_bar_fades_and_reappears_on_first_damage`; suite 41 ok, 0 failed, exit 0)
- `Enemy.gd` `is_menu_backdrop` skip re-examined and kept with recorded rationale (comment-only change); menu-backdrop regression `tests/menu/test_menu_backdrop_camera.tscn` passes (12 ok, 0 failed, exit 0)

## ⬜ Pending
- (none)

## ❌ Impossible
- (none)
