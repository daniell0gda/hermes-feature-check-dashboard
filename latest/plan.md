# Acceptance Plan: fix-preexisting-harness-reds

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cannon_bunker_buster.json"]`
- Full test: `["bash", ".gen/run_full_suite.sh"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]`

## Clusters

1. cannon-ballista-damage-path — files: `scripts/game` (tower attack/damage scripts), `tests/scenarios/cannon_bunker_buster.json`, `tests/scenarios/cannon_bunker_buster_progression.json`, `tests/scenarios/cannon_heavier_shells_blast.json`, `tests/scenarios/curse_overheat_cycle.json` — depends on: none
- A fresh run of `cannon_bunker_buster` reaches `stats.damage_by_type.cannon > 0` within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `cannon_bunker_buster_progression` observes its expected cannon damage progression value and reports status pass with exit code 0.
- A fresh run of `cannon_heavier_shells_blast` observes its expected blast behaviour and reports status pass with exit code 0.
- A fresh run of `curse_overheat_cycle` observes balista damage landing on enemies within the scenario timeout and reports status pass with exit code 0.
2. burn-material-state — files: `scripts/game/actors/effects` (status/material restore scripts), `tests/scenarios/fire_oil_slick.json`, `tests/scenarios/fire_oil_slick_progression.json`, `tests/scenarios/ice_burn_material_restore_stuck.json` — depends on: none
- After the `fire_oil_slick` scenario's burn application, `Mushnub_boss.materials_clean` becomes false within the scenario timeout, and the scenario reports status pass with exit code 0.
- A fresh run of `fire_oil_slick_progression` reports status pass with exit code 0.
- In `ice_burn_material_restore_stuck`, after the frozen enemy's effect expires, the enemy's original materials are restored (material state returns to clean) within the scenario timeout, and the scenario reports status pass with exit code 0.
3. fire-spread-family — files: `scripts/game` (fire spread/hazard scripts), `tests/scenarios/fire_flashover_spread.json`, `tests/scenarios/fire_wildfire_spread_progression.json`, `tests/scenarios/fire_wildfire_spread_runtime.json`, `tests/scenarios/fire_wildfire_spread_visual.json` — depends on: none
- A fresh run of `fire_flashover_spread` observes fire spreading beyond the ignition tile within the scenario timeout and reports status pass with exit code 0.
- Fresh runs of `fire_wildfire_spread_progression`, `fire_wildfire_spread_runtime`, and `fire_wildfire_spread_visual` each report status pass with exit code 0.
4. elemental-progression-tuning — files: `autoload/ProgressionManager.gd` or perk config data, `tests/scenarios/scifi_overclock.json`, `tests/scenarios/scifi_overclock_progression.json`, `tests/scenarios/scifi_capacitor_bank.json`, `tests/scenarios/scifi_piercing_beam_progression.json`, `tests/scenarios/water_deep_soak_progression.json`, `tests/scenarios/floodgate_cryobrine_progression.json` — depends on: none
- A fresh run of `scifi_overclock` reports its overclock effect within the scenario timeout and exits with status pass, code 0.
- The `scifi_overclock_progression` scenario's observed progression value matches the scenario's expected value exactly (currently 1.5 vs expected 1.4); whichever side is stale, game code or scenario notes[] record which value is correct and why.
- Fresh runs of `scifi_capacitor_bank`, `scifi_piercing_beam_progression`, `water_deep_soak_progression`, and `floodgate_cryobrine_progression` each report status pass with exit code 0.
5. hud-armed-mode-call-path — files: `scripts/ui` (armed-mode button handling), `tests/scenarios/hud_controls_state.json` — depends on: none
- After the `hud_controls_state` scenario issues `call ui _on_carve`, `ui_call.get_armed_mode_buttons` reports `"carve"` within the scenario timeout, and the scenario reports status pass with exit code 0.
6. timed-hazards-and-victory — files: `scripts/game` (hazard timer / victory-clear scripts), `tests/scenarios/issue_35_timed_hazards_map_change.json`, `tests/scenarios/issue_86_victory_underground_clear.json` — depends on: none
- A fresh run of `issue_35_timed_hazards_map_change` observes its timed hazard surviving/behaving across a map change within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `issue_86_victory_underground_clear` reaches its victory condition after the underground clear within the scenario timeout and reports status pass with exit code 0.
7. boss-targeting-isolation — files: `scripts/game` (boss/porter, static breach, targeting scripts), `tests/scenarios/porter_boss_runner.json`, `tests/scenarios/static_breach_isolation.json`, `tests/scenarios/tower_targeting_armor_priority.json` — depends on: none
- A fresh run of `porter_boss_runner` completes its runner timeline within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `static_breach_isolation` observes breach isolation within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `tower_targeting_armor_priority` observes armored enemies prioritized per the targeting contract within the scenario timeout and reports status pass with exit code 0.
8. long-carve-tile-budget — files: `scripts/game/CaveSystem.gd` or underground grid sizing, `tests/scenarios/cave_discovery_long_carve.json` — depends on: none
- A fresh run of `cave_discovery_long_carve` yields at least 1000 carved tiles, either because the carveable area was restored or because the threshold was adjusted with an explicit justification recorded in the scenario's `notes[]`; no silent weakening of the assertion.
9. progression-economy — files: `autoload/ProgressionManager.gd` or chest/pick reward scripts, `tests/scenarios/progression_chest_pool.json`, `tests/scenarios/progression_pick.json` — depends on: none
- A fresh run of `progression_chest_pool` observes the expected chest reward pool contents and reports status pass with exit code 0.
- A fresh run of `progression_pick` observes the expected pick rewards and reports status pass with exit code 0.
10. projectile-damage-thresholds — files: `scripts/game` (projectile damage/scoring scripts), `tests/scenarios/projectiles_10x_ballistic.json`, `tests/scenarios/projectiles_10x_beam_cone.json`, `tests/scenarios/projectiles_2x_roster.json`, `tests/scenarios/projectiles_5x_roster.json` — depends on: none
- A fresh run of `projectiles_10x_ballistic` reaches its cumulative damage/score thresholds within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `projectiles_10x_beam_cone` reaches its cumulative damage/score thresholds within the scenario timeout and reports status pass with exit code 0.
- Fresh runs of `projectiles_2x_roster` and `projectiles_5x_roster` reach their roster damage/score thresholds and each report status pass with exit code 0.
11. roster-and-diversion-baseline — files: `scripts/game` (tower roster/placement, diversion scripts), `tests/scenarios/smoke_tower_roster.json`, `tests/scenarios/underground_diversion_baseline.json` — depends on: none
- A fresh run of `smoke_tower_roster` places every expected roster tower without errors and reports status pass with exit code 0.
- A fresh run of `underground_diversion_baseline` establishes its baseline diversion measurements within the scenario timeout and reports status pass with exit code 0.
12. suite-regression-guard — files: none — depends on: 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11
- Every previously-green neighboring scenario in each touched feature family (same-name family suites listed in `.gen/full_suite.txt`) still reports status pass on a rerun after the fixes.
- Any scenario whose JSON expectation was adjusted rather than game code changed carries a `notes[]` entry justifying the change as a stale tuning assumption; no failing expectation is weakened silently.
- A fresh focused-run runner stdout/stderr contains no new Godot parse/script errors compared to the pre-existing baseline noise (known pre-existing HudTheme texture-load noise excluded).

