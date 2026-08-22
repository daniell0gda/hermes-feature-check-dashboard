# Cluster 1: undermining-perk-definition

- Files: `scripts/progression/trap.json`, `scripts/progression/managers/TrapProgressionManager.gd`, `autoload/ProgressionManager.gd`
- Dependencies: none
- Parallel: false

## Acceptance criteria

- A progression named `undermining` exists in the trap progression pool with type Common, exactly 3 levels, and is offered only for traps (never for any surface tower).
- With `undermining` at levels 1/2/3, the exposed trap armor-damage bonus is 8/15/25 respectively; when the perk is not owned it is 0.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/undermining_progression.json"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/undermining_*.json tests/scenarios/enemy_armor_trap.json tests/scenarios/traps_serrated_edges_progression.json tests/scenarios/trap_stats_attribution.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
