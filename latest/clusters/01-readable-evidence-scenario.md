# Cluster 1: readable-evidence-scenario

- owned files: `tests/scenarios/enemy_armor_bar_visual.json`, `scripts/testing/HarnessActions.gd` (only if hiding the Debug Panel or live-enemy camera targeting needs a small harness capability addition)
- dependencies: none
- parallel: true

## Acceptance criteria

- Before any screenshot in the evidence scenario, the Debug Panel (gray overlay covering the left third of the frame) is hidden, and no final evidence PNG shows it.
- The doctrine-leg screenshots frame the live Mushnub from map_3 wave 1 at its actual spawn position (camera aimed at the enemy's current world position, then updated for the surface layer), not a hardcoded map coordinate.
- In every doctrine-leg screenshot the HP row and the granted armor row are both individually readable at more than a couple of pixels (close zoom or enlarged bar scale for the shot only); if Mushnub's GLB model is missing in this worktree, the bars remain readable without faking armor on a naturally armored enemy.
- After one scripted armor hit against the doctrine-armored enemy, the screenshot visibly shows the armor row's fill smaller than at full armor.
- After enough scripted armor hits to deplete granted armor to 0, the final screenshot shows the armor row hidden while the HP row remains visible.

## Verification commands

- Focused test: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json"]`
- Full test: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_bar_visual.json"]`
- Typecheck/build: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --headless --path . --editor --quit-after 300"]`
- Windowed evidence: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --path . --rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_bar_visual.json"]`
