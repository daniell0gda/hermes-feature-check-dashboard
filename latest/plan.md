# Acceptance Plan: Sci-Fi Overclock perk

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_overclock.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_capacitor_bank.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
- manual_testing: required

## Clusters

1. Overclock perk catalog — files: `scripts/progression/scifi_tower.json`, `scripts/progression/managers/ScifiTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `tests/scenarios/scifi_overclock_progression.json`, `tests/scenarios/progression_chest_pool.json` — depends on: none
- Unowned `scifi_overclock` is an eligible Unique Sci-Fi perk and Sci-Fi beam DPS multiplier is 1.0.
- Applying `scifi_overclock` once owns Unique level 1 and Sci-Fi beam DPS multiplier is 1.4.
- A 100-draw chest includes `scifi_overclock` when a Sci-Fi tower is placed and excludes it when none is placed; `scifi_capacitor_bank` stays independently eligible.
- `tests/scenarios/progression_chest_pool.json` seeded pins are remasured so the scenario still passes after `scifi_overclock` is added to the pool.
2. Overclock beam lockout and tell — files: `scripts/game/actors/towers/ScifiTower.gd`, `scripts/game/actors/projectiles/ScifiTowerProjectile.gd`, `scripts/utils/HighlightShaderUtils.gd`, `tests/scenarios/scifi_overclock.json`, `tests/scenarios/scifi_overclock_visual.json` — depends on: 1
- A Sci-Fi tower placed before the perk is taken deals beam damage at 1.4× unowned DPS once the perk is owned and the beam is firing.
- While unowned, a firing Sci-Fi tower does not enter a 6s no-beam lockout.
- While owned, 6.0s of continuous Sci-Fi beam fire forces a 2.0s lockout with no damaging beam on that tower.
- After the 2.0s lockout the beam may resume against a valid aligned target, and another 6.0s of continuous fire starts another 2.0s lockout.
- If the beam stops before 6.0s of continuous fire, the continuous-fire clock resets and lockout does not start.
- Gameplay pause does not advance the Overclock continuous-fire or lockout clocks.
- During the 2.0s lockout the Sci-Fi tower shows a recharge tell (dim/desaturated beam and/or HighlightShaderUtils tower tint) that is absent while the damaging beam is firing.
- Debug-build [SCIFI_OVERCLOCK] log line per lockout start (tower instance and duration) and per lockout end.
- Non-Sci-Fi towers keep dealing damage while `scifi_overclock` is owned.
- When `scifi_overclock` is unowned, Capacitor Bank still cuts Sci-Fi yaw wait after a retarget.

## Criteria

- Unowned `scifi_overclock` is an eligible Unique Sci-Fi perk and Sci-Fi beam DPS multiplier is 1.0.
- Applying `scifi_overclock` once owns Unique level 1 and Sci-Fi beam DPS multiplier is 1.4.
- A Sci-Fi tower placed before the perk is taken deals beam damage at 1.4× unowned DPS once the perk is owned and the beam is firing.
- While unowned, a firing Sci-Fi tower does not enter a 6s no-beam lockout.
- While owned, 6.0s of continuous Sci-Fi beam fire forces a 2.0s lockout with no damaging beam on that tower.
- After the 2.0s lockout the beam may resume against a valid aligned target, and another 6.0s of continuous fire starts another 2.0s lockout.
- If the beam stops before 6.0s of continuous fire, the continuous-fire clock resets and lockout does not start.
- Gameplay pause does not advance the Overclock continuous-fire or lockout clocks.
- During the 2.0s lockout the Sci-Fi tower shows a recharge tell (dim/desaturated beam and/or HighlightShaderUtils tower tint) that is absent while the damaging beam is firing.
- Debug-build [SCIFI_OVERCLOCK] log line per lockout start (tower instance and duration) and per lockout end.
- Non-Sci-Fi towers keep dealing damage while `scifi_overclock` is owned.
- A 100-draw chest includes `scifi_overclock` when a Sci-Fi tower is placed and excludes it when none is placed; `scifi_capacitor_bank` stays independently eligible.
- `tests/scenarios/progression_chest_pool.json` seeded pins are remasured so the scenario still passes after `scifi_overclock` is added to the pool.
- When `scifi_overclock` is unowned, Capacitor Bank still cuts Sci-Fi yaw wait after a retarget.
