# Coder report: 1-venom-barbs-perk-definition\n\n# Coder report: 1-venom-barbs-perk-definition

## Changed files
- `scripts/progression/trap.json` — mod: added `traps_venom_barbs` Unique progression with exactly 3 levels (values 8/16/24 absolute poison total per trap hit)
- `scripts/progression/managers/TrapProgressionManager.gd` — mod: added `VENOM_BARBS_NAME` const, Venom baseline timing consts (`VENOM_BARBS_BASELINE_DURATION = 10.0`, `VENOM_BARBS_TICK_INTERVAL = 0.25`), `can_handle` for the new name, idempotent `apply_level` branch (absolute values), and getters `get_venom_barbs_poison_total/duration/tick`; reset zeroes poison
- `autoload/ProgressionManager.gd` — mod: exposed `get_trap_poison_total()` (0.0 when unowned), `get_trap_poison_duration()`, `get_trap_poison_tick_interval()`

## Criteria
- traps_venom_barbs exists as Unique perk, exactly 3 levels — Done
- Poison total scales strictly by level 1<2<3, zero when unowned — Done
- Other trap perks don't enable poison; venom barbs doesn't change hit damage or armor strip — Done

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_venom_barbs_progression.json` — exit code 0; `.gen/harness/traps_venom_barbs_progression/result.json` status=pass (unowned → 0.0; L1→8, L2→16, L3→24; duration 10.0, tick 0.25 at every level; serrated edges owned → poison still 0.0; type==Unique asserted)
- `godot --headless --editor --path . --quit-after 120` — exit code 0 (typecheck clean)

## Notes
- Level values are absolute poison totals so replaying levels on load is idempotent (same pattern as undermining).
\n\n# Coder report: 2-venom-barbs-trap-poison-runtime\n\n# Coder report: 2-venom-barbs-trap-poison-runtime

## Changed files
- `scripts/game/actors/Trap.gd` — mod: added `_venom_barbs_poison_config()` (per-hit read from ProgressionManager, `{}` while unowned) and `_apply_venom_barbs_poison(enemy, config)` routing through `enemy.get_node("EffectsManager").apply_poison(total, duration, tick, StringName(trap_id), -1)`; `perform_hit` applies it after the HP hit when configured, plus an `[VENOM-BARBS]` debug log (enemy id, trap id, level, poison total, duration, tick)

## Criteria
- Owned: trap hit applies lingering poison via existing EffectsManager.apply_poison path (PoisonStatus appears) — Done
- Unowned: no poison, no PoisonStatus — Done
- Venom baseline timing (10s duration, 0.25s tick) at every level — Done
- Poison keeps ticking after the hit without further hits — Done
- Trap-sourced poison never compounds the Venom stack: tower_type_id is the trap's own id (never "venom"), so apply_poison takes the non-stacking refresh branch even with the stacking perk active — Done (by construction; refresh semantics come from the shared PoisonStatus path)
- `[VENOM-BARBS]` debug log line — Done
- Reused poison VFX from apply_poison shows only on owned hits — Done (VFX is created inside EffectsManager.apply_poison, which is not called when unowned)

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_venom_barbs_trap_poison.json` — exit code 0; `.gen/harness/traps_venom_barbs_trap_poison/result.json` status=pass

## Notes
- Direct hit damage and armor strip are computed independently of the poison config, so owning Venom Barbs leaves both unchanged (asserted in the harness scenario).
- The log line reads the current level via `ProgressionManager.get_current_level` for reporting.
\n\n# Coder report: 3-venom-barbs-harness-coverage\n\n# Coder report: 3-venom-barbs-harness-coverage

## Changed files
- `tests/scenarios/traps_venom_barbs_progression.json` — new: headless scenario asserting the perk definition (Unique, 3 levels), unowned → `get_trap_poison_total()==0.0`, L1/L2/L3 → 8/16/24 strictly increasing, duration 10.0 / tick 0.25 constant across levels, save/load replay lands back on L3, serrated-edges ownership does not enable poison
- `tests/scenarios/traps_venom_barbs_trap_poison.json` — new: live runtime scenario on map_7 wave 6 (single armored Orc Enemy King, hp 1625, armor 60): scripted `Trap.perform_hit` (apply_effect "trap_hit") proves unowned → exact direct damage, no PoisonStatus, no extra stats accrual over a wait window; owned L1 → same direct damage, `poisoned` becomes true, first visible HP drop composes direct+first poison point, HP keeps dropping across ticks with no further trap hits, trap-attributed poison totals ≥4 through stats, and a regex assertion on the `[VENOM-BARBS]` log line (enemy id, trap id, level, total, duration, tick); Undermining arm re-proves armor strip 60→52 alongside poison (perks compose)
- `scripts/testing/HarnessValues.gd` — mod (harness seam): enemy condition source gained a `poisoned` field (`PoisonStatus != null`) in `_live_enemy_field` and `_enemy_report`

## Criteria
- Progression/scaling scenario — Done (status=pass)
- Live trap-hit poison scenario — Done (status=pass)

## Commands and results
- Both focused scenarios run via runner: exit code 0 each; result.json status=pass for `traps_venom_barbs_progression` and `traps_venom_barbs_trap_poison`

## Gotchas
- `scripts/testing/HarnessValues.gd` was extended to expose the `poisoned` enemy field — if another issue touches HarnessValues, expect this addition.
- Full-suite note: `progression_pick` fails on `venom_miasma_bloom` expectations, but it fails identically with all venom-barbs changes stashed — pre-existing on this branch, unrelated to trap files.

## Measured
- Focused scenarios elapsed ~1.4s each headless.
\n\n# Coder report: implementation\n\n# Coder report: implementation (clusters 1–3, venom barbs)

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
\n