# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/progression/global.json` — new `exposed_plating` Common perk, 3 levels (value/duration per level)
- `scripts/progression/managers/CurseProgressionManager.gd` — handles `exposed_plating`: `_apply_exposed_plating`, `get_exposed_plating_config`, reset/init wiring
- `autoload/ProgressionManager.gd` — passthrough getter `get_exposed_plating_config()`
- `scripts/game/status/ExposedStatus.gd` — NEW: timed Exposed mark (BurnStatus pattern), `[EXPOSED]` trigger/expire logs gated by OS.is_debug_build()
- `scripts/game/actors/effects/ExposedVFX.gd` — NEW: amber cracked-shield pulsing wash shell (StaticBreachVFX pattern; own meshes + material_override, never the enemy model slot)
- `scripts/game/actors/effects/EffectsManager.gd` — lazy `show_exposed()` / `hide_exposed()` (BurnVFX/OilVFX pattern)
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — trigger in `_consume_armor()` on the armor >0 -> 0 transition; `exposed_damage_multiplier()` applied to hit_damage in `take_damage`
- `scripts/testing/HarnessValues.gd` — `enemy.exposed`, `enemy.exposed_multiplier`, enemies-report `exposed_count`
- `tests/scenarios/exposed_plating_once_per_shield.json` — NEW headless scenario

## Criteria (issue #89 acceptance)
1. Perk registered like other progression perks, purchasable at 3 levels — Done (global.json; apply_progression ladder exercised L1->L2->L3 in the run)
2. Trigger exactly once per shield instance (>0 -> 0 only) — Done (`_consume_armor` guard `before > 0.0 and enemy.armor <= 0.0`; second hit at armor 0 does not re-fire; re-trigger requires regained armor)
3. Damage multiplier per level for duration then expires cleanly — Done (L1 1.15 / L2 1.25 / L3 1.35 asserted via exact hp deltas; expiry asserted by baseline hp drop and exposed_multiplier == 1.0)
4. New VFX ExposedStatus/ExposedVFX in scripts/game/actors/effects following BurnStatus/BurnVFX, lazily instantiated by EffectsManager — Done
5. `[EXPOSED]` debug logs on trigger and expiry gated by OS.is_debug_build() — Done

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit 0 (import + class cache; new ExposedStatus class_name registered)
- `godot --headless --path . res://scenes/Main.tscn --audio-driver Dummy -- --harness=res://tests/scenarios/exposed_plating_once_per_shield.json` — exit 0, harness status=pass, all 6 expectations pass
  - Breaching hit hp deltas: L1 1625->1614 (10x1.15=11), L2 ->1613 (12), L3 ->1612 (13) — same-hit amplification proves the window opens inside the breaching hit
  - Second armor_hit while armor==0 still amplified but does NOT re-trigger (no duplicate [EXPOSED] triggered lines; one per leg)
  - After 2s wait: exposed=false, next hit unamplified (hp -10 exactly), exposed_multiplier == 1.0, "[EXPOSED] expire on Orc Enemy_boss" logged

## Notes
- The breaching hit itself is amplified because take_damage multiplies after `_consume_armor` — deliberate and covered by exact-delta assertions.
- Static Breach's `consume_static_breach_armor()` zeroes armor before HP damage; that path also flows through take_damage so an Exposed window opened earlier still applies, and a breach that itself zeroes armor does NOT fire Exposed (it bypasses `_consume_armor`). If issue intent differs there, flag back.
- Manual/windowed VFX verification (screenshot or record_frames GIF of ExposedVFX on a real enemy) NOT done here — player-facing visual check left to the tester per verification requirements. Headless cannot capture pixels. End with ui_feels_broken pass.
- Runner invocation needs the explicit scene arg `res://scenes/Main.tscn` before `--` (Run-Scenario.ps1 equivalent); without it the harness times out waiting for the game scene.
\n