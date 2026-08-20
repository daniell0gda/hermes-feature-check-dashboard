# Coder report: implementation

## Changed files
- `scripts/progression/global.json` — modified (`tower_dmg` 0.05/0.10/0.20, `tower_atk_speed` 5/10/20 percent, matching +5/+10/+20 descriptions)
- `docs/progression-system.md` — modified (documented the new magnitudes vs one tower upgrade)
- `autoload/ProgressionManager.gd` — modified (debug `[PROGRESSION] apply` log per global aggregate)
- `tests/scenarios/progression_global_scaling.json` — modified (new multipliers, descriptions, Generic 3.0→4.8 vs 1.20<1.60, log expectations)
- `tests/scenarios/traps_serrated_edges_progression.json` — modified (1.05/1.20; trap hit stays 6.5)
- `tests/scenarios/curse_overheat_cycle.json` — modified (aggregate 1.05; burst 1.365)
- `tests/scenarios/retry_after_defeat_clears_rewards.json` — modified (1.20 / 1.05 before Try Again)

## Criteria
- With neither Common owned, the global damage multiplier and the global attack-speed multiplier are both 1.0. — Done
- Applying `tower_dmg` once, twice, then three times sets the global damage multiplier (and the generic and bazooka tower damage multipliers) to 1.05, then 1.10, then 1.20, each step strictly greater than the last, and that level's description states the matching +5% / +10% / +20% bonus. — Done
- Applying `tower_atk_speed` once, twice, then three times sets the global attack-speed multiplier to 1.05, then 1.10, then 1.20, each step strictly greater than the last, and that level's description states the matching +5% / +10% / +20% bonus. — Done
- After `tower_dmg` and `tower_atk_speed` are each at level 2, a progression save and reload leaves both multipliers at 1.10. — Done
- `reset_for_new_game` returns both global multipliers to 1.0. — Done
- On an unupgraded Generic tower whose stored damage is 3.0, one tower upgrade raises stored damage to 4.8; `tower_dmg` at level 3 multiplies outgoing damage by 1.20, which is less than that upgrade's 1.60. — Done
- Debug-build [PROGRESSION] log line per global aggregate apply — Done
- Applying `tower_dmg` does not change trap hit damage. — Done
- After `tower_atk_speed` level 1 is owned, the progression attack-speed aggregate stays 1.05 while Overheat's burst fire-rate multiplies on top of that aggregate. — Done
- After three `tower_dmg` picks and one `tower_atk_speed` pick, a defeat Try Again returns both global multipliers to 1.0 and makes `tower_dmg` eligible again. — Done

## Commands and results
- `["godot", "--version"]` project=`poke-defense-godot` workspace=`poke-defense-godot/issue-progression-global-perks-too-weak` — exit code 0; 4.4.1.stable.official.49a5bc7b6
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; import/typecheck completed in 59301ms
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_global_scaling.json"]` — exit code 0; `.gen/harness/progression_global_scaling/result.json` status=pass
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_serrated_edges_progression.json"]` — exit code 0; `.gen/harness/traps_serrated_edges_progression/result.json` status=pass
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/retry_after_defeat_clears_rewards.json"]` — exit code 0; `.gen/harness/retry_after_defeat_clears_rewards/result.json` status=pass
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/curse_overheat_cycle.json"]` — exit code 0; `.gen/harness/curse_overheat_cycle/result.json` status=pass (43235ms)

## Notes
- Balance choice: option 1 from the issue — per-level values an order of magnitude larger (0.5/1/2% → 5/10/20%). Replacement-per-pick semantics unchanged.
- L3 global damage (×1.20 outgoing) remains smaller than one Generic upgrade (stored 3.0 → 4.8, ×1.60).
- Player-facing perk descriptions changed; this is a numeric Common, no new VFX. Windowed HUD screenshot not run here (headless).
- Log-expectation gotcha: delete stale `.gen/harness/_logs/<scenario>.out.log` after adding new print lines, or `materialize_engine_out_log` reuses the old file.
- Runner key used: `poke-defense-godot` (as assigned). Plan text still says `godot-td`.
- Team-work: status classification left to checker. No dashboard events published.
