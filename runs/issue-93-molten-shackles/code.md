# Coder report: 1\n\n# Coder report: 1 (molten-shackles perk catalog)

## Changed files
- `scripts/progression/fire_tower.json` — new `molten_shackles` Common perk, maxLevels 3, values 1/2/3
- `scripts/progression/managers/FireTowerProgressionManager.gd` — `_shackles_base_armor` / `_burn_level` state, `can_handle("molten_shackles")`, `_apply_molten_shackles()`, `get_molten_shackles_config()` returning {enabled, base_armor, burn_level, armor_per_tick}; enabled only when shackles base > 0 AND fire_burn config is enabled; armor_per_tick = base * max(1, burn_level)
- `autoload/ProgressionManager.gd` — `get_molten_shackles_config()` passthrough to the Fire manager
- `tests/scenarios/molten_shackles_progression.json` — new data/manager scenario

## Criteria
- molten_shackles Common, 3 levels; L1→2→3 then ineligible; reset_for_new_game() → level 0, config inactive — Done
- No fire burn perk → config enabled=false, armor_per_tick=0 at every owned level — Done
- With fire_burn active → armor_per_tick = base (1/2/3) × burn level multiplier — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/molten_shackles_progression.json` — exit 0; `[Harness] status=pass exit=0`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json` — exit 0; status=pass
- `godot --headless --path . --editor --quit-after 300` — exit 0

## Notes
- Scenario also covers save/load roundtrip at L3 (armor_per_tick stays 3) before the final reset.
- Scaling side proven by re-applying Ember Coat to L3 with shackles fixed at L1: armor_per_tick reads 3.
\n\n# Coder report: 2\n\n# Coder report: 2 (burn-tick armor shred)

## Changed files
- `scripts/game/status/BurnStatus.gd` — after each tick's HP damage lands (`_apply_damage`), `_apply_molten_shackles_armor_strip()` runs when `tower_type_id == &"fire"`: reads ProgressionManager.get_molten_shackles_config(), strips `armor_per_tick` via `enemy.set("armor", max(0.0, before - strip))`, debug log `[MOLTEN_SHACKLES] strip enemy=<id> armor=<before>-><after> amount=<n>`. No new VFX.
- `tests/scenarios/molten_shackles_armor_shred.json` — new five-arm game-test scenario on map_7 wave 6 (single armored Orc Enemy King, armor 60)

## Criteria
- Burn L1 + Shackles L1: armor 60→56 over 4 ticks, enemy alive — Done
- Higher burn level (L3) same shackles L1: armor 60→48 (3/tick vs 1/tick) — Done
- No burn perk owned → armor unchanged — Done (arm 1)
- Burn owned but no shackles → armor unchanged — Done (arm 3)
- Armor never below 0 (max(0,...)); HP identical with/without perk (strip lands after take_damage): all arms end hp=1615 — Done
- Visual parity structural: no VFX code added; same BurnStatus path — Done
- Debug [MOLTEN_SHACKLES] log per stripping tick naming enemy, before/after, amount — verified in harness output

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/molten_shackles_armor_shred.json` — exit 0; `[Harness] status=pass exit=0`
- Smoke + editor typecheck also pass (see cluster 1 report)

## Notes
- Strip is gated on `tower_type_id == &"fire"` so rocket burns are untouched.
- Scenario resets progression between arms because apply_progression persists across load_map.
- EnemyHealthController.gd was listed as an owned file but needed no change — the strip lives entirely in BurnStatus and uses the enemy's existing `armor` property.
\n