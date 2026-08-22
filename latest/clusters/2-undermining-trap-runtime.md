# Cluster 2: undermining-trap-runtime

- Files: `scripts/game/actors/Trap.gd`, `scripts/game/actors/enemy/parts/EnemyHealthController.gd`
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- Each hit from any of the four traps (`trap_01`, `trap_02`, `trap_03`, `trap_05`) on an armored enemy removes exactly the current `undermining` level's armor amount (8/15/25), clamped so armor never goes below zero, in addition to normal HP damage.
- Without the perk owned, a trap hit changes enemy armor by exactly zero (existing behaviour preserved).
- Surface tower hits are unchanged by `undermining`: owning it does not add armor damage to any non-trap tower's hits.
- Debug-build `[Undermining]` log line per armor-stripping trap hit

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/undermining_trap_armor.json"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/undermining_*.json tests/scenarios/enemy_armor_trap.json tests/scenarios/traps_serrated_edges_progression.json tests/scenarios/trap_stats_attribution.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
