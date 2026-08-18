# Acceptance Plan: EnemyHealthBar IconBoss path mismatch

manual_testing: optional

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_health_bar_boss_icon.tscn"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_armor_bar.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. iconboss-path-resolve — files: `scripts/ui/EnemyHealthBar.gd`, `scenes/ui/EnemyHealthBar.tscn`, `tests/ui/test_enemy_health_bar_boss_icon.gd`, `tests/ui/test_enemy_health_bar_boss_icon.tscn` — depends on: none
- Instantiating an enemy health bar does not log Node not found for IconBoss.
- A boss-styled enemy health bar shows the IconBoss control.
- A non-boss enemy health bar keeps IconBoss hidden.
- With the boss icon column present, the HP bar stays centered under the armor bar with the same side margin on both sides.
- Debug-build [ENEMYHEALTHBAR] log line per boss icon visibility update (is_boss, resolved)

## Criteria

- Instantiating an enemy health bar does not log Node not found for IconBoss.
- A boss-styled enemy health bar shows the IconBoss control.
- A non-boss enemy health bar keeps IconBoss hidden.
- With the boss icon column present, the HP bar stays centered under the armor bar with the same side margin on both sides.
- Debug-build [ENEMYHEALTHBAR] log line per boss icon visibility update (is_boss, resolved)
