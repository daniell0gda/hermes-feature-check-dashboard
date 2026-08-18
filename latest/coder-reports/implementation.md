# Coder report: implementation

## Changed files
- `scripts/progression/managers/FloodgateTowerProgressionManager.gd` — modified
- `autoload/ProgressionManager.gd` — modified
- `scripts/game/actors/towers/FloodgateTower.gd` — modified

## Criteria
- Unowned, floodgate_pump_efficiency is Common, level 0, eligible, and present in a non-flagged chest draw of 100 after venom_miasma_bloom is taken. — Done
- Unowned, the Floodgate waiting cooldown equals the TowersConfig floodgate cycle_cooldown of 2.0s. — Done
- Applying floodgate_pump_efficiency to levels 1, 2, and 3 sets the waiting cooldown to 1.8s, 1.6s, and 1.4s respectively as absolute level values. — Done
- After level 3, floodgate_pump_efficiency is ineligible and absent from a chest draw of 100. — Done
- Saving and reloading at level 3 keeps the waiting cooldown at 1.4s rather than compounding past 30%. — Done
- reset_for_new_game returns floodgate_pump_efficiency to level 0 and the waiting cooldown to 2.0s. — Done
- Applying floodgate_pump_efficiency does not change Quick Valve fill duration, Cryobrine chill, or Saltwater Purge enabled-state, and applying those siblings does not change the pump-efficiency waiting cooldown. — Done
- Debug-build [FloodgateProgression] log line per pump efficiency apply — Done
- A Floodgate already on the map that then receives floodgate_pump_efficiency L3 uses the 1.4s waiting cooldown from the next waiting phase onward. — Done
- At floodgate_pump_efficiency L3, fill, active, and drain phase lengths stay at their unperked values, and a live Floodgate still deals floodgate damage to underground enemies. — Done
- Debug-build [FLOODGATE] log line per waiting-phase cooldown — Done
- The progression_chest_pool scenario still passes after its seeded fallback pins are remasured for one extra eligible Common. — Done

## Commands and results
- `["godot","--version"]` project=`poke-defense-godot` workspace=`poke-defense-godot/issue-floodgate-pump-efficiency` — HTTP 400; project must be an approved profile key
- `["godot","--version"]` project=`godot-td` workspace=`poke-defense-godot/issue-floodgate-pump-efficiency` — exit code 0; 4.4.1.stable.official.49a5bc7b6
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit code 0; import completed, re-registered FloodgateTower / FloodgateTowerProgressionManager / ProgressionManager.gd
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/floodgate_pump_efficiency_progression.json"]` — exit code 0; status=pass in `.gen/harness/floodgate_pump_efficiency_progression/result.json`
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/floodgate_pump_efficiency_cycle.json"]` — exit code 0; status=pass in `.gen/harness/floodgate_pump_efficiency_cycle/result.json`
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/progression_chest_pool.json"]` — exit code 0; status=pass in `.gen/harness/progression_chest_pool/result.json`

## Notes
- Revision 1 quality fix only: no new perk behavior. `_floodgate_pm` is now typed `FloodgateTowerProgressionManager`; getters call it directly; FloodgateTower uses the ProgressionManager autoload (no `float()`/`int()`/`.call()` on the pump-efficiency path).
- JSON `lvl` is a float (`L1.0` in `[FloodgateProgression]` logs). Dictionary numbers go through `_read_float` (typeof + typed assignment), not `float()`.
- status.md left for the checker. Existing tests already covered the criteria; this pass re-verified after the quality rewrite.
