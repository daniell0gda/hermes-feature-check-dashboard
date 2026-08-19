# Coder report: implementation

## Changed files
- `scripts/config/Balance.gd` — modified (`porter_boss_runner.miss_chance` 0.7, `miss_reduction_per_level` 0.05)
- `scripts/progression/porter_tower.json` — modified (`maxLevels` 3)
- `scripts/progression/managers/PorterTowerProgressionManager.gd` — modified
- `autoload/ProgressionManager.gd` — modified (`get_porter_boss_runner_miss_chance`, seed/consume)
- `scripts/game/actors/towers/PorterTower.gd` — modified (boss miss skips dissolve/reroute; miss log)
- `tests/scenarios/porter_boss_runner.json` — modified
- `.gen/changes.md` — appended

## Criteria
- Unique perk `porter_boss_runner` is type Unique, is eligible before it is taken, and appears in a chest draw before it is taken. — Done
- Applying `porter_boss_runner` owns it at level 1, then 2, then 3; it stays eligible and appears in a chest draw until level 3; a further apply leaves the level at 3; `reset_for_new_game` returns it to unowned level 0. — Done
- Without `porter_boss_runner` owned, a Porter in range of a live surface boss does not lock or charge that boss, and the boss stays on the surface. — Done
- With `porter_boss_runner` owned, a Porter in range of a live surface boss locks that boss and completes a full charge. — Done
- After a successful full Porter charge on a boss with `porter_boss_runner` owned, the boss remains in play and is rerouted onto an extra underground detour produced by `UGSystem.compute_underground_route` rather than being consumed or removed. — Done
- A successful boss reroute plays Porter's existing teleport dissolve VFX; dissolving is observable during charge completion. — Done
- After a completed Porter charge on a boss that misses, the boss stays on the surface, is not rerouted, and the success teleport dissolve cue does not play. — Done
- Level 1 miss chance is Balance `porter_boss_runner.miss_chance` 0.7; each extra level subtracts `miss_reduction_per_level` 0.05 (L2 0.65, L3 0.60). A seeded roll below the current chance misses; a seeded roll at or above it hits. — Done
- Without `porter_boss_runner` owned, a Porter still teleports a non-boss surface enemy onto a valid underground route. — Done
- Debug-build [PORTER_BOSS_RUNNER] log line per perk apply — Done
- Debug-build [PORTER_BOSS_RUNNER] log line per boss lock event — Done
- Debug-build [PORTER_BOSS_RUNNER] log line per boss reroute event — Done
- Debug-build [PORTER_BOSS_RUNNER] log line per boss miss event — Done
- Focused harness scenario `porter_boss_runner` covers perk off (no boss target), perk on plus a hit seed (charge then reroute), and perk on plus a miss seed (charge spent, no reroute), and finishes with `status: pass`. — Done

## Commands and results
- `godot --version` — exit code 0; 4.4.1.stable.official.49a5bc7b6
- `godot --headless --path . --editor --quit-after 300` — exit code 0; import/class refresh 9.2s
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/porter_boss_runner.json` — first RED: exit 1; `status: timeout`; action 11 `get_porter_boss_runner_miss_chance == 0.7` actual null
- same focused command after getter — exit 1; `status: fail`; miss log expectation failed (seed/load_map order)
- same focused command after harness strengthen — exit 0; `.gen/harness/porter_boss_runner/result.json` `status: pass`; elapsed 19.22s; apply/lock/reroute/miss log expectations all passed

## Notes
- Runner key is `godot-td`, workspace `poke-defense-godot/issue-porter-boss-runner`.
- Public miss API: `ProgressionManager.get_porter_boss_runner_miss_chance()`, `set_porter_boss_runner_next_miss_roll()`, `consume_porter_boss_runner_miss()`.
- Did not change `status.md` (checker owns classification). Did not publish dashboard events. Did not commit.
