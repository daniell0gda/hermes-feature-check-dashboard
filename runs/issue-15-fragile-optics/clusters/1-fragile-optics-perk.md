# Cluster 1: fragile-optics-perk

- cluster_id: 1-fragile-optics-perk
- owned file scope: `scripts/progression/global.json`, `scripts/progression/managers/CurseProgressionManager.gd`, `autoload/ProgressionManager.gd`, `docs/progression.md`, `docs/progression-system.md`, `tests/scenarios/curse_fragile_optics_progression.json`
- dependencies: none
- parallel: false

## Acceptance criteria

- `curse_fragile_optics` is a Common perk that starts at level 0 and eligible, appears in a full chest draw while eligible, becomes level 1 after it is applied, becomes level 2 after it is applied again, and is absent from a full chest draw after that second level is taken.
- While unowned, Fragile Optics reports disabled with no range bonus and no miss chance. At level 1 it reports a +10% range bonus and a documented L1 miss chance. At level 2 it reports a +20% range bonus and a documented L2 miss chance higher than L1. Both owned levels report the same documented speed threshold relative to the map baseline enemy speed.
- An owned `curse_fragile_optics` remains owned after progression save and reload, and `reset_for_new_game` returns it to unowned with the disabled config.
- `curse_fragile_optics` and `curse_overheat` keep independent state: taking either one does not own or change the other's level or config.
- Debug-build [FRAGILE_OPTICS] log line per perk apply

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/curse_fragile_optics_progression.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/curse_overheat_progression.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
