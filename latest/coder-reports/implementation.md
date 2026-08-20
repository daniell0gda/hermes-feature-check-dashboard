# Coder report: implementation

## Changed files
- `autoload/ProgressionManager.gd` — modified
- `tests/scenarios/chest_reward_compatibility.json` — modified
- `tests/scenarios/chest_filter_visual.json` — modified
- `tests/scenarios/scifi_overclock_progression.json` — modified
- `tests/scenarios/cannon_bunker_buster_progression.json` — modified

## Criteria
- On a map whose build roster includes Fire, with zero Fire towers placed, a 100-choice chest draw includes Unique `fire_flashover`. — Done
- On a map whose build roster includes Fire, with zero Fire towers placed and `fire_burn` owned, a 100-choice chest draw includes Unique `fire_wildfire_spread`. — Done
- On a map whose build roster does not include Floodgate, a 100-choice chest draw does not include Unique `floodgate_saltwater_purge`. — Done
- On a map whose build roster does not include Cannon, a 100-choice chest draw does not include Unique `cannon_bunker_buster` after `venom_miasma_bloom` is taken. — Done
- On a map whose build roster includes Fire, with zero Fire towers placed, a 100-choice chest draw includes Common `fire_burn` and Unique `chest_quality`. — Done
- Opening a chest still offers a Gold money card regardless of map roster coverage or placed towers. — Done
- On a map whose build roster does not include Fire, owning a perk whose unlocks metadata grants Fire still includes Unique `fire_flashover` in a 100-choice chest draw with zero Fire towers placed. — Done
- A 100-choice chest draw returns a non-empty set when compatibility filtering excludes Uniques for towers not on the current map roster. — Done
- On a map whose build roster includes Sci-Fi, with zero Sci-Fi towers placed, a 100-choice chest draw includes Unique `scifi_overclock`. — Done
- Debug-build [PROGRESSION] log line per skip of an incompatible chest reward, including the skipped reward name. — Done

## Commands and results
- `godot --version` — exit code 0; 4.4.1.stable.official.49a5bc7b6
- `godot --headless --path . --editor --quit-after 300` — exit code 0; import/parse gate completed (~58s)
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/chest_reward_compatibility.json` — exit code 0; `.gen/harness/chest_reward_compatibility/result.json` `status: pass`; all 3 expectations passed; modal answered `kind: money` with 3 cards

## Notes
- Coverage source is map roster, not placed towers. `_placed_tower_kinds()` remains unused by the filter.
- Cluster 1 owned files only. Other progression scenarios that still expect “must place the tower first” may fail a broader suite (for example `fire_flashover_progression.json` notes).
- Visual scenario `chest_filter_visual.json` notes updated; headless cannot prove pixels.
- Project runner key used: `godot-td` / workspace `poke-defense-godot/issue-map-available-tower-perks`.
