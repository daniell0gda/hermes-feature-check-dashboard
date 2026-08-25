# Acceptance Plan: traps_grave_robber economy perk (issue #80)

## Verification

- Focused test: `run_project_cmd ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_grave_robber_progression.json"]`
- Full test: `run_project_cmd ["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `run_project_cmd ["godot", "--headless", "--path", ".", "--import"]`

manual_testing: optional

Rationale: this is a pure numeric/economy perk (per project CLAUDE.md such perks need no new VFX). All behaviour is assertable headlessly through the harness (money deltas via `_set_money`/`GameState.money`, config via `progression_call`). A manual screenshot is optional proof only (HUD gold counter rising after an underground trap kill) — see `.gen/ui_scenario.md`.

## Clusters

1. perk-data-and-economy-manager — files: `scripts/progression/global.json`, `scripts/progression/managers/EconomyProgressionManager.gd` — depends on: none
- With no perk owned, `ProgressionManager.get_bounty_config()` is unchanged from today's shape and carries no grave-robber bonus.
- Owning `traps_grave_robber` at L1/L2/L3 exposes a bonus-gold configuration through the existing bounty path (`get_bounty_config()`) equivalent to +10%/+15%/+25% of the enemy's base reward on qualifying kills.
- Re-applying a level or replaying levels 1..N of `traps_grave_robber` (save/load) sets the bonus to that level's exact percentage rather than compounding or collapsing.
- Resetting progression (new run / `reset`) clears the grave-robber bonus so no bonus applies afterwards.
- Debug-build `[EconomyProgression]` log line per grave-robber level change naming the perk name, new level, and resulting bonus percentage.

2. trap-kill-bonus-application — files: `scripts/game/actors/Trap.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — depends on: 1
- When an enemy dies to a trap-sourced killing blow while underground and `traps_grave_robber` L1 is owned, the gold awarded for that kill equals base reward plus 10% of base reward (exact integer gold delta asserted).
- At L2 and L3 the same setup awards exactly +15% and +25% of base reward respectively over the base reward.
- A trap killing blow on an above-ground (surface) enemy awards exactly the base reward — no bonus.
- An underground enemy killed by a non-trap source (tower/projectile) while the perk is owned awards exactly what the existing bounty rules give — no grave-robber bonus.
- The bonus rides the existing bounty/economy payout path: with the perk owned, an ordinary qualifying kill's total gold still reflects any concurrently-owned `gold_on_kill`/`curse_blood_money` amounts unchanged (no cross-perk interference either direction).
- Debug-build `[GRAVE_ROBBER]` log line per qualifying bonus payout naming the enemy id and the bonus gold amount.

3. harness-scenario — files: `tests/scenarios/traps_grave_robber_progression.json` — depends on: 1, 2
- Focused harness scenario `traps_grave_robber_progression.json` runs headless to `[Harness] status=pass` with exit code 0, asserting inline (wait_for_condition) each of: L1/L2/L3 bonus config values, exact gold delta on underground trap kill, zero delta on surface trap kill, and zero delta on non-trap underground kill.

## Criteria

- With no perk owned, `ProgressionManager.get_bounty_config()` is unchanged from today's shape and carries no grave-robber bonus.
- Owning `traps_grave_robber` at L1/L2/L3 exposes a bonus-gold configuration through the existing bounty path (`get_bounty_config()`) equivalent to +10%/+15%/+25% of the enemy's base reward on qualifying kills.
- Re-applying a level or replaying levels 1..N of `traps_grave_robber` (save/load) sets the bonus to that level's exact percentage rather than compounding or collapsing.
- Resetting progression (new run / `reset`) clears the grave-robber bonus so no bonus applies afterwards.
- Debug-build `[EconomyProgression]` log line per grave-robber level change naming the perk name, new level, and resulting bonus percentage.
- When an enemy dies to a trap-sourced killing blow while underground and `traps_grave_robber` L1 is owned, the gold awarded for that kill equals base reward plus 10% of base reward (exact integer gold delta asserted).
- At L2 and L3 the same setup awards exactly +15% and +25% of base reward respectively over the base reward.
- A trap killing blow on an above-ground (surface) enemy awards exactly the base reward — no bonus.
- An underground enemy killed by a non-trap source (tower/projectile) while the perk is owned awards exactly what the existing bounty rules give — no grave-robber bonus.
- The bonus rides the existing bounty/economy payout path: with the perk owned, an ordinary qualifying kill's total gold still reflects any concurrently-owned `gold_on_kill`/`curse_blood_money` amounts unchanged (no cross-perk interference either direction).
- Debug-build `[GRAVE_ROBBER]` log line per qualifying bonus payout naming the enemy id and the bonus gold amount.
- Focused harness scenario `traps_grave_robber_progression.json` runs headless to `[Harness] status=pass` with exit code 0, asserting inline (wait_for_condition) each of: L1/L2/L3 bonus config values, exact gold delta on underground trap kill, zero delta on surface trap kill, and zero delta on non-trap underground kill.
