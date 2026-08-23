# Acceptance Plan: warlords_doctrine progression perk

## Verification

- Focused test: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 warlords_doctrine"]`
- Full test: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 warlords_doctrine"]`
- Typecheck/build: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 -Editor"]`

## Clusters

1. perk-definition-and-damage-bonus — files: `scripts/progression/global.json`, `autoload/ProgressionManager.gd` — depends on: none
- The global progression pool contains a Common perk named `warlords_doctrine` with exactly 3 levels whose values encode +5%/+9%/+14% tower damage for L1/L2/L3.
- After applying `warlords_doctrine` at L1/L2/L3, the global tower damage multiplier equals 1.05/1.09/1.14 respectively, and it stacks additively with the existing `tower_dmg` perk rather than overriding its contribution.
- Taking and removing (`give`/`take`) the perk updates the applied level and the resulting damage multiplier consistently with how existing Common global perks behave, and resetting progression clears its effect to 1.0.
2. spawn-bonus-armor-and-health-bar — files: `scripts/game/actors/Enemy.gd`, `scripts/game/SpawnerSystem.gd`, `scripts/ui/EnemyHealthBar.gd` — depends on: 1
- While `warlords_doctrine` is active at level N, every spawned enemy receives bonus armor equal to 8%/12%/15% of its max HP for L1/L2/L3.
- Bonus armor is additive on top of innate armor from the enemy config: an enemy that already has innate armor spawns with innate plus bonus, and a normally-unarmored enemy spawns with `armor > 0`.
- When the perk is inactive or removed, enemies spawn with exactly their innate armor (no residual bonus).
- The existing armor bar shows and animates the granted armor on a previously-unarmored enemy while the perk is active.
- Debug-build `[WARLORDS-DOCTRINE]` log line per perk application/spawn-bonus event, naming the perk level and the granted armor amount.
3. harness-scenarios — files: `tests/scenarios/warlords_doctrine.json`, `tests/scenarios/enemy_armor_ballista.json` — depends on: 1, 2
- A `game-test` scenario activates `warlords_doctrine`, spawns a normally-unarmored enemy, and asserts `enemy.armor > 0` (and matching `max_armor`) at spawn; headless result is `pass`.
- The same scenario asserts the tower damage bonus end-to-end through the harness at one perk level (a scripted hit applies 1.05x/1.09x/1.14x the base damage).
- The pre-existing `enemy_armor_ballista` scenario still passes unchanged after the feature lands (armor arithmetic regression).

manual_testing: required

## Criteria

- The global progression pool contains a Common perk named `warlords_doctrine` with exactly 3 levels whose values encode +5%/+9%/+14% tower damage for L1/L2/L3.
- After applying `warlords_doctrine` at L1/L2/L3, the global tower damage multiplier equals 1.05/1.09/1.14 respectively, and it stacks additively with the existing `tower_dmg` perk rather than overriding its contribution.
- Taking and removing (`give`/`take`) the perk updates the applied level and the resulting damage multiplier consistently with how existing Common global perks behave, and resetting progression clears its effect to 1.0.
- While `warlords_doctrine` is active at level N, every spawned enemy receives bonus armor equal to 8%/12%/15% of its max HP for L1/L2/L3.
- Bonus armor is additive on top of innate armor from the enemy config: an enemy that already has innate armor spawns with innate plus bonus, and a normally-unarmored enemy spawns with `armor > 0`.
- When the perk is inactive or removed, enemies spawn with exactly their innate armor (no residual bonus).
- The existing armor bar shows and animates the granted armor on a previously-unarmored enemy while the perk is active.
- Debug-build `[WARLORDS-DOCTRINE]` log line per perk application/spawn-bonus event, naming the perk level and the granted armor amount.
- A `game-test` scenario activates `warlords_doctrine`, spawns a normally-unarmored enemy, and asserts `enemy.armor > 0` (and matching `max_armor`) at spawn; headless result is `pass`.
- The same scenario asserts the tower damage bonus end-to-end through the harness at one perk level (a scripted hit applies 1.05x/1.09x/1.14x the base damage).
- The pre-existing `enemy_armor_ballista` scenario still passes unchanged after the feature lands (armor arithmetic regression).

Manual testing note: windowed run required (no `--headless`) for the UI-sanity criterion (perk visible/selectable in the progression draw and the armor row appearing over a fresh unarmored enemy); see `.gen/ui_scenario.md`.
