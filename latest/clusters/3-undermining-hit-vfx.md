# Cluster 3: undermining-hit-vfx

- Files: `scripts/game/actors/Trap.gd`, `scripts/game/actors/effects/EffectsManager.gd`
- Dependencies: 2
- Parallel: false

## Acceptance criteria

- When an owned-perk trap hit actually strips armor, the trap's existing hit-impact effect shows a distinct tint signalling the armor-strip portion, and the tint is absent on trap hits while the perk is unowned.

Note: if a given trap currently has no impact VFX at all, that is a pre-existing gap and out of scope for this issue (per issue #91).

## Verification

- Focused test: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/undermining_*.json tests/scenarios/enemy_armor_trap.json tests/scenarios/traps_serrated_edges_progression.json tests/scenarios/trap_stats_attribution.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
