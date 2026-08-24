# Acceptance Plan: Exposed Plating perk (issue #89) — continuation r2

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
- Typecheck/build: `["godot", "--headless", "--check-only", "--script", "res://scripts/game/status/ExposedStatus.gd"]`

manual_testing: required

Note: all project commands run through `run_project_cmd` with `project=godot-td`, `workspace=poke-defense-godot/issue-exposed-plating`. Fresh post-rebase evidence (2026-08-24): both focused harnesses already pass (`status=pass, exit=0`; results under `.gen/harness/`). Remaining unmet work is windowed player-facing evidence only.

## Clusters

1. exposed-plating-trigger-and-multiplier — files: `autoload/ProgressionManager.gd`, `scripts/progression/global.json`, `scripts/progression/managers/CurseProgressionManager.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `tests/scenarios/exposed_plating_once_per_shield.json` — depends on: none
- The `exposed_plating` perk is registered in the progression config like other global progression perks and is purchasable at exactly 3 levels.
- When an enemy's armor transitions from greater than 0 to 0, the enemy gains the Exposed status exactly once for that shield instance; subsequent hits while armor remains 0 do not re-trigger it.
- After an expired Exposed status ends and the enemy regains armor and loses it again to 0, the Exposed status triggers again.
- While Exposed is active at level 1, damage taken by the affected enemy is multiplied by 1.15; at level 2 by 1.25; at level 3 by 1.35.
- When the Exposed duration for the purchased level elapses (0.5s / 1s / 1.5s), the damage-taken multiplier returns to 1.0 without any further trigger until a new armor breach occurs.
- Debug builds emit a log line with the `[EXPOSED]` prefix on each Exposed trigger (including level and duration) and on each Exposed expiry; release builds do not.
2. exposed-vfx — files: `scripts/game/actors/effects/EffectsManager.gd`, `scripts/game/actors/effects/ExposedVFX.gd`, `scripts/game/status/ExposedStatus.gd`, `tests/scenarios/exposed_plating_vfx.json` — depends on: none
- While the Exposed status is active on an enemy, a visible cracked-shield emissive overlay effect is present on that enemy, following the same lazy-instantiation pattern as the existing burn/oil VFX.
- When the Exposed status expires or the enemy dies, the overlay is removed from that enemy with no leftover nodes or leaked resources.
3. windowed-manual-evidence — files: none — depends on: 1, 2
- Windowed gameplay captures show the Exposed overlay appearing on a real armored enemy at the armor-breach moment and disappearing when the status expires, with no visual glitches to surrounding UI.
- A real-time (30fps) recording of the Exposed VFX on a real enemy exists and plays smoothly, produced from the harness `record_frames` path of `exposed_plating_vfx`.
- A manual UI sanity pass concludes with an explicit `ui_feels_broken: yes|no` verdict recorded in the manual-test report.

## Criteria

- The `exposed_plating` perk is registered in the progression config like other global progression perks and is purchasable at exactly 3 levels.
- When an enemy's armor transitions from greater than 0 to 0, the enemy gains the Exposed status exactly once for that shield instance; subsequent hits while armor remains 0 do not re-trigger it.
- After an expired Exposed status ends and the enemy regains armor and loses it again to 0, the Exposed status triggers again.
- While Exposed is active at level 1, damage taken by the affected enemy is multiplied by 1.15; at level 2 by 1.25; at level 3 by 1.35.
- When the Exposed duration for the purchased level elapses (0.5s / 1s / 1.5s), the damage-taken multiplier returns to 1.0 without any further trigger until a new armor breach occurs.
- Debug builds emit a log line with the `[EXPOSED]` prefix on each Exposed trigger (including level and duration) and on each Exposed expiry; release builds do not.
- While the Exposed status is active on an enemy, a visible cracked-shield emissive overlay effect is present on that enemy, following the same lazy-instantiation pattern as the existing burn/oil VFX.
- When the Exposed status expires or the enemy dies, the overlay is removed from that enemy with no leftover nodes or leaked resources.
- Windowed gameplay captures show the Exposed overlay appearing on a real armored enemy at the armor-breach moment and disappearing when the status expires, with no visual glitches to surrounding UI.
- A real-time (30fps) recording of the Exposed VFX on a real enemy exists and plays smoothly, produced from the harness `record_frames` path of `exposed_plating_vfx`.
- A manual UI sanity pass concludes with an explicit `ui_feels_broken: yes|no` verdict recorded in the manual-test report.
