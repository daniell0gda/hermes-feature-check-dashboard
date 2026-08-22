# Cluster 3: frozen-fracture-game-test-coverage

- Files: `tests/scenarios/frozen_fracture_slowed_vs_unslowed.json`, `tests/scenarios/frozen_fracture_levels_and_expiry.json`
- Dependencies: 1, 2
- Parallel: false

## Acceptance criteria

- A focused game-test scenario compares armor remaining after identical armor-damage hits on a slowed versus an unslowed enemy with the perk active, asserting the slowed enemy lost exactly the level-multiplied amount more.
- A focused game-test scenario asserts the three perk levels produce 10%/20%/30% extra armor damage respectively and that behavior reverts to baseline once the slow expires.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/frozen_fracture_slowed_vs_unslowed.json"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/frozen_fracture_*.json tests/scenarios/enemy_armor_ballista.json tests/scenarios/progression_pick.json tests/scenarios/ice_rate_matched_speeds.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
