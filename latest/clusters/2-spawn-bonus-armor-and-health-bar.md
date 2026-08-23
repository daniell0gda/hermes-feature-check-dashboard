# Cluster 2: spawn-bonus-armor-and-health-bar

- Files: `scripts/game/actors/Enemy.gd`, `scripts/game/SpawnerSystem.gd`, `scripts/ui/EnemyHealthBar.gd`
- Dependencies: 1
- parallel: false

## Acceptance criteria
- While `warlords_doctrine` is active at level N, every spawned enemy receives bonus armor equal to 8%/12%/15% of its max HP for L1/L2/L3.
- Bonus armor is additive on top of innate armor from the enemy config: an enemy that already has innate armor spawns with innate plus bonus, and a normally-unarmored enemy spawns with `armor > 0`.
- When the perk is inactive or removed, enemies spawn with exactly their innate armor (no residual bonus).
- The existing armor bar shows and animates the granted armor on a previously-unarmored enemy while the perk is active.
- Debug-build `[WARLORDS-DOCTRINE]` log line per perk application/spawn-bonus event, naming the perk level and the granted armor amount.

## Verification commands
- Focused test: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 warlords_doctrine"]`
- Full test: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 warlords_doctrine"]`
- Typecheck/build: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 -Editor"]`
