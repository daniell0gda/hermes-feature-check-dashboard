# Cluster 1: perk-definition-and-damage-bonus

- Files: `scripts/progression/global.json`, `autoload/ProgressionManager.gd`
- Dependencies: none
- parallel: true

## Acceptance criteria
- The global progression pool contains a Common perk named `warlords_doctrine` with exactly 3 levels whose values encode +5%/+9%/+14% tower damage for L1/L2/L3.
- After applying `warlords_doctrine` at L1/L2/L3, the global tower damage multiplier equals 1.05/1.09/1.14 respectively, and it stacks additively with the existing `tower_dmg` perk rather than overriding its contribution.
- Taking and removing (`give`/`take`) the perk updates the applied level and the resulting damage multiplier consistently with how existing Common global perks behave, and resetting progression clears its effect to 1.0.

## Verification commands
- Focused test: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 warlords_doctrine"]`
- Full test: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 warlords_doctrine"]`
- Typecheck/build: `["run_project_cmd", "pwsh", "-Command", "$env:GODOT_BIN='/usr/local/bin/godot'; & .claude/skills/game-test/scripts/Run-Scenario.ps1 -Editor"]`
