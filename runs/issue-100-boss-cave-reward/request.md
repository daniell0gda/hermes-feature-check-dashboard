# Request: boss-cave-reward-and-spawner-lifetime-tests

Project: godot-td
Workspace: poke-defense-godot/issue-boss-cave-reward-and-spawner-lifetime-tests
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/100
Branch: issue/boss-cave-reward-and-spawner-lifetime-tests

## Problem

Issue #77 promised:
- Defeating a boss cave's boss grants exactly one perk, Unique with 60% probability, Common otherwise, via a reward hook.
- Focused tests covering lifetime-expiry -> chest conversion (40% Unique) and boss-clear reward (60% Unique).

The boss-kill reward was never implemented. The kill path is EnemyHealthController.gd -> Enemy.gd cave-enemy-consumed -> CaveSystem.consume_cave_enemy. That function only removes the enemy record and clears has_enemies. It never checks cave.has_boss and never calls _create_cave_chest / draw_single_cave_perk(0.6).

Spawner-lifetime -> chest conversion (40% Unique) IS implemented in SpawnerSystem.gd / CaveSystem.convert_spawner_to_chest but has no tests.

## Done when

1. Killing a cave boss (cave.has_boss == true) grants exactly one perk, Unique with 60% probability, Common otherwise, via the same draw_single_cave_perk pattern used for spawner-lifetime chests (CaveSystem._create_cave_chest / _populate_cave special_single_perk). Wire a reward hook from consume_cave_enemy or the actual boss-death path.
2. A game-test scenario exercises the boss-kill reward path and asserts Unique/Common split behavior (seeded branches; single_perk_chest_count / granted perk rarity).
3. A game-test scenario exercises spawner-lifetime-expiry -> chest conversion end to end (force wave_lifetime waves, assert has_spawner flips to has_chest with spawner_lifetime_expired = true and the chest's single-perk 40% Unique roll).

## Constraints

- Use run_project_cmd with project=godot-td and this workspace only.
- Native Linux Godot verification. No Windows/PowerShell wrappers as primary gates.
- Do not commit, push, merge, or close the issue.
- Follow /opt/data/coding_rules.md and project CLAUDE.md.
