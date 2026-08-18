# Coder report: pump-efficiency-perk

## Changed files
- `scripts/progression/floodgate_tower.json` — modified
- `scripts/progression/managers/FloodgateTowerProgressionManager.gd` — modified
- `autoload/ProgressionManager.gd` — modified
- `tests/scenarios/floodgate_pump_efficiency_progression.json` — new

## Criteria
- Unowned, floodgate_pump_efficiency is Common, level 0, eligible, and present in a non-flagged chest draw of 100 after venom_miasma_bloom is taken. — Done
- Unowned, the Floodgate waiting cooldown equals the TowersConfig floodgate cycle_cooldown of 2.0s. — Done
- Applying floodgate_pump_efficiency to levels 1, 2, and 3 sets the waiting cooldown to 1.8s, 1.6s, and 1.4s respectively as absolute level values. — Done
- After level 3, floodgate_pump_efficiency is ineligible and absent from a chest draw of 100. — Done
- Saving and reloading at level 3 keeps the waiting cooldown at 1.4s rather than compounding past 30%. — Done
- reset_for_new_game returns floodgate_pump_efficiency to level 0 and the waiting cooldown to 2.0s. — Done
- Applying floodgate_pump_efficiency does not change Quick Valve fill duration, Cryobrine chill, or Saltwater Purge enabled-state, and applying those siblings does not change the pump-efficiency waiting cooldown. — Done
- Debug-build [FloodgateProgression] log line per pump efficiency apply — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/floodgate_pump_efficiency_progression.json` — exit code 0; status=pass

## Notes
- Absolute cuts 0.1/0.2/0.3 on isolated `_cycle_cooldown_multiplier`. See implementation.md for harness gotchas.
