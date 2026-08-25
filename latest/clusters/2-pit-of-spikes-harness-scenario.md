# Cluster 2: pit-of-spikes-harness-scenario

- Files: `tests/scenarios/traps_pit_of_spikes_first_hit_stun.json`
- Depends on: 1
- Parallel: false

## Acceptance criteria

- The focused headless harness scenario passes with all expectations green: it grants Pit of Spikes, drives a trap hit onto an underground enemy, asserts the enemy is stunned, asserts a second hit does not refresh/re-grant the stun, and asserts the `[PIT-OF-SPIKES]` log line appears exactly once.
- With the game windowed, after a trap's first hit the stun status icon is visible on that enemy's health bar (driven by `stun_time_left` via the existing health-bar icon path) and disappears once the stun expires.

## Verification commands

- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_pit_of_spikes_first_hit_stun.json"]`
- Full: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--quit-after", "3", "--path", "."]`
