# Cluster 3: harness-scenarios

- Files: `tests/scenarios/warlords_doctrine.json`, `tests/scenarios/enemy_armor_ballista.json`
- Dependencies: 1, 2
- parallel: false

## Acceptance criteria
- A `game-test` scenario activates `warlords_doctrine`, spawns a normally-unarmored enemy, and asserts `enemy.armor > 0` (and matching `max_armor`) at spawn; headless result is `pass`.
- The same scenario asserts the tower damage bonus end-to-end through the harness at one perk level (a scripted hit applies 1.05x/1.09x/1.14x the base damage).
- The pre-existing `enemy_armor_ballista` scenario still passes unchanged after the feature lands (armor arithmetic regression).

## Verification commands
- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/warlords_doctrine.json"]`
- Full test: `["bash", "-lc", "for s in warlords_doctrine enemy_armor_ballista enemy_armor_trap enemy_armor_bar_visual; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Manual testing: windowed run required for UI-sanity (see `.gen/ui_scenario.md`).
