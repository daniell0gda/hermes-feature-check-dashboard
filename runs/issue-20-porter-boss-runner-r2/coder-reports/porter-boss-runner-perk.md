# Coder report: porter-boss-runner-perk

## Changed files
- `scripts/progression/porter_tower.json` — modified
- `scripts/progression/managers/PorterTowerProgressionManager.gd` — modified
- `autoload/ProgressionManager.gd` — modified
- `scripts/game/actors/towers/PorterTower.gd` — modified

## Criteria
- Unique perk `porter_boss_runner` is type Unique, is eligible before it is taken, and appears in a chest draw before it is taken. — Done
- Applying `porter_boss_runner` once owns it at level 1, makes it ineligible, and removes it from a chest draw; a further apply leaves the level at 1; `reset_for_new_game` returns it to unowned. — Done
- Without `porter_boss_runner` owned, a Porter in range of a live surface boss does not lock or charge that boss, and the boss stays on the surface. — Done
- With `porter_boss_runner` owned, a Porter in range of a live surface boss locks that boss and completes a full charge. — Done
- After a full Porter charge on a boss with `porter_boss_runner` owned, the boss remains in play and is rerouted onto an extra underground detour produced by `UGSystem.compute_underground_route` rather than being consumed or removed. — Done
- That boss reroute plays Porter's existing teleport dissolve VFX; dissolving is observable during charge completion. — Done
- Without `porter_boss_runner` owned, a Porter still teleports a non-boss surface enemy onto a valid underground route. — Done
- Debug-build [PORTER_BOSS_RUNNER] log line per perk apply — Done
- Debug-build [PORTER_BOSS_RUNNER] log line per boss lock event — Done
- Debug-build [PORTER_BOSS_RUNNER] log line per boss reroute event — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/porter_boss_runner.json` — exit code 0; status=pass in `.gen/harness/porter_boss_runner/result.json`

## Notes
- Public query: `ProgressionManager.is_porter_boss_runner_owned()`.
- 40% miss chance from the issue request is not in the plan and was not added.
