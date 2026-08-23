# Acceptance Plan: health-bar-never-auto-hides

manual_testing: required

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/ui/test_enemy_armor_bar.tscn"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/menu/test_menu_backdrop_camera.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--check-only", "-s", "res://scripts/ui/EnemyHealthBar.gd"]`

## Clusters

1. spawned-bar-auto-fade — files: `scripts/ui/EnemyHealthBar.gd`, `tests/ui/test_enemy_armor_bar.gd`, `tests/ui/verify_enemy_bar_spawn_fade.gd`, `tests/ui/verify_enemy_bar_spawn_fade.tscn` — depends on: none
- A spawned, undamaged full-health bar is shown immediately after setup and its hide timer is armed to FADE_OUT_DELAY, so the auto-hide branch is reachable on spawn.
- After FADE_OUT_DELAY of frames with no damage, the spawned bar stops being shown, fades out, and hides itself once fully faded.
- The first damage to a spawned enemy whose bar has faded shows the bar again, and a bar below full health is not auto-hidden.
- Both the direct setup path and the deferred setup path (progress bar not yet ready) arm the hide timer, so a bar set up either way fades identically.
- Debug-build [ENEMYHEALTHBAR] log line per auto-hide transition of a spawned bar (existing visibility-transition log covers it; the spawned path must emit the same "auto_hide" line with hp context).
- The menu-backdrop special case in Enemy.gd either stays with its recorded rationale or is removed only after a verified run proves bars self-hide safely there; the existing menu-backdrop camera test still passes either way.

## Criteria

- A spawned, undamaged full-health bar is shown immediately after setup and its hide timer is armed to FADE_OUT_DELAY, so the auto-hide branch is reachable on spawn.
- After FADE_OUT_DELAY of frames with no damage, the spawned bar stops being shown, fades out, and hides itself once fully faded.
- The first damage to a spawned enemy whose bar has faded shows the bar again, and a bar below full health is not auto-hidden.
- Both the direct setup path and the deferred setup path (progress bar not yet ready) arm the hide timer, so a bar set up either way fades identically.
- Debug-build [ENEMYHEALTHBAR] log line per auto-hide transition of a spawned bar (existing visibility-transition log covers it; the spawned path must emit the same "auto_hide" line with hp context).
- The menu-backdrop special case in Enemy.gd either stays with its recorded rationale or is removed only after a verified run proves bars self-hide safely there; the existing menu-backdrop camera test still passes either way.
