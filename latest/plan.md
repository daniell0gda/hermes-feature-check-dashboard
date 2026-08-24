# Acceptance Plan: Warlord's Doctrine — readable windowed armor-bar evidence (r2)

## Verification

- Focused test: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json"]`
- Full test: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_ballista.json && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_trap.json"]`
- Typecheck/build: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --headless --path . --editor --quit-after 300"]`

Windowed evidence command (manual_testing leg, not part of the three above):
`["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --path . --rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_bar_visual.json"]`

manual_testing: required — the deliverable of this run is human-readable windowed PNGs; headless passes alone do not satisfy it. A pass with unreadable bars is a fail.

## Clusters

1. readable-evidence-scenario — files: `tests/scenarios/enemy_armor_bar_visual.json`, `scripts/testing/HarnessActions.gd` (only if hiding the Debug Panel or live-enemy camera targeting needs a small harness capability addition) — depends on: none
- Before any screenshot in the evidence scenario, the Debug Panel (gray overlay covering the left third of the frame) is hidden, and no final evidence PNG shows it.
- The doctrine-leg screenshots frame the live Mushnub from map_3 wave 1 at its actual spawn position (camera aimed at the enemy's current world position, then updated for the surface layer), not a hardcoded map coordinate.
- In every doctrine-leg screenshot the HP row and the granted armor row are both individually readable at more than a couple of pixels (close zoom or enlarged bar scale for the shot only); if Mushnub's GLB model is missing in this worktree, the bars remain readable without faking armor on a naturally armored enemy.
- After one scripted armor hit against the doctrine-armored enemy, the screenshot visibly shows the armor row's fill smaller than at full armor.
- After enough scripted armor hits to deplete granted armor to 0, the final screenshot shows the armor row hidden while the HP row remains visible.
2. evidence-capture-and-regression — files: `.gen/screenshots/` (fresh windowed PNGs overwriting the unreadable r1 crops), `tests/scenarios/enemy_armor_bar_visual.json` — depends on: 1
- A fresh windowed (non-headless) run of the evidence scenario completes with all its checkpoints passing, and its PNGs are copied into `.gen/screenshots/` replacing the r1 crops.
- Each final evidence screenshot has no misplaced or clipped HUD elements and nothing covering the bars (`ui_feels_broken` equivalent reads clean).
- The headless `warlords_doctrine` scenario still passes after the scenario/camera changes (perk multipliers L1/L2/L3, Mushnub granted 1.76, Orc boss 303.75, reset to 0).
- The pre-existing `enemy_armor_ballista` and `enemy_armor_trap` scenarios still pass unchanged after the changes.

## Criteria

- Before any screenshot in the evidence scenario, the Debug Panel (gray overlay covering the left third of the frame) is hidden, and no final evidence PNG shows it.
- The doctrine-leg screenshots frame the live Mushnub from map_3 wave 1 at its actual spawn position (camera aimed at the enemy's current world position, then updated for the surface layer), not a hardcoded map coordinate.
- In every doctrine-leg screenshot the HP row and the granted armor row are both individually readable at more than a couple of pixels (close zoom or enlarged bar scale for the shot only); if Mushnub's GLB model is missing in this worktree, the bars remain readable without faking armor on a naturally armored enemy.
- After one scripted armor hit against the doctrine-armored enemy, the screenshot visibly shows the armor row's fill smaller than at full armor.
- After enough scripted armor hits to deplete granted armor to 0, the final screenshot shows the armor row hidden while the HP row remains visible.
- A fresh windowed (non-headless) run of the evidence scenario completes with all its checkpoints passing, and its PNGs are copied into `.gen/screenshots/` replacing the r1 crops.
- Each final evidence screenshot has no misplaced or clipped HUD elements and nothing covering the bars (`ui_feels_broken` equivalent reads clean).
- The headless `warlords_doctrine` scenario still passes after the scenario/camera changes (perk multipliers L1/L2/L3, Mushnub granted 1.76, Orc boss 303.75, reset to 0).
- The pre-existing `enemy_armor_ballista` and `enemy_armor_trap` scenarios still pass unchanged after the changes.
