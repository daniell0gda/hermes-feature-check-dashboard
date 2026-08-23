# Cluster 3: undermining-hit-vfx

- Files: `scripts/game/actors/Trap.gd`, `scripts/game/actors/effects/EffectsManager.gd`
- Dependencies: 2
- Parallel: false

## Acceptance criteria

- When an owned-perk trap hit actually strips armor, the trap's existing hit-impact effect shows a distinct tint signalling the armor-strip portion, and the tint is absent on trap hits while the perk is unowned.

Note: if a given trap currently has no impact VFX at all, that is a pre-existing gap and out of scope for this issue (per issue #91).

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/undermining_trap_armor.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/enemy_armor_trap.json"]` plus per-scenario runs of `tests/scenarios/trap_stats_attribution.json`, `tests/scenarios/traps_serrated_edges_progression.json`, and each new `undermining_*.json` scenario below (same command shape)
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "300"]`
