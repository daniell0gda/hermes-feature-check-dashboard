# Coder report: implementation

## Changed files
- `scripts/progression/floodgate_tower.json` — adds `floodgate_corrosive_soak` perk (maxLevels 3, Unique, towers ["floodgate"]; levels carry absolute amplification 0.25 / 0.45 / 0.70, duration 6s)
- `scripts/progression/managers/FloodgateTowerProgressionManager.gd` — corrosive-soak state (`get_corrosive_soak_config()`: enabled/level/amplification/duration), absolute per-level application (no compounding), `reset_for_new_game()` clears it, `[FLOODGATE]` debug log per level application
- `autoload/ProgressionManager.gd` — `get_floodgate_corrosive_soak_config()` delegating to the Floodgate manager
- `scripts/game/actors/towers/FloodgateTower.gd` — `_mark_corroded_if_needed()` called on each discharge hit when the perk is owned
- `scripts/game/actors/enemy/parts/EnemyStatusController.gd` — `apply_corroded(dur, level, amplification)` + `update_corroded` expiry; `[CORROSIVE_SOAK]` applied/expired log lines
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — `_apply_corrosive_soak_if_needed()`: amplifies armor damage only for non-Floodgate attackers (exclusion), HP damage unchanged; `[CORROSIVE_SOAK]` amplified-hit log with bonus % and attacker_excluded flag
- `scripts/game/actors/enemy/parts/Enemy.gd`, `scripts/game/underground/WaterSubmersionSystem.gd` — corroded fields; rust tint via water-submersion tint mechanism (`set_enemy_corroded`), reverts to normal wet/soak tint on expiry
- `scripts/testing/HarnessActions.gd` — harness effects `floodgate_hit` (with `mark_corroded`), `armor_hit` (tower_type_id/instance_id), `set_armor`, `clear_corroded`; all target by enemy_id, not index
- `scripts/testing/HarnessValues.gd` — enemy-report pins: `staged.<id>.<field>` selecting live instance by max armor > 0; report fields `corroded_count`, `corroded_level`, `corroded_amplification`, `rust_tint_count`
- `tests/scenarios/floodgate_corrosive_soak.json` — new focused scenario

## Criteria
All 13 plan criteria — Done (verified via focused harness + editor gate; see below). Master three-way-merged ProgressionManager behaviors untouched beyond the added getter.

## Commands and results
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/floodgate_corrosive_soak.json` — exit 0; `[Harness] status=pass exit=0`. Raw stdout contains `[FLOODGATE] floodgate_corrosive_soak L1/L2/L3 applied -> amplification 0.25/0.45/0.7`, `[CORROSIVE_SOAK] corroded applied on enemy=Alien level=1`, amplified-hit lines bonus=25%/45%/70% armor_dmg=50/58/68, `corroded expired`, no Parse Error after editor-gate rebuild.
- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0.
- Full test: `res://tests/tower/test_tower_armor_damage.tscn` — exit 1, `15 ok, 3 failed`. **Pre-existing**: stashing all working-tree changes reproduces the identical 15 ok / 3 failed (FAIL lines: ballista bolt not reaching armored enemy) on the clean baseline, so the failures are environmental to this fresh worktree, not introduced by this feature. r2 plan notes recorded 18 ok / 0 failed on the previous tree state.

## Notes
- First focused run on the fresh worktree printed Parse Errors ("Identifier PerkPresentation not declared" in PerkCard.gd cascading into HarnessValues/HarnessActions/AgentHarness). Running the editor gate once rebuilt Godot's global class cache and the errors disappeared; subsequent harness runs are clean. Checker should run the editor gate before judging parse-error noise on a fresh checkout.
- `.glb` Failed-loading-resource errors (Alien.glb, stylized_earth.glb, portal arch) are pre-existing LFS import noise, explicitly non-gating per plan.
- Rust tint is player-facing: manual windowed screenshot evidence still required per `.gen/ui_scenario.md` (not assertable headless).
