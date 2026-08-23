# Cluster 3: harness-scenarios

- Files: `tests/scenarios/warlords_doctrine.json`, `tests/scenarios/enemy_armor_ballista.json`
- Dependencies: 1, 2
- parallel: false

## Acceptance criteria
- A `game-test` scenario activates `warlords_doctrine`, spawns a normally-unarmored enemy, and asserts `enemy.armor > 0` (and matching `max_armor`) at spawn; headless result is `pass`.
- The same scenario asserts the tower damage bonus end-to-end through the harness at one perk level (a scripted hit applies 1.05x/1.09x/1.14x the base damage).
- The pre-existing `enemy_armor_ballista` scenario still passes unchanged after the feature lands (armor arithmetic regression).

## Verification commands
- Focused test: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 warlords_doctrine"]`
- Full test: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 warlords_doctrine"]`
- Typecheck/build: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 -Editor"]`

Manual testing: windowed run required for UI-sanity (see `.gen/ui_scenario.md`).
