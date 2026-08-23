# Check report: health-bar-never-auto-hides (issue #112) — iteration 1

classification: pass

## Verdict

All three "Done when" criteria verified with fresh runner evidence (this check, all through
run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-health-bar-never-auto-hides;
no host-shell Godot). The implementor chose the "keep the fade" option: `setup()` and
`_deferred_setup()` in `scripts/ui/EnemyHealthBar.gd` arm `hide_timer = FADE_OUT_DELAY` immediately
AFTER `show_health_bar()` (which resets it to 0), with the ordering trap documented in code comments —
decision recorded in code. `Enemy.gd`'s `is_menu_backdrop` skip was re-examined and kept with an
updated recorded rationale (comment-only change).

## Acceptance criteria evidence

1. Decision recorded in EnemyHealthBar.gd — DONE.
   Evidence: `git diff` (via runner) shows `hide_timer = FADE_OUT_DELAY` after `show_health_bar()`
   in both `setup()` and `_deferred_setup()`, with a comment citing issue #112 and explaining that
   show_health_bar resets hide_timer to 0.

2. Spawned bar fades after FADE_OUT_DELAY and reappears on first damage; asserted in
   tests/ui/test_enemy_armor_bar.gd — DONE. Fresh evidence:
   - Parse/type gate: `godot --headless --path . --check-only -s res://scripts/ui/EnemyHealthBar.gd`
     exit 0, clean parse.
   - Focused suite: `godot --headless --path . --log-file .gen/check_test_armor_bar.log
     res://tests/ui/test_enemy_armor_bar.tscn` exit 0 — `=== enemy_armor_bar: 41 ok, 0 failed ===`,
     including the new `_test_spawned_bar_fades_and_reappears_on_first_damage` asserting:
     timer armed to FADE_OUT_DELAY (2.0), bar stops being shown after FADE_OUT_DELAY, fully faded bar
     hides itself, first hit shows the bar again, damaged bar not auto-hidden.
   - Windowed real-frame verification:
     `godot --path . --rendering-method gl_compatibility --audio-driver Dummy --log-file
     .gen/check_spawn_fade.log res://tests/ui/verify_enemy_bar_spawn_fade.tscn` exit 0 —
     `[VERIFY] spawned: is_showing=true hide_timer=2.000000`, `[VERIFY] ok - bar fully faded after
     3.50s of real frames`, `[VERIFY] ok - bar stayed shown for 1.00s after damage`,
     `[VERIFY RESULT] PASSED`. Debug transitions `[ENEMYHEALTHBAR] show`/`auto_hide` with hp context
     present in the log (criterion 4 satisfied).
   - Regression: menu-backdrop camera test exit 0 — `=== menu_backdrop_camera: 12 ok, 0 failed ===`.

3. Both direct and deferred setup paths arm the hide timer — DONE. Diff shows the identical
   `show_health_bar(); hide_timer = FADE_OUT_DELAY` sequence in both paths; focused suite passes.

4. Debug [ENEMYHEALTHBAR] auto_hide log line per transition with hp context — DONE.
   `_log_visibility_transition("auto_hide", ...)` emits `[ENEMYHEALTHBAR] auto_hide is_showing true
   -> false hp=100/100` in debug builds; observed in both fresh logs; deduped against repeats.

5. Enemy.gd is_menu_backdrop skip kept with recorded rationale — DONE (comment-only diff; rationale
   updated for the new behavior). Menu-backdrop regression passes.

6. Test overlap check: the new test covers the spawn auto-hide path (`hide_timer` armed at setup).
   The existing `_test_boss_style_and_fading_survive_the_armor_row` covers only damage-driven fade;
   no prior test asserted the spawn path. EXPECTED_TESTS bumped 7 -> 8 in the same change. No overlap.

## Commands (fresh, this check)

- `git status --short` via runner — exit 0 (5 changed/new files as claimed).
- `[godot, --headless, --path, ., --check-only, -s, res://scripts/ui/EnemyHealthBar.gd]` — exit 0.
- `[godot, --headless, --path, ., --log-file, .gen/check_test_armor_bar.log,
  res://tests/ui/test_enemy_armor_bar.tscn]` — exit 0, 41 ok / 0 failed.
- `[godot, --path, ., --rendering-method, gl_compatibility, --audio-driver, Dummy, --log-file,
  .gen/check_spawn_fade.log, res://tests/ui/verify_enemy_bar_spawn_fade.tscn]` — exit 0,
  VERIFY RESULT PASSED.
- `[godot, --headless, --path, ., --log-file, .gen/check_menu_backdrop.log,
  res://tests/menu/test_menu_backdrop_camera.tscn]` — exit 0, 12 ok / 0 failed.

## Changed-file quality findings

New/changed code follows project rules: typed vars, debug-only `[TAG]` state-transition logging per
CLAUDE.md, surgical diff (5 files, one comment-only), no scope creep beyond the issue's three items.
Minor advisory item recorded in quality-notes.md (misnamed shared dedupe cache field); not demoting.

## Blockers

None.

## Unverified items

None. No full-project typecheck/build command exists for this Godot project beyond script parse
(check-only passed) plus scene execution (both suites ran cleanly headless; verify scene ran windowed).
