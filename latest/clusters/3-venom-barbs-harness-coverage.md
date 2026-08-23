# Cluster 3: venom-barbs-harness-coverage

- owned file scope: `tests/scenarios/traps_venom_barbs_progression.json`, `tests/scenarios/traps_venom_barbs_trap_poison.json`
- dependencies: 1, 2
- parallel: false

## Acceptance criteria

- A focused headless harness scenario proves the perk definition and level scaling: unowned yields no trap poison, and levels 1/2/3 yield strictly increasing poison totals through the exposed getter.
- A focused headless harness scenario proves live trap-hit poison: a scripted `Trap.perform_hit` on an underground enemy with the perk owned creates a `PoisonStatus`, deals lingering damage over time after the hit, and leaves the enemy's HP lower than an unowned-perk equivalent hit sequence; it also asserts the pre-existing trap hit damage and Undermining armor-strip behavior are unchanged alongside the new poison.

## Verification commands

- Focused test: `["bash", "-lc", "for f in tests/scenarios/traps_venom_barbs_progression.json tests/scenarios/traps_venom_barbs_trap_poison.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/traps_venom_barbs_progression.json tests/scenarios/traps_venom_barbs_trap_poison.json tests/scenarios/undermining_trap_armor.json tests/scenarios/traps_serrated_edges_progression.json tests/scenarios/enemy_armor_trap.json tests/scenarios/trap_stats_attribution.json tests/scenarios/progression_pick.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
