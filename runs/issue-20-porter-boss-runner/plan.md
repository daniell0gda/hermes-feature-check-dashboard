# Acceptance Plan: Porter Boss Runner

## Verification

- Focused test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-boss-runner cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_boss_runner.json"]`
- Full test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-boss-runner cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_wide_gate_reach.json"]`
- Typecheck/build: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-boss-runner cmd=["godot","--headless","--path",".","--editor","--quit-after","300"]`
- manual_testing: required

## Clusters

1. porter-boss-runner-perk — files: `scripts/progression/porter_tower.json`, `scripts/progression/managers/PorterTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `scripts/game/actors/towers/PorterTower.gd` — depends on: none
- Unique perk `porter_boss_runner` is type Unique, is eligible before it is taken, and appears in a chest draw before it is taken.
- Applying `porter_boss_runner` once owns it at level 1, makes it ineligible, and removes it from a chest draw; a further apply leaves the level at 1; `reset_for_new_game` returns it to unowned.
- Without `porter_boss_runner` owned, a Porter in range of a live surface boss does not lock or charge that boss, and the boss stays on the surface.
- With `porter_boss_runner` owned, a Porter in range of a live surface boss locks that boss and completes a full charge.
- After a full Porter charge on a boss with `porter_boss_runner` owned, the boss remains in play and is rerouted onto an extra underground detour produced by `UGSystem.compute_underground_route` rather than being consumed or removed.
- That boss reroute plays Porter's existing teleport dissolve VFX; dissolving is observable during charge completion.
- Without `porter_boss_runner` owned, a Porter still teleports a non-boss surface enemy onto a valid underground route.
- Debug-build [PORTER_BOSS_RUNNER] log line per perk apply
- Debug-build [PORTER_BOSS_RUNNER] log line per boss lock event
- Debug-build [PORTER_BOSS_RUNNER] log line per boss reroute event
2. porter-boss-runner-harness — files: `tests/scenarios/porter_boss_runner.json` — depends on: 1
- Focused harness scenario `porter_boss_runner` covers perk off (no boss target) and perk on (charge then reroute plus VFX/path evidence) and finishes with `status: pass`.

## Criteria

- Unique perk `porter_boss_runner` is type Unique, is eligible before it is taken, and appears in a chest draw before it is taken.
- Applying `porter_boss_runner` once owns it at level 1, makes it ineligible, and removes it from a chest draw; a further apply leaves the level at 1; `reset_for_new_game` returns it to unowned.
- Without `porter_boss_runner` owned, a Porter in range of a live surface boss does not lock or charge that boss, and the boss stays on the surface.
- With `porter_boss_runner` owned, a Porter in range of a live surface boss locks that boss and completes a full charge.
- After a full Porter charge on a boss with `porter_boss_runner` owned, the boss remains in play and is rerouted onto an extra underground detour produced by `UGSystem.compute_underground_route` rather than being consumed or removed.
- That boss reroute plays Porter's existing teleport dissolve VFX; dissolving is observable during charge completion.
- Without `porter_boss_runner` owned, a Porter still teleports a non-boss surface enemy onto a valid underground route.
- Debug-build [PORTER_BOSS_RUNNER] log line per perk apply
- Debug-build [PORTER_BOSS_RUNNER] log line per boss lock event
- Debug-build [PORTER_BOSS_RUNNER] log line per boss reroute event
- Focused harness scenario `porter_boss_runner` covers perk off (no boss target) and perk on (charge then reroute plus VFX/path evidence) and finishes with `status: pass`.
