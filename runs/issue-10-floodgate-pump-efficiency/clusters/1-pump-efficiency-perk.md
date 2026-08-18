# Cluster 1: pump-efficiency-perk

cluster_id: pump-efficiency-perk
owned file scope: `scripts/progression/floodgate_tower.json`, `scripts/progression/managers/FloodgateTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`
dependencies: none
parallel: false

## Acceptance criteria

- Unowned, floodgate_pump_efficiency is Common, level 0, eligible, and present in a non-flagged chest draw of 100 after venom_miasma_bloom is taken.
- Unowned, the Floodgate waiting cooldown equals the TowersConfig floodgate cycle_cooldown of 2.0s.
- Applying floodgate_pump_efficiency to levels 1, 2, and 3 sets the waiting cooldown to 1.8s, 1.6s, and 1.4s respectively as absolute level values.
- After level 3, floodgate_pump_efficiency is ineligible and absent from a chest draw of 100.
- Saving and reloading at level 3 keeps the waiting cooldown at 1.4s rather than compounding past 30%.
- reset_for_new_game returns floodgate_pump_efficiency to level 0 and the waiting cooldown to 2.0s.
- Applying floodgate_pump_efficiency does not change Quick Valve fill duration, Cryobrine chill, or Saltwater Purge enabled-state, and applying those siblings does not change the pump-efficiency waiting cooldown.
- Debug-build [FloodgateProgression] log line per pump efficiency apply

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-floodgate-pump-efficiency` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/floodgate_pump_efficiency_progression.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-floodgate-pump-efficiency` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_chest_pool.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-floodgate-pump-efficiency` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]
