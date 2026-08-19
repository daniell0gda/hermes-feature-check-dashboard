# Acceptance Plan: Porter Boss Runner

## Verification

- Focused test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-boss-runner cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_boss_runner.json"]`
- Full test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-boss-runner cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_wide_gate_reach.json"]`
- Typecheck/build: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-boss-runner cmd=["godot","--headless","--path",".","--editor","--quit-after","300"]`

manual_testing: required

## Clusters

1. porter-boss-runner-perk — files: `scripts/progression/porter_tower.json`, `scripts/config/Balance.gd`, `scripts/progression/managers/PorterTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `scripts/game/actors/towers/PorterTower.gd` — depends on: none
- Unique perk `porter_boss_runner` is type Unique, is eligible before it is taken, and appears in a chest draw before it is taken.
- Applying `porter_boss_runner` owns it at level 1, then 2, then 3; it stays eligible and appears in a chest draw until level 3; a further apply leaves the level at 3; `reset_for_new_game` returns it to unowned level 0.
- Without `porter_boss_runner` owned, a Porter in range of a live surface boss does not lock or charge that boss, and the boss stays on the surface.
- With `porter_boss_runner` owned, a Porter in range of a live surface boss locks that boss and completes a full charge.
- After a successful full Porter charge on a boss with `porter_boss_runner` owned, the boss remains in play and is rerouted onto an extra underground detour produced by `UGSystem.compute_underground_route` rather than being consumed or removed.
- A successful boss reroute plays Porter's existing teleport dissolve VFX; dissolving is observable during charge completion.
- After a completed Porter charge on a boss that misses, the boss stays on the surface, is not rerouted, and the success teleport dissolve cue does not play.
- Level 1 miss chance is Balance `porter_boss_runner.miss_chance` 0.7; each extra level subtracts `miss_reduction_per_level` 0.05 (L2 0.65, L3 0.60). A seeded roll below the current chance misses; a seeded roll at or above it hits.
- Without `porter_boss_runner` owned, a Porter still teleports a non-boss surface enemy onto a valid underground route.
- Debug-build [PORTER_BOSS_RUNNER] log line per perk apply
- Debug-build [PORTER_BOSS_RUNNER] log line per boss lock event
- Debug-build [PORTER_BOSS_RUNNER] log line per boss reroute event
- Debug-build [PORTER_BOSS_RUNNER] log line per boss miss event
2. porter-boss-runner-harness — files: `tests/scenarios/porter_boss_runner.json` — depends on: 1
- Focused harness scenario `porter_boss_runner` covers perk off (no boss target), perk on plus a hit seed (charge then reroute), and perk on plus a miss seed (charge spent, no reroute), and finishes with `status: pass`.

## Criteria

- Unique perk `porter_boss_runner` is type Unique, is eligible before it is taken, and appears in a chest draw before it is taken.
- Applying `porter_boss_runner` owns it at level 1, then 2, then 3; it stays eligible and appears in a chest draw until level 3; a further apply leaves the level at 3; `reset_for_new_game` returns it to unowned level 0.
- Without `porter_boss_runner` owned, a Porter in range of a live surface boss does not lock or charge that boss, and the boss stays on the surface.
- With `porter_boss_runner` owned, a Porter in range of a live surface boss locks that boss and completes a full charge.
- After a successful full Porter charge on a boss with `porter_boss_runner` owned, the boss remains in play and is rerouted onto an extra underground detour produced by `UGSystem.compute_underground_route` rather than being consumed or removed.
- A successful boss reroute plays Porter's existing teleport dissolve VFX; dissolving is observable during charge completion.
- After a completed Porter charge on a boss that misses, the boss stays on the surface, is not rerouted, and the success teleport dissolve cue does not play.
- Level 1 miss chance is Balance `porter_boss_runner.miss_chance` 0.7; each extra level subtracts `miss_reduction_per_level` 0.05 (L2 0.65, L3 0.60). A seeded roll below the current chance misses; a seeded roll at or above it hits.
- Without `porter_boss_runner` owned, a Porter still teleports a non-boss surface enemy onto a valid underground route.
- Debug-build [PORTER_BOSS_RUNNER] log line per perk apply
- Debug-build [PORTER_BOSS_RUNNER] log line per boss lock event
- Debug-build [PORTER_BOSS_RUNNER] log line per boss reroute event
- Debug-build [PORTER_BOSS_RUNNER] log line per boss miss event
- Focused harness scenario `porter_boss_runner` covers perk off (no boss target), perk on plus a hit seed (charge then reroute), and perk on plus a miss seed (charge spent, no reroute), and finishes with `status: pass`.
