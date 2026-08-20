# Acceptance Plan: map-available-tower-perks

manual_testing: required

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-map-available-tower-perks` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/chest_reward_compatibility.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-map-available-tower-perks` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-map-available-tower-perks` cmd=["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]

## Clusters

1. map-roster-chest-filter — files: `autoload/ProgressionManager.gd`, `tests/scenarios/chest_reward_compatibility.json`, `tests/scenarios/chest_filter_visual.json`, `tests/scenarios/scifi_overclock_progression.json`, `tests/scenarios/cannon_bunker_buster_progression.json` — depends on: none
- On a map whose build roster includes Fire, with zero Fire towers placed, a 100-choice chest draw includes Unique `fire_flashover`.
- On a map whose build roster includes Fire, with zero Fire towers placed and `fire_burn` owned, a 100-choice chest draw includes Unique `fire_wildfire_spread`.
- On a map whose build roster does not include Floodgate, a 100-choice chest draw does not include Unique `floodgate_saltwater_purge`.
- On a map whose build roster does not include Cannon, a 100-choice chest draw does not include Unique `cannon_bunker_buster` after `venom_miasma_bloom` is taken.
- On a map whose build roster includes Fire, with zero Fire towers placed, a 100-choice chest draw includes Common `fire_burn` and Unique `chest_quality`.
- Opening a chest still offers a Gold money card regardless of map roster coverage or placed towers.
- On a map whose build roster does not include Fire, owning a perk whose unlocks metadata grants Fire still includes Unique `fire_flashover` in a 100-choice chest draw with zero Fire towers placed.
- A 100-choice chest draw returns a non-empty set when compatibility filtering excludes Uniques for towers not on the current map roster.
- On a map whose build roster includes Sci-Fi, with zero Sci-Fi towers placed, a 100-choice chest draw includes Unique `scifi_overclock`.
- Debug-build [PROGRESSION] log line per skip of an incompatible chest reward, including the skipped reward name.

## Criteria

- On a map whose build roster includes Fire, with zero Fire towers placed, a 100-choice chest draw includes Unique `fire_flashover`.
- On a map whose build roster includes Fire, with zero Fire towers placed and `fire_burn` owned, a 100-choice chest draw includes Unique `fire_wildfire_spread`.
- On a map whose build roster does not include Floodgate, a 100-choice chest draw does not include Unique `floodgate_saltwater_purge`.
- On a map whose build roster does not include Cannon, a 100-choice chest draw does not include Unique `cannon_bunker_buster` after `venom_miasma_bloom` is taken.
- On a map whose build roster includes Fire, with zero Fire towers placed, a 100-choice chest draw includes Common `fire_burn` and Unique `chest_quality`.
- Opening a chest still offers a Gold money card regardless of map roster coverage or placed towers.
- On a map whose build roster does not include Fire, owning a perk whose unlocks metadata grants Fire still includes Unique `fire_flashover` in a 100-choice chest draw with zero Fire towers placed.
- A 100-choice chest draw returns a non-empty set when compatibility filtering excludes Uniques for towers not on the current map roster.
- On a map whose build roster includes Sci-Fi, with zero Sci-Fi towers placed, a 100-choice chest draw includes Unique `scifi_overclock`.
- Debug-build [PROGRESSION] log line per skip of an incompatible chest reward, including the skipped reward name.
