# Cluster 1: exposed-plating-trigger-and-multiplier

- Files: `autoload/ProgressionManager.gd`, `scripts/progression/global.json`, `scripts/progression/managers/CurseProgressionManager.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `tests/scenarios/exposed_plating_once_per_shield.json`
- Dependencies: none
- Parallel: true

## Acceptance criteria

- The `exposed_plating` perk is registered in the progression config like other global progression perks and is purchasable at exactly 3 levels.
- When an enemy's armor transitions from greater than 0 to 0, the enemy gains the Exposed status exactly once for that shield instance; subsequent hits while armor remains 0 do not re-trigger it.
- After an expired Exposed status ends and the enemy regains armor and loses it again to 0, the Exposed status triggers again.
- While Exposed is active at level 1, damage taken by the affected enemy is multiplied by 1.15; at level 2 by 1.25; at level 3 by 1.35.
- When the Exposed duration for the purchased level elapses (0.5s / 1s / 1.5s), the damage-taken multiplier returns to 1.0 without any further trigger until a new armor breach occurs.
- Debug builds emit a log line with the `[EXPOSED]` prefix on each Exposed trigger (including level and duration) and on each Exposed expiry; release builds do not.

## Verification

Run via `run_project_cmd` (`project=godot-td`, `workspace=poke-defense-godot/issue-exposed-plating`):

```json
["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]
```

Status (fresh, post-rebase 2026-08-24): pass, exit 0 — `.gen/harness/exposed_plating_once_per_shield/result.json`.
