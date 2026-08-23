# Coder report: implementation

## Changed files
- `scripts/progression/trap.json` — new `undermining` Common progression, 3 levels (8/15/25 armor damage)
- `scripts/progression/managers/TrapProgressionManager.gd` — handles `undermining`, `_armor_damage_bonus` state + `get_undermining_armor_damage()`; reset clears it
- `autoload/ProgressionManager.gd` — new accessor `get_trap_armor_damage()` (returns 0.0 when unowned)
- `scripts/game/actors/Trap.gd` — `_undermining_armor_damage()`, new `perform_hit(enemy)` entry point used by both real hit paths; `[Undermining]` debug log; strip tint dispatch
- `scripts/game/actors/effects/EffectsManager.gd` — preload + `play_undermining_strip()`, counter `undermining_strips`
- `scripts/game/actors/effects/UnderminingStripVFX.gd` — NEW one-shot amber wash VFX node
- `scripts/testing/HarnessActions.gd` — new `trap_hit` apply_effect effect (drives real `Trap.perform_hit`; prefers a placed trap near "pos", else a scripted in-tree trap) + `_nearest_trap`
- `tests/scenarios/undermining_progression.json` — NEW definition/persistence scenario
- `tests/scenarios/undermining_trap_armor.json` — NEW live trap behavior scenario
- `tests/scenarios/undermining_scope_isolation.json` — NEW surface-tower scope isolation scenario

## Criteria
All 10 plan criteria — Done.

## Commands and results
- Typecheck: `["godot","--headless","--editor","--path",".","--quit-after","300"]` — exit 0 (~9s); scripts parse clean.
- Focused: `... --harness=res://tests/scenarios/undermining_progression.json` — exit 0, `[Harness] status=pass exit=0`.
- `--harness=res://tests/scenarios/undermining_trap_armor.json` — exit 0 pass. Log shows the exact ladder: trap_01 unowned armor 60->60 (no strip), then trap_02 L1 60->52, trap_03 L2 52->37, trap_05 L3 37->12 and 12->0 clamped, with `[Undermining] strip ...` lines per stripping hit.
- `--harness=res://tests/scenarios/undermining_scope_isolation.json` — exit 0 pass. With undermining at L3, balista/generic armor_hits strip only their explicit armor_damage (60->40->35); fire_hit leaves armor untouched.
- Full test baseline: `--harness=res://tests/scenarios/enemy_armor_trap.json` — exit 0 pass (armor stays 60 through an unowned-perk trap-attributed hit).
- Regression: `--harness=res://tests/scenarios/traps_serrated_edges_progression.json` — exit 0 pass.
- Regression: `--harness=res://tests/scenarios/trap_stats_attribution.json` — exit 0 pass (full wave with live traps still kills via trap_01).

## Notes
- Trap.gd hit paths were consolidated into `perform_hit(enemy)` (both Area body_entered and overlap poll call it). It returns a detail dict {damage, armor_damage, armor_before/after, stripped} which the harness seam surfaces in result.json for exact arithmetic assertions.
- The tint is dispatched by EffectsManager.play_undermining_strip -> UnderminingStripVFX child (own-mesh material_override pattern, same as StaticBreachVFX shatter flash); counted in `undermining_strips` even headless so it is assertable without rendering.
- Scope isolation is structural: the perk value lives only on TrapProgressionManager and is consumed exclusively by Trap.gd; no tower code path reads get_trap_armor_damage.
- Gotchas for tester: see changes.md entries (progression-field type check must follow first apply; scripted trap must be inside tree).
