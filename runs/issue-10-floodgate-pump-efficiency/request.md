# Request: #10 Floodgate Tower: Pump Efficiency — reduce cycle cooldown

Project: poke-defense-godot (runner key `godot-td`)
Workspace: `poke-defense-godot/issue-floodgate-pump-efficiency`
Branch: `issue/floodgate-pump-efficiency`
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/10
Slug: `floodgate-pump-efficiency`

## Problem

Floodgate (`scripts/game/actors/towers/FloodgateTower.gd`) has no progression. `cycle_cooldown` is a fixed value from `data/towers.xml` via `TowersConfig`.

## Done when

- New Common perk `floodgate_pump_efficiency` (L1-3): `cycle_cooldown` −10%/−20%/−30%.
- Implemented via a new `FloodgateTowerProgressionManager` + `floodgate_tower.json`, registered in `ProgressionManager.PROGRESSION_FILES`.
- Pure stat modifier: existing flood/drain cycle runs more often. No new visual required.

## Constraints

- Use `run_project_cmd` with project `godot-td` and workspace `poke-defense-godot/issue-floodgate-pump-efficiency`.
- Follow `/opt/data/coding_rules.md`.
- Do not commit, push, merge, or close the issue.
- Native Linux Godot verification through the runner.
