# Cluster 2: floodgate-waiting-cooldown

cluster_id: floodgate-waiting-cooldown
owned file scope: `scripts/game/actors/towers/FloodgateTower.gd`, `tests/scenarios/floodgate_pump_efficiency_progression.json`, `tests/scenarios/floodgate_pump_efficiency_cycle.json`, `tests/scenarios/progression_chest_pool.json`
dependencies: 1
parallel: false

## Acceptance criteria

- A Floodgate already on the map that then receives floodgate_pump_efficiency L3 uses the 1.4s waiting cooldown from the next waiting phase onward.
- At floodgate_pump_efficiency L3, fill, active, and drain phase lengths stay at their unperked values, and a live Floodgate still deals floodgate damage to underground enemies.
- Debug-build [FLOODGATE] log line per waiting-phase cooldown
- The progression_chest_pool scenario still passes after its seeded fallback pins are remasured for one extra eligible Common.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-floodgate-pump-efficiency` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/floodgate_pump_efficiency_progression.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-floodgate-pump-efficiency` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_chest_pool.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-floodgate-pump-efficiency` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]
