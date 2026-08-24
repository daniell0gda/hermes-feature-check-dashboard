# Cluster 2: evidence-capture-and-regression

- owned files: `.gen/screenshots/` (fresh windowed PNGs overwriting the unreadable r1 crops), `tests/scenarios/enemy_armor_bar_visual.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- A fresh windowed (non-headless) run of the evidence scenario completes with all its checkpoints passing, and its PNGs are copied into `.gen/screenshots/` replacing the r1 crops.
- Each final evidence screenshot has no misplaced or clipped HUD elements and nothing covering the bars (`ui_feels_broken` equivalent reads clean).
- The headless `warlords_doctrine` scenario still passes after the scenario/camera changes (perk multipliers L1/L2/L3, Mushnub granted 1.76, Orc boss 303.75, reset to 0).
- The pre-existing `enemy_armor_ballista` and `enemy_armor_trap` scenarios still pass unchanged after the changes.

## Verification commands

- Focused test: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json"]`
- Full test: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_ballista.json && godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_trap.json"]`
- Typecheck/build: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --headless --path . --editor --quit-after 300"]`
- Windowed evidence: `["run_project_cmd", "project=godot-td", "workspace=poke-defense-godot/issue-warlords-doctrine", "godot --path . --rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_bar_visual.json"]`
