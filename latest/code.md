# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/ui/UI.gd` — heart-beat animation on the HUD egg icon (mod)
- `scripts/testing/HarnessValues.gd` — new `hud` value source (`egg_icon_scale_ratio.x/.y`) (mod)
- `scripts/testing/HarnessActions.gd` — new `damage_egg` action (mod)
- `tests/scenarios/hud_heart_beat_on_egg_damage.json` — new focused scenario (new)

## Criteria
- Each egg HP decrease triggers a scale-up-and-return beat on the HUD egg/heart icon — Done
  (`_setup_egg_beat` / `_on_egg_changed_for_beat` / `_play_heart_beat` in `scripts/ui/UI.gd`;
  tween to base*1.3 in 0.15s, back to base in 0.25s)
- After every beat the icon is exactly back at its original base scale (no drift) — Done
  (final tween target is the exact captured `_egg_icon_base_scale` Vector2; harness asserts
  ratio == 1.0 after each beat)
- Rapid consecutive decreases do not stack or break the animation — Done
  (each hit resets scale to base and kills any in-flight tween before restarting; scenario
  applies two back-to-back hits and still settles at exactly 1.0)
- Non-decrease egg_changed emissions do not trigger a beat — Done
  (handler tracks `_last_seen_egg_hp`; only a strict decrease beats; scenario applies
  amount -1 and asserts no beat + no log line)
- Debug-build [HUD] log line naming old and new egg HP — Done
  (`[HUD] heart beat: egg HP <old> -> <new>`, OS.is_debug_build()-gated; asserted by regex)
- Harness can read the HUD heart icon's current scale during a run — Done
  (`source: "hud"`, fields `egg_icon_scale_ratio.x/.y`, backed by `UI.get_egg_icon_scale_ratio()`)
- Focused headless scenario applies two HP decreases, observes rise above base and return,
  finishes status pass with all expectations green — Done

## Commands and results
- `godot --headless --editor --quit-after 300 --path .` — exit code 0; script scan clean
  (only pre-existing theme/scene UID warnings)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_heart_beat_on_egg_damage.json`
  — exit code 0; `[Harness] status=pass exit=0`; all 5 expectations pass (egg_hp==17,
  scale ratios x/y == 1.0, both [HUD] heart beat log lines matched); result at
  `.gen/harness/hud_heart_beat_on_egg_damage/result.json`
- Full test `... --harness=res://tests/scenarios/smoke_placement.json` — exit code 0;
  `[Harness] status=pass exit=0`

## Notes
- Gotcha: `GameState._set_egg` emits `egg_changed` AFTER assigning `egg_hp`, so a decrease can
  only be detected against a locally tracked previous value, not `GameState.egg_hp`.
- The beat scales only the Icon TextureRect inside the Egg HudStat instance; the scene file
  itself was not modified.
- Manual windowed screenshot verification (ui_scenario.md) is left to the tester per plan's
  manual_testing: required.
\n