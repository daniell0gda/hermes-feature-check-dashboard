# Cluster 2: frozen-fracture-armor-runtime

- Files: `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `autoload/ProgressionManager.gd`, `scripts/game/actors/towers/IceTower.gd` (only if slow-source attribution needs exposing)
- Dependencies: 1
- Parallel: false

## Acceptance criteria

- While an enemy is under an active Ice Tower slow, each point of incoming armor damage from any damage source is increased by the perk's level bonus (10%/20%/30%).
- An enemy that is not currently slowed takes unmodified armor damage even when `frozen_fracture` is owned.
- After an enemy's Ice slow expires, subsequent hits take unmodified armor damage again.
- The bonus scales armor damage only; the hit's HP damage is unchanged by the perk.
- A debug-build log line with a stable filterable `[FrozenFracture]` marker records each boosted armor-damage application (enemy id, level, bonus percent).

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/frozen_fracture_slowed_vs_unslowed.json"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/frozen_fracture_*.json tests/scenarios/enemy_armor_ballista.json tests/scenarios/progression_pick.json tests/scenarios/ice_rate_matched_speeds.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
