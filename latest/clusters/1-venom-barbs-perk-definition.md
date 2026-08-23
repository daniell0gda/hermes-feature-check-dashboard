# Cluster 1: venom-barbs-perk-definition

- owned file scope: `scripts/progression/trap.json`, `scripts/progression/managers/TrapProgressionManager.gd`, `autoload/ProgressionManager.gd`
- dependencies: none
- parallel: false

## Acceptance criteria

- The `traps_venom_barbs` progression exists in the trap progression pool as a Unique perk and offers exactly 3 levels.
- With `traps_venom_barbs` at levels 1/2/3, the exposed poison total per trap hit scales strictly by level (level N deals more poison damage than level N−1), and reads as disabled (zero poison) when the perk is not owned.
- Taking a different trap perk (Serrated Edges or Undermining) does not enable trap poison, and owning `traps_venom_barbs` does not change trap hit damage or armor strip values.

## Verification commands

- Focused test: `["bash", "-lc", "for f in tests/scenarios/traps_venom_barbs_progression.json tests/scenarios/traps_venom_barbs_trap_poison.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/traps_venom_barbs_progression.json tests/scenarios/traps_venom_barbs_trap_poison.json tests/scenarios/undermining_trap_armor.json tests/scenarios/traps_serrated_edges_progression.json tests/scenarios/enemy_armor_trap.json tests/scenarios/trap_stats_attribution.json tests/scenarios/progression_pick.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
