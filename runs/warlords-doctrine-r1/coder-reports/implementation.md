# Coder report: implementation

## Changed files
- `scripts/progression/global.json` — mod: added Common global perk `warlords_doctrine` (3 levels, `value` 0.05/0.09/0.14 tower-damage ratios, `armor_bonus` 0.08/0.12/0.15), inserted directly after `tower_dmg`.
- `autoload/ProgressionManager.gd` — mod: new `_warlords_damage_ratio` aggregate added additively inside `get_global_damage_multiplier()` (stacks with, never overrides, `tower_dmg`); dedicated `_apply_to_handler` branch for the perk with `[WARLORDS-DOCTRINE] applied L<n>` debug log; reset clears the ratio; new public `get_warlords_armor_ratio()` returning the level's `armor_bonus` (0 when unowned).
- `scripts/game/actors/Enemy.gd` — mod: in `setup()`, after innate `max_armor` is read from cfg and after `max_hp`/boss multiplier are final, adds `max_hp * warlords_armor_ratio` bonus armor (additive on innate armor), with `[WARLORDS-DOCTRINE] spawn_bonus level=<n> granted_armor=<amount>` debug log per spawn.
- `scripts/game/SpawnerSystem.gd` — no change needed: it only resolves innate armor into `enemy_config["armor"]`; the perk bonus is applied at the Enemy.gd spawn site as the plan requires.
- `scripts/ui/EnemyHealthBar.gd` — no change needed: `_update_armor_bar()` already mirrors `enemy.armor` / `enemy.max_armor` generically and seeds/eases the ArmorRow on first appearance, so granted armor shows/animates on previously-unarmored enemies for free.
- `tests/scenarios/warlords_doctrine.json` — new game-test scenario (details below).

## Criteria
- Perk definition (Common, exactly 3 levels, +5/+9/+14%) — Done
- Multiplier 1.05/1.09/1.14, additive with `tower_dmg`, reset → 1.0 — Done
- give/take consistent with existing Common globals (`apply_progression` / `reset_for_new_game`; there is no take API in this codebase — removal is via reset, same as every other global perk) — Done
- Bonus armor 8/12/15% of max HP while active — Done
- Additive over innate armor; unarmored enemy spawns with armor > 0 — Done
- Inactive/removal → exactly innate armor (ratio returns 0.0 when level ≤ 0 or def missing) — Done
- Armor bar shows/animates granted armor — Done (existing generic ArmorRow path; no code change)
- `[WARLORDS-DOCTRINE]` debug log per application/spawn-bonus naming level and amount — Done
- Harness scenario: unarmored enemy spawns armor > 0 / max_armor > 0; headless pass — Done
- Harness asserts damage bonus end-to-end at L1 (20 base × 1.05 = 21 lands full through `armor_hit`) — Done
- `enemy_armor_ballista` still passes unchanged — Done

## Commands and results
- `pwsh -Command "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 warlords_doctrine"` (via run_project_cmd, project poke-defense-godot) — exit 0; `status=pass exit=0 elapsed=1.278s`; "3 scenarios — 3 pass, 0 fail, 0 timeout, 0 error" (warlords_doctrine, progression_global_scaling, enemy_armor_ballista).
- Same command shape with `-Editor` (typecheck/build) — exit 0; "OK — no script or parse errors".
- Evidence in run log `.gen/harness/_logs/warlords_doctrine.out.log`: `[WARLORDS-DOCTRINE] applied L1 tower_damage_bonus=0.05 total_multiplier=1.05`, `[WARLORDS-DOCTRINE] spawn_bonus level=1 granted_armor=1.76 on Mushnub` (8% of hp 22), `[PROGRESSION] apply tower_dmg L1 multiplier=1.10` proving additive stacking.
- result.json assertions all pass: Mushnub spawns max_armor > 0 / armor > 0 where the pre-perk baseline scenario asserts 0/0; scripted neutral hit deals exactly 21 (StatsManager instance_summary.8801.damage == 21); owning both perks gives 1.10; reset returns multiplier to 1.0 and level to 0.

## Notes
- Scenario design gotchas for the tester: enemy HP is int, so the end-to-end damage leg uses base 20 (×1.05 = 21 integer-exact) instead of 10 (would floor to 10 and prove nothing). The scripted hit carries armor_damage 5 so the 1.76-granted armor strips fully (5 > 1.76 avoids the ARMOR_DAMAGE_REDUCTION halving of the HP component) and the full 21 reaches HP.
- `warlords_doctrine` has `forceVisibility: false` like other non-flagged globals, so chest draws behave unchanged; the scenario drives picks via `apply_progression` directly (same route as `progression_global_scaling.json`).
- Bonus armor intentionally scales off post-boss-multiplier `max_hp` (computed after the boss multiplier block in `setup()`), matching "share of its max HP" for bosses too.
- Manual/UI-sanity criterion remains for the manual tester (windowed run per `.gen/ui_scenario.md`): perk visible/selectable in progression draw, armor row appearing over a fresh unarmored enemy.
