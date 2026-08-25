## ✅ Done
- A fresh run of `projectiles_10x_ballistic` reaches its cumulative damage/score thresholds within the scenario timeout and reports status pass with exit code 0.
- A fresh run of `projectiles_10x_beam_cone` reaches its cumulative damage/score thresholds within the scenario timeout and reports status pass with exit code 0.
- Fresh runs of `projectiles_2x_roster` and `projectiles_5x_roster` reach their roster damage/score thresholds and each report status pass with exit code 0.

## ⬜ Pending
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
- A fresh run of `smoke_tower_roster` places every expected roster tower without errors and reports status pass with exit code 0.
- A fresh run of `underground_diversion_baseline` establishes its baseline diversion measurements within the scenario timeout and reports status pass with exit code 0.
- Every previously-green neighboring scenario in each touched feature family (same-name family suites listed in `.gen/full_suite.txt`) still reports status pass on a rerun after the fixes.
- Any scenario whose JSON expectation was adjusted rather than game code changed carries a `notes[]` entry justifying the change as a stale tuning assumption; no failing expectation is weakened silently.
- A fresh focused-run runner stdout/stderr contains no new Godot parse/script errors compared to the pre-existing baseline noise (known pre-existing HudTheme texture-load noise excluded).

## ❌ Impossible
(none)
