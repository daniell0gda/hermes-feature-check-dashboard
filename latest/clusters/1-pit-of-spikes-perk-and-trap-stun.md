# Cluster 1: pit-of-spikes-perk-and-trap-stun

- Files: `scripts/progression/trap.json`, `scripts/progression/managers/TrapProgressionManager.gd`, `autoload/ProgressionManager.gd`, `scripts/game/actors/Trap.gd`
- Depends on: none
- Parallel: false (single implementation unit; cluster 2's scenario depends on it)

## Acceptance criteria

- While the Pit of Spikes perk (`traps_pit_of_spikes`) is unowned, a trap hit does not change the target's stun state (`stun_time_left` stays 0).
- The first trap hit against a given enemy while Pit of Spikes is owned applies a stun of approximately 0.4 seconds through the enemy's existing `EffectsManager.apply_stun` path (enemy `stun_time_left` becomes > 0 immediately after the hit).
- A second trap hit on the same enemy while that first stun is active or has expired does not re-apply the stun (`stun_time_left` remains 0 after it expires from the first application; no new stun is granted by later hits).
- Stun tracking is per enemy: a first trap hit on a different enemy stuns that enemy even after another enemy was already stunned once.
- Replaying progression levels on save load does not compound or reset the per-enemy "already stunned" state incorrectly: re-applying the same Pit of Spikes level is idempotent and does not itself trigger any stun.
- Debug-build `[PIT-OF-SPIKES]` log line per stun event: when a trap hit applies the first-hit stun, a filterable log line names the enemy id, trap id, and applied stun duration; subsequent non-stunning hits do not emit it.

## Verification commands

- Focused: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_pit_of_spikes_first_hit_stun.json"]`
- Full: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--quit-after", "3", "--path", "."]`
