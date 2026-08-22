# Cluster 2: harness-scenario-heart-beat

cluster_id: harness-scenario-heart-beat
owned file scope: `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessActions.gd`, `tests/scenarios/hud_heart_beat_on_egg_damage.json`
dependencies: 1
parallel: false

## Acceptance criteria

- The harness can read the HUD heart icon's current scale during a run so a headless scenario can assert the beat behaviour.
- A focused headless harness scenario applies two egg HP decreases, observes the icon scale rise above its base and return to it, and finishes with status pass and all expectations green.

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-heart-hud-beat-on-egg-damage` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_heart_beat_on_egg_damage.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-heart-hud-beat-on-egg-damage` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-heart-hud-beat-on-egg-damage` cmd=["godot", "--headless", "--editor", "--quit-after", "300", "--path", "."]
