# Acceptance Plan: traps-pit-of-spikes-first-hit-stuns-turn

manual_testing: required

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_pit_of_spikes_first_hit_stun.json"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--quit-after", "3", "--path", "."]`

## Clusters

1. pit-of-spikes-perk-and-trap-stun — files: `scripts/progression/trap.json`, `scripts/progression/managers/TrapProgressionManager.gd`, `autoload/ProgressionManager.gd`, `scripts/game/actors/Trap.gd` — depends on: none
- While the Pit of Spikes perk (`traps_pit_of_spikes`) is unowned, a trap hit does not change the target's stun state (`stun_time_left` stays 0).
- The first trap hit against a given enemy while Pit of Spikes is owned applies a stun of approximately 0.4 seconds through the enemy's existing `EffectsManager.apply_stun` path (enemy `stun_time_left` becomes > 0 immediately after the hit).
- A second trap hit on the same enemy while that first stun is active or has expired does not re-apply the stun (`stun_time_left` remains 0 after it expires from the first application; no new stun is granted by later hits).
- Stun tracking is per enemy: a first trap hit on a different enemy stuns that enemy even after another enemy was already stunned once.
- Replaying progression levels on save load does not compound or reset the per-enemy "already stunned" state incorrectly: re-applying the same Pit of Spikes level is idempotent and does not itself trigger any stun.
- Debug-build `[PIT-OF-SPIKES]` log line per stun event: when a trap hit applies the first-hit stun, a filterable log line names the enemy id, trap id, and applied stun duration; subsequent non-stunning hits do not emit it.
2. pit-of-spikes-harness-scenario — files: `tests/scenarios/traps_pit_of_spikes_first_hit_stun.json` — depends on: 1
- The focused headless harness scenario passes with all expectations green: it grants Pit of Spikes, drives a trap hit onto an underground enemy, asserts the enemy is stunned, asserts a second hit does not refresh/re-grant the stun, and asserts the `[PIT-OF-SPIKES]` log line appears exactly once.
- With the game windowed, after a trap's first hit the stun status icon is visible on that enemy's health bar (driven by `stun_time_left` via the existing health-bar icon path) and disappears once the stun expires.

## Criteria

- While the Pit of Spikes perk (`traps_pit_of_spikes`) is unowned, a trap hit does not change the target's stun state (`stun_time_left` stays 0).
- The first trap hit against a given enemy while Pit of Spikes is owned applies a stun of approximately 0.4 seconds through the enemy's existing `EffectsManager.apply_stun` path (enemy `stun_time_left` becomes > 0 immediately after the hit).
- A second trap hit on the same enemy while that first stun is active or has expired does not re-apply the stun (`stun_time_left` remains 0 after it expires from the first application; no new stun is granted by later hits).
- Stun tracking is per enemy: a first trap hit on a different enemy stuns that enemy even after another enemy was already stunned once.
- Replaying progression levels on save load does not compound or reset the per-enemy "already stunned" state incorrectly: re-applying the same Pit of Spikes level is idempotent and does not itself trigger any stun.
- Debug-build `[PIT-OF-SPIKES]` log line per stun event: when a trap hit applies the first-hit stun, a filterable log line names the enemy id, trap id, and applied stun duration; subsequent non-stunning hits do not emit it.
- The focused headless harness scenario passes with all expectations green: it grants Pit of Spikes, drives a trap hit onto an underground enemy, asserts the enemy is stunned, asserts a second hit does not refresh/re-grant the stun, and asserts the `[PIT-OF-SPIKES]` log line appears exactly once.
- With the game windowed, after a trap's first hit the stun status icon is visible on that enemy's health bar (driven by `stun_time_left` via the existing health-bar icon path) and disappears once the stun expires.