## Criteria

- A fresh run of `cannon_bunker_buster` reaches `stats.damage_by_type.cannon > 0` within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `cannon_bunker_buster_progression` observes its expected cannon damage progression value and reports status pass with exit code 0.
- A fresh run of `cannon_heavier_shells_blast` observes its expected blast behaviour and reports status pass with exit code 0.
- A fresh run of `curse_overheat_cycle` observes balista damage landing on enemies within the scenario timeout and reports status pass with exit code 0.
- After the `fire_oil_slick` scenario's burn application, `Mushnub_boss.materials_clean` becomes false within the scenario timeout, and the scenario reports status pass with exit code 0.
- A fresh run of `fire_oil_slick_progression` reports status pass with exit code 0.
- In `ice_burn_material_restore_stuck`, after the frozen enemy's effect expires, the enemy's original materials are restored (material state returns to clean) within the scenario timeout, and the scenario reports status pass with exit code 0.
- A fresh run of `fire_flashover_spread` observes fire spreading beyond the ignition tile within the scenario timeout and reports status pass with exit code 0.
- Fresh runs of `fire_wildfire_spread_progression`, `fire_wildfire_spread_runtime`, and `fire_wildfire_spread_visual` each report status pass with exit code 0.
- A fresh run of `scifi_overclock` reports its overclock effect within the scenario timeout and exits with status pass, code 0.
- The `scifi_overclock_progression` scenario's observed progression value matches the scenario's expected value exactly (currently 1.5 vs expected 1.4); whichever side is stale, game code or scenario notes[] record which value is correct and why.
- Fresh runs of `scifi_capacitor_bank`, `scifi_piercing_beam_progression`, `water_deep_soak_progression`, and `floodgate_cryobrine_progression` each report status pass with exit code 0.
- After the `hud_controls_state` scenario issues `call ui _on_carve`, `ui_call.get_armed_mode_buttons` reports `"carve"` within the scenario timeout, and the scenario reports status pass with exit code 0.
- A fresh run of `issue_35_timed_hazards_map_change` observes its timed hazard surviving/behaving across a map change within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `issue_86_victory_underground_clear` reaches its victory condition after the underground clear within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `porter_boss_runner` completes its runner timeline within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `static_breach_isolation` observes breach isolation within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `tower_targeting_armor_priority` observes armored enemies prioritized per the targeting contract within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `cave_discovery_long_carve` yields at least 1000 carved tiles, either because the carveable area was restored or because the threshold was adjusted with an explicit justification recorded in the scenario's `notes[]`; no silent weakening of the assertion.
- A fresh run of `progression_chest_pool` observes the expected chest reward pool contents and reports status pass with exit code 0.
- A fresh run of `progression_pick` observes the expected pick rewards and reports status pass with exit code 0.
- A fresh run of `projectiles_10x_ballistic` reaches its cumulative damage/score thresholds within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `projectiles_10x_beam_cone` reaches its cumulative damage/score thresholds within the scenario timeout and reports status pass with exit code 0.
- Fresh runs of `projectiles_2x_roster` and `projectiles_5x_roster` reach their roster damage/score thresholds and each report status pass with exit code 0.
- A fresh run of `smoke_tower_roster` places every expected roster tower without errors and reports status pass with exit code 0.
- A fresh run of `underground_diversion_baseline` establishes its baseline diversion measurements within the scenario timeout and reports status pass with exit code 0.
- Every previously-green neighboring scenario in each touched feature family (same-name family suites listed in `.gen/full_suite.txt`) still reports status pass on a rerun after the fixes.
- Any scenario whose JSON expectation was adjusted rather than game code changed carries a `notes[]` entry justifying the change as a stale tuning assumption; no failing expectation is weakened silently.
- A fresh focused-run runner stdout/stderr contains no new Godot parse/script errors compared to the pre-existing baseline noise (known pre-existing HudTheme texture-load noise excluded).

manual_testing: none
