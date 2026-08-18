## ✅ Done

## ⬜ Pending
- Unowned, floodgate_pump_efficiency is Common, level 0, eligible, and present in a non-flagged chest draw of 100 after venom_miasma_bloom is taken. — quality: scripts/progression/managers/FloodgateTowerProgressionManager.gd: type cast float() forbidden by coding_rules.md; scripts/autoload/ProgressionManager.gd: type cast float() forbidden
- Unowned, the Floodgate waiting cooldown equals the TowersConfig floodgate cycle_cooldown of 2.0s. — quality: autoload/ProgressionManager.gd: type cast float() forbidden by coding_rules.md
- Applying floodgate_pump_efficiency to levels 1, 2, and 3 sets the waiting cooldown to 1.8s, 1.6s, and 1.4s respectively as absolute level values. — quality: scripts/progression/managers/FloodgateTowerProgressionManager.gd: type cast float() forbidden by coding_rules.md
- After level 3, floodgate_pump_efficiency is ineligible and absent from a chest draw of 100. — quality: scripts/progression/managers/FloodgateTowerProgressionManager.gd: type cast float() forbidden by coding_rules.md
- Saving and reloading at level 3 keeps the waiting cooldown at 1.4s rather than compounding past 30%. — quality: scripts/progression/managers/FloodgateTowerProgressionManager.gd: type cast float() forbidden by coding_rules.md
- reset_for_new_game returns floodgate_pump_efficiency to level 0 and the waiting cooldown to 2.0s. — quality: scripts/progression/managers/FloodgateTowerProgressionManager.gd: type cast float() forbidden by coding_rules.md
- Applying floodgate_pump_efficiency does not change Quick Valve fill duration, Cryobrine chill, or Saltwater Purge enabled-state, and applying those siblings does not change the pump-efficiency waiting cooldown. — quality: scripts/progression/managers/FloodgateTowerProgressionManager.gd: type cast float() forbidden by coding_rules.md
- Debug-build [FloodgateProgression] log line per pump efficiency apply — quality: scripts/progression/managers/FloodgateTowerProgressionManager.gd: type cast float() forbidden by coding_rules.md
- A Floodgate already on the map that then receives floodgate_pump_efficiency L3 uses the 1.4s waiting cooldown from the next waiting phase onward. — quality: scripts/game/actors/towers/FloodgateTower.gd: type cast float() and int() forbidden by coding_rules.md; dynamic .call() without type safety
- At floodgate_pump_efficiency L3, fill, active, and drain phase lengths stay at their unperked values, and a live Floodgate still deals floodgate damage to underground enemies. — quality: scripts/game/actors/towers/FloodgateTower.gd: type cast float() and int() forbidden by coding_rules.md
- Debug-build [FLOODGATE] log line per waiting-phase cooldown — quality: scripts/game/actors/towers/FloodgateTower.gd: type cast float() and int() forbidden by coding_rules.md
- The progression_chest_pool scenario still passes after its seeded fallback pins are remasured for one extra eligible Common. — quality: tests/scenarios/progression_chest_pool.json: no code violation but dependent on ProgressionManager casts

## ❌ Impossible
