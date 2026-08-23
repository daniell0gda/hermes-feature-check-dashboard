# Coder report: implementation (clusters 1–3)

## Changed files
- `scripts/progression/global.json` — mod: new `sundering_bolts` Common perk, 3 levels (0.10 / 0.20 / 0.35)
- `scripts/progression/managers/CurseProgressionManager.gd` — mod: handles `sundering_bolts`, absolute per-level ratio, `get_sundering_bolts_config()` ({enabled, ratio}), reset clears it
- `autoload/ProgressionManager.gd` — mod: `get_sundering_bolts_config()` pass-through to the curse manager
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — mod: `_apply_sundering_bolts_if_needed(final_hit_damage)` called in `take_damage` after all damage modifiers; converts ratio × final damage into extra armor drain via `_consume_armor`, debug `[SUNDERING_BOLTS] sunder ... level=N base_damage=X armor_damage=Y` log
- `tests/scenarios/sundering_bolts_progression.json` — new: focused harness scenario

## Criteria — all Done
Perk definition/eligibility/config/reset; take_damage application (final-damage base, zero-damage guard via `final_hit_damage <= 0.0`, additive with flat armor_dmg, no-op unowned); harness scenario.

## Commands and results (all via run_project_cmd)
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/sundering_bolts_progression.json` — exit 0; `[Harness] status=pass`. Observed log lines:
  - `[SUNDERING_BOLTS] sunder enemy=Orc Enemy_boss level=1 base_damage=5.0 armor_damage=0.5`
  - `level=2 base_damage=5.0 armor_damage=1.0`
  - `level=3 base_damage=5.0 armor_damage=1.75`
  Armor assertions 60→40 (baseline), →39.5 / 39.0 / 38.25 at L1/L2/L3 all passed.
- `godot --headless --path . res://tests/enemy/test_enemy_armor_damage.tscn` — exit 0; 18 ok, 0 failed.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_ballista.json` — exit 0; status=pass.
- Typecheck/build `godot --headless --path . --editor --quit-after 2` — exit 0, scripts compile clean.
- `godot --headless --path . res://tests/tower/test_tower_armor_damage.tscn` — exit 1: **15 ok, 3 failed** — PRE-EXISTING on pristine baseline (verified by stashing my changes and rerunning; identical failures). Cause: `[BalistaTower] WARNING: Cannot fire - no bolt found in model` in this worker's model-import state; the bolt never reaches the enemy, so my hook is never involved.

## Notes for tester
- Conversion base is the hit's FINAL damage after ARMOR_DAMAGE_REDUCTION etc., not towers.xml base: scenario asserts 60→39.5/39.0/38.25 (base 5 = 10 × 0.5 while armor remains).
- Sunder runs before HP accumulation but after armor depletion checks; a hit that fully strips armor still sunders nothing extra afterward (`enemy.armor <= 0.0` fast escape).
- Fresh worktree needs one `--editor --quit-after 300` import pass before any harness run, else GLB loads fail.
