# Coder report: implementation (clusters 1–3, venom barbs)

## Changed files
- `scripts/progression/trap.json` — mod: new `traps_venom_barbs` Unique progression, 3 levels (absolute poison totals 8/16/24)
- `scripts/progression/managers/TrapProgressionManager.gd` — mod: handles `traps_venom_barbs`, idempotent absolute-value apply_level, getters `get_venom_barbs_poison_total/duration/tick`, baseline constants (10.0s duration / 0.25s tick from towers.xml venom)
- `autoload/ProgressionManager.gd` — mod: exposed `get_trap_poison_total()` (0.0 unowned), `get_trap_poison_duration()`, `get_trap_poison_tick_interval()`
- `scripts/game/actors/Trap.gd` — mod: `perform_hit` applies Venom Barbs poison via enemy's `EffectsManager.apply_poison` when configured; tower type id = trap's own id so the Venom compounding stack never engages (refresh branch only); `[VENOM-BARBS]` debug log; result dict gains `poison_total`; direct hit damage and Undermining strip computed independently of poison config
- `scripts/testing/HarnessValues.gd` — mod: enemy source gained `poisoned` field (`PoisonStatus != null`) in `_live_enemy_field` and `_enemy_report`
- `tests/scenarios/traps_venom_barbs_progression.json` — new: definition/level-scaling scenario through the exposed getters (unowned → 0; L1/2/3 → 8/16/24 strictly increasing; baseline timing constant across levels; save/load replay idempotent; other trap perk isolation)
- `tests/scenarios/traps_venom_barbs_trap_poison.json` — new: scripted `Trap.perform_hit` on map_7 wave 6 Orc Enemy King; proves PoisonStatus creation, lingering HP loss across ticks with no further hits, unchanged exact direct damage, unchanged armor strip alongside undermining owned, `[VENOM-BARBS]` log-line regex assertion, and no poison/VFX path when unowned
- `tests/scenarios/progression_pick.json` — mod: exhaustive-grant setup extended for `traps_venom_barbs` (x3), `undermining` (x2 more to max), plus previously missing `elemental_attunement` (x3), `overcharge_capacitors` (x3), `static_breach` (x3); notes updated

## Criteria
All cluster 1/2/3 criteria — Done.

## Commands and results
- Typecheck/build: `godot --headless --editor --path . --quit-after 120` — exit 0
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_venom_barbs_progression.json` — exit 0, `[Harness] status=pass exit=0` (result.json 2026-08-23T20:27:13 pass)
- `... traps_venom_barbs_trap_poison.json` — exit 0, status=pass (20:27:29). Log evidence: `[POISON] begin ... base=8.0 dur=10.0`, `[VENOM-BARBS] enemy=Orc Enemy_boss trap=trap_01 level=1 poison_total=8.0 duration=10.0 tick=0.25`, repeated `[POISON] tick apply` + hp drops 1625→1622→1621→1620 without further hits, `[Undermining] strip ... armor 60.0->52.0` unchanged
- `... undermining_trap_armor.json` — exit 0, status=pass (20:47:27)
- `... traps_serrated_edges_progression.json` — exit 0, status=pass (20:47:35)
- `... enemy_armor_trap.json` — exit 0, status=pass (20:47:43)
- `... trap_stats_attribution.json` — exit 0, status=pass (20:48:58)
- `... progression_pick.json` — initially FAIL (`venom_miasma_bloom` level read 0, modal answered "money"); after extending the exhaustive-grant setup → exit 0, status=pass (20:47:16), modal answered card index 2 name `venom_miasma_bloom`, level 1 and miasma enabled true

## Notes
- Root cause of the pre-existing `progression_pick` failure was twofold: (a) this issue adds `traps_venom_barbs` to the chest pool, and (b) my first fix only granted the trap perks once each — a perk is still chest-eligible until maxed, leaving pool=4 and a non-deterministic draw. The scenario must grant EVERY other perk to max levels so the pool collapses to exactly {venom_neurotoxin, venom_miasma_bloom}. The stale `.gen/harness/_logs/<id>.out.log` also short-circuits AgentHarness.materialize_engine_out_log (marker already present), hiding fresh logs — delete it if log assertions look stale.
- Trap-sourced poison passes the trap's own id as tower_type_id to apply_poison, so EffectsManager takes the non-stacking refresh branch even with the Venom stacking perk active.
- manual_testing remains required per plan (VFX/poison-cloud visual check in a debug build).
