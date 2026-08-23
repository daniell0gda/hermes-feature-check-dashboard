# Check report: health-bar-never-auto-hides (issue #112) — iteration 1

classification: pass

## Verdict

All three "Done when" criteria verified with fresh runner evidence. The implementor chose the
"keep the fade" option: `setup()` / `_deferred_setup()` in `scripts/ui/EnemyHealthBar.gd` now arm
`hide_timer = FADE_OUT_DELAY` immediately AFTER `show_health_bar()` (which resets it to 0), so a
spawned undamaged full-health bar auto-fades after 2s and reappears on first damage. Decision is
recorded in code comments. `Enemy.gd`'s `is_menu_backdrop` skip was re-examined and kept with an
updated comment rationale (removing it would flash a full-green bar on every menu creature for
FADE_OUT_DELAY) — comment-only change.

## Acceptance criteria evidence

1. Decision recorded in EnemyHealthBar.gd — DONE.
   Evidence: diff shows `hide_timer = FADE_OUT_DELAY` after `show_health_bar()` in both `setup()`
   (line ~129) and `_deferred_setup()` (line ~139), with a comment citing issue #112 and the
   show_health_bar-resets-timer ordering trap.

2. Spawn fade verified on a windowed run; spawn case asserted in tests/ui/test_enemy_armor_bar.gd — DONE.
   Evidence (fresh, this check, all via run_project_cmd):
   - `godot --headless --path . --log-file .gen/check_test_armor_bar.log res://tests/ui/test_enemy_armor_bar.tscn`
     exit 0 — `=== enemy_armor_bar: 41 ok, 0 failed ===`, including the new
     `_test_spawned_bar_fades_and_reappears_on_first_damage` (8 test functions, EXPECTED_TESTS 7->8):
     "setup arms the hide timer to FADE_OUT_DELAY", "after FADE_OUT_DELAY the spawned bar stops being
     shown", "the fully faded spawned bar hides itself", "the first hit shows the bar again".
   - `godot --path . --rendering-method gl_compatibility --audio-driver Dummy --log-file
     .gen/check_spawn_fade.log res://tests/ui/verify_enemy_bar_spawn_fade.tscn` (windowed, real
     frames) exit 0 — `[VERIFY] spawned: is_showing=true hide_timer=2.000000`, `[VERIFY] ok - bar
     fully faded after 3.50s of real frames`, reappear on first hit, `[VERIFY RESULT] PASSED`.
     Debug transitions `[ENEMYHEALTHBAR] show/auto_hide` present in the log.
   - Regression: `godot --headless --path . --log-file .gen/check_menu_backdrop.log
     res://tests/menu/test_menu_backdrop_camera.tscn` exit 0 — `=== menu_backdrop_camera: 12 ok,
     0 failed ===`.

3. Enemy.gd is_menu_backdrop skip re-examined — DONE (kept, rationale recorded in comment).
   The issue allows "remove only if verified safe"; keeping it with a recorded rationale satisfies
   the criterion. The menu-backdrop regression test passes.

## Commands (all through run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-health-bar-never-auto-hides)

- `["godot","--version"]` — exit 0 (4.4.1.stable.official.49a5bc7b6), runner healthy.
- armor-bar suite — exit 0, 41 ok / 0 failed.
- windowed spawn-fade verify scene — exit 0, VERIFY RESULT PASSED.
- menu-backdrop camera regression — exit 0, 12 ok / 0 failed.

## Changed-file quality findings

- New test does not overlap existing coverage: `_test_boss_style_and_fading_survive_the_armor_row`
  covers damage-driven fade in/out; the new test covers the spawn auto-hide path (`hide_timer`
  armed at setup) which no prior test asserted.
- New code follows project rules: typed vars, debug-only `[ENEMYHEALTHBAR]` transition logs per
  CLAUDE.md logging rule, surgical diff (5 files, one comment-only).
- Minor (advisory, not demoting): `_log_visibility_transition` reuses the
  `last_boss_icon_debug_line` field as its dedupe cache — a misnamed shared field, harmless here.

## Blockers

None.

## Unverified items

None. No full-project typecheck/build command exists for this Godot project beyond scene parse
(both scenes parsed and ran cleanly headless and windowed).
