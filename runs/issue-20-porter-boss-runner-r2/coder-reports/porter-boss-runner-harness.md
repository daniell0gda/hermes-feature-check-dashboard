# Coder report: porter-boss-runner-harness

## Changed files
- `tests/scenarios/porter_boss_runner.json` — new

## Criteria
- Focused harness scenario `porter_boss_runner` covers perk off (no boss target) and perk on (charge then reroute plus VFX/path evidence) and finishes with `status: pass`. — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/porter_boss_runner.json` — exit code 0; status=pass; dissolving observed (optional wait ok); underground >= 1; Ninja_boss.count >= 1 after reroute; floodgate damage 4 on perk-off non-boss arm; apply/lock/reroute logs present

## Notes
- Arms: progression apply/reset → wave 7 perk-off (boss stays surface) → wave 7 perk-on (dissolve + underground) → wave 2 perk-off Floodgate probe.
- Do not wait on `source: log` mid-timeline; native materialize can freeze a stale out.log.
