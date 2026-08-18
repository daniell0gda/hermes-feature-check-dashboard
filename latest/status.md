## ✅ Done
- Unowned, floodgate_pump_efficiency is Common, level 0, eligible, and present in a non-flagged chest draw of 100 after venom_miasma_bloom is taken.
- Unowned, the Floodgate waiting cooldown equals the TowersConfig floodgate cycle_cooldown of 2.0s.
- Applying floodgate_pump_efficiency to levels 1, 2, and 3 sets the waiting cooldown to 1.8s, 1.6s, and 1.4s respectively as absolute level values.
- After level 3, floodgate_pump_efficiency is ineligible and absent from a chest draw of 100.
- Saving and reloading at level 3 keeps the waiting cooldown at 1.4s rather than compounding past 30%.
- reset_for_new_game returns floodgate_pump_efficiency to level 0 and the waiting cooldown to 2.0s.
- Applying floodgate_pump_efficiency does not change Quick Valve fill duration, Cryobrine chill, or Saltwater Purge enabled-state, and applying those siblings does not change the pump-efficiency waiting cooldown.
- Debug-build [FloodgateProgression] log line per pump efficiency apply
- A Floodgate already on the map that then receives floodgate_pump_efficiency L3 uses the 1.4s waiting cooldown from the next waiting phase onward.
- At floodgate_pump_efficiency L3, fill, active, and drain phase lengths stay at their unperked values, and a live Floodgate still deals floodgate damage to underground enemies.
- Debug-build [FLOODGATE] log line per waiting-phase cooldown
- The progression_chest_pool scenario still passes after its seeded fallback pins are remasured for one extra eligible Common.

## ⬜ Pending

## ❌ Impossible
