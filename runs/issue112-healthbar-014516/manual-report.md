# Manual Test Report – health-bar-never-auto-hides (issue #112)

## Summary

- Result: PASSED
- Tested on: 2026-08-23, windowed Godot 4.4.1 (gl_compatibility + Dummy audio) via run_project_cmd, project poke-defense-godot, workspace issue-health-bar-never-auto-hides
- Scenario: .gen/ui_scenario.md
- Tester: Manual-tester profile

Overall: Walked the three-beat spawn/fade story on real engine frames using the windowed verify scene `res://tests/ui/verify_enemy_bar_spawn_fade.tscn`. A freshly spawned undamaged bar shows at full health immediately, fades completely away ~3.5 s later with no damage, and reappears partially depleted on the first hit and stays shown. All three beats are proven by distinct rendered PNGs plus the engine's own [VERIFY]/[ENEMYHEALTHBAR] log lines (`[VERIFY RESULT] PASSED`, exit 0).

## How it was exercised

Windowed run through the approved runner (no --headless):

```
godot --path . --rendering-method gl_compatibility --audio-driver Dummy \
  --log-file .gen/manual_verify15.log res://tests/ui/verify_enemy_bar_spawn_fade.tscn
```

To make the bar visible to a camera for screenshots I extended the existing verify scene's script additively (camera + WorldEnvironment, window set to 640x360, three `_shot()` captures saved under `.gen/screenshots/`). No production script was touched.

Key log evidence (.gen/manual_verify15.log):

- `[ENEMYHEALTHBAR] show is_showing false -> true hp=100/100` — spawned path shows the bar
- `[VERIFY] spawned: is_showing=true hide_timer=2.000000` — hide timer armed to FADE_OUT_DELAY=2.0
- `[ENEMYHEALTHBAR] auto_hide is_showing true -> false hp=100/100` — auto-hide fired on full health
- `[VERIFY] ok - bar fully faded after 3.50s of real frames`
- `[ENEMYHEALTHBAR] show is_showing false -> true hp=80/100` — first damage re-shows the bar
- `[VERIFY] ok - bar reappeared on first damage, still shown after 0.61s`
- `[VERIFY] ok - bar stayed shown for 1.00s after damage` — damaged bar does not auto-hide
- `[VERIFY RESULT] PASSED`, process exit 0

## Criteria

- A spawned, undamaged full-health bar is shown immediately after setup and its hide timer is armed to FADE_OUT_DELAY
  - ![spawn full bar](screenshots/bar_spawn_full.png)
  - Log: `is_showing=true hide_timer=2.000000` right after `setup(100)`
- After FADE_OUT_DELAY of frames with no damage the bar fades out and hides itself once fully faded
  - ![bar faded](screenshots/bar_faded.png)
  - Log: `auto_hide is_showing true -> false hp=100/100`, `fully faded after 3.50s` (alpha 0.014 ≈ fully transparent)
- The first damage to a spawned enemy whose bar has faded shows the bar again, and a bar below full health is not auto-hidden
  - ![damaged bar reappears](screenshots/bar_damaged_reappear.png)
  - Log: `show ... hp=80/100`, `reappeared on first damage, still shown after 0.61s`, `stayed shown for 1.00s after damage`; shot shows green fill at ~80% with dark remainder
- Direct vs deferred setup both arm the hide timer — logic-verified by the focused suite `tests/ui/test_enemy_armor_bar.tscn`: `41 ok, 0 failed` (coder gate, re-run this iteration); not separately shown in a still (both paths are internal state)
- Debug-build [ENEMYHEALTHBAR] auto_hide line with hp context — confirmed verbatim in `.gen/manual_verify15.log`
- Menu-backdrop special case stays; menu regression `tests/menu/test_menu_backdrop_camera.tscn`: `12 ok, 0 failed` exit 0 (coder gate re-run)

## Issues and Observations

- Low: the standalone verify scene has no enemy/camera of its own, so its default window is a blank frame; screenshots required adding a camera+environment to the test script. Test-scene-only, no player impact.
- Low: repeated script errors `Invalid call. Nonexistent 'float' constructor.` from `EnemyHealthBar.gd:_update_status_icons/_update_armor_bar` when the bar's parent has no enemy fields (only happens in this bare test scene; parent lookups return null → float(null)). Cosmetic log noise in tests, but worth a null-guard someday.
- Note: fade-out takes ~3.5 s wall time (2.0 s delay + fade speed), slightly longer than the "about 2s" phrasing in the focus; matches FADE_OUT_DELAY + FADE_OUT_SPEED math, so behavior is per design.

## Recommendation

Ready. The player-visible spawn/fade story works exactly as planned on real frames; all six cluster criteria are covered (three by stills here, three by the coder's focused/menu gates re-run green).
