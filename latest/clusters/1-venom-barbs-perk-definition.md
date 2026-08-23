# Cluster 1: venom-barbs-perk-definition

- owned file scope: `scripts/progression/trap.json`, `scripts/progression/managers/TrapProgressionManager.gd`, `autoload/ProgressionManager.gd`, `scripts/testing/HarnessValues.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- The `traps_venom_barbs` progression exists in the trap progression pool as a Unique perk and offers exactly 3 levels.
- With `traps_venom_barbs` at levels 1/2/3, the exposed poison total per trap hit scales strictly by level (level N deals more poison damage than level N−1), and reads as disabled (zero poison) when the perk is not owned.
- Taking a different trap perk (Serrated Edges or Undermining) does not enable trap poison, and owning `traps_venom_barbs` does not change trap hit damage or armor strip values.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_venom_barbs_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_venom_barbs_progression.json"]` plus the same token array re-run for each of: `traps_venom_barbs_trap_poison.json`, `undermining_trap_armor.json`, `traps_serrated_edges_progression.json`, `enemy_armor_trap.json`, `trap_stats_attribution.json`, `progression_pick.json` — each run must end `[Harness] status=pass exit=0`.
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
