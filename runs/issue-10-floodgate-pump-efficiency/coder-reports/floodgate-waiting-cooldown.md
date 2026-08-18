# Coder report: floodgate-waiting-cooldown

## Changed files
- `scripts/game/actors/towers/FloodgateTower.gd` — modified
- `tests/scenarios/floodgate_pump_efficiency_progression.json` — new
- `tests/scenarios/floodgate_pump_efficiency_cycle.json` — new
- `tests/scenarios/progression_chest_pool.json` — modified

## Criteria
- A Floodgate already on the map that then receives floodgate_pump_efficiency L3 uses the 1.4s waiting cooldown from the next waiting phase onward. — Done
- At floodgate_pump_efficiency L3, fill, active, and drain phase lengths stay at their unperked values, and a live Floodgate still deals floodgate damage to underground enemies. — Done
- Debug-build [FLOODGATE] log line per waiting-phase cooldown — Done
- The progression_chest_pool scenario still passes after its seeded fallback pins are remasured for one extra eligible Common. — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/floodgate_pump_efficiency_progression.json` — exit code 0; status=pass
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/floodgate_pump_efficiency_cycle.json` — exit code 0; status=pass
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_chest_pool.json` — exit code 0; status=pass

## Notes
- Live tower queries `get_floodgate_cycle_cooldown` every waiting frame. Logs: L0 2.00s then L3 1.40s.
- Chest-pool pins remasured to ice_cold_steel / ice_frostbite_focus. See implementation.md.
