# Cluster 1: frozen-fracture-perk-definition

- Files: `scripts/progression/global.json`, `scripts/progression/managers/CurseProgressionManager.gd` (or a sibling global-Common manager following it), `autoload/ProgressionManager.gd`
- Dependencies: none
- Parallel: false

## Acceptance criteria

- The `frozen_fracture` perk exists in the global Common progression pool as type Common with exactly 3 levels.
- With `frozen_fracture` at levels 1/2/3, the exposed armor-damage bonus is 10%/20%/30% respectively; when the perk is not owned the bonus is disabled (no increase).
- A chest draw that includes the pool offers `frozen_fracture` alongside other Common perks, and its level descriptions state the armor-damage bonus values.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/frozen_fracture_levels_and_expiry.json"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/frozen_fracture_*.json tests/scenarios/enemy_armor_ballista.json tests/scenarios/progression_pick.json tests/scenarios/ice_rate_matched_speeds.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
