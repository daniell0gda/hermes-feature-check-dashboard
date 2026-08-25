# Cluster 1: perk-data-and-economy-manager

- owned files: `scripts/progression/global.json`, `scripts/progression/managers/EconomyProgressionManager.gd`
- dependencies: none
- parallel: false (foundation for clusters 2 and 3)

## Acceptance criteria
- With no perk owned, `ProgressionManager.get_bounty_config()` is unchanged from today's shape and carries no grave-robber bonus.
- Owning `traps_grave_robber` at L1/L2/L3 exposes a bonus-gold configuration through the existing bounty path (`get_bounty_config()`) equivalent to +10%/+15%/+25% of the enemy's base reward on qualifying kills.
- Re-applying a level or replaying levels 1..N of `traps_grave_robber` (save/load) sets the bonus to that level's exact percentage rather than compounding or collapsing.
- Resetting progression (new run / `reset`) clears the grave-robber bonus so no bonus applies afterwards.
- Debug-build `[EconomyProgression]` log line per grave-robber level change naming the perk name, new level, and resulting bonus percentage.

## Verification commands
- Focused: `run_project_cmd ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_grave_robber_progression.json"]`
- Full test: `run_project_cmd ["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `run_project_cmd ["godot", "--headless", "--path", ".", "--import"]`

## Notes
- Follow the existing `gold_on_kill` / `curse_blood_money` precedent inside `EconomyProgressionManager` (`HANDLED`, per-perk state fields, absolute-level semantics). Do not invent a new gold path.
- The bonus is conditional on trap-sourced kill + underground; the config must carry enough for the payout site in cluster 2 to apply it conditionally.
