# Cluster: porter-boss-runner-harness

- cluster ID: porter-boss-runner-harness
- owned file scope: `tests/scenarios/porter_boss_runner.json`
- dependencies: porter-boss-runner-perk
- parallel: false

## Acceptance criteria

- Focused harness scenario `porter_boss_runner` covers perk off (no boss target) and perk on (charge then reroute plus VFX/path evidence) and finishes with `status: pass`.

## Verification

- Focused test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-boss-runner cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_boss_runner.json"]`
- Full test: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-boss-runner cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_wide_gate_reach.json"]`
- Typecheck/build: `run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-boss-runner cmd=["godot","--headless","--path",".","--editor","--quit-after","300"]`
