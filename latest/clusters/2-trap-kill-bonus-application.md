# Cluster 2: trap-kill-bonus-application

- owned files: `scripts/game/actors/Trap.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd`
- dependencies: 1
- parallel: false

## Acceptance criteria
- When an enemy dies to a trap-sourced killing blow while underground and `traps_grave_robber` L1 is owned, the gold awarded for that kill equals base reward plus 10% of base reward (exact integer gold delta asserted).
- At L2 and L3 the same setup awards exactly +15% and +25% of base reward respectively over the base reward.
- A trap killing blow on an above-ground (surface) enemy awards exactly the base reward — no bonus.
- An underground enemy killed by a non-trap source (tower/projectile) while the perk is owned awards exactly what the existing bounty rules give — no grave-robber bonus.
- The bonus rides the existing bounty/economy payout path: with the perk owned, an ordinary qualifying kill's total gold still reflects any concurrently-owned `gold_on_kill`/`curse_blood_money` amounts unchanged (no cross-perk interference either direction).
- Debug-build `[GRAVE_ROBBER]` log line per qualifying bonus payout naming the enemy id and the bonus gold amount.

## Verification commands
- Focused: `run_project_cmd ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_grave_robber_progression.json"]`
- Full test: `run_project_cmd ["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `run_project_cmd ["godot", "--headless", "--path", ".", "--import"]`

## Notes
- Trap attribution already exists (`EnemyHealthController._is_trap_attribution`, `enemy.last_hit_tower_type_id`, underground super-effective path at take_damage). The kill site in `_handle_death()` already knows both the killer type and `enemy.is_underground`; extend that payout, do not add a parallel gold path.
