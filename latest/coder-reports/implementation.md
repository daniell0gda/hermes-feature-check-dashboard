# Coder report: implementation

## Changed files
No new source changes this pass (revision-code-3, revision 2). The implementation is present as uncommitted working-tree modifications from iteration 5:
- `scripts/progression/trap.json` — `undermining` Common trap perk, 3 levels (8/15/25 armor damage)
- `scripts/progression/managers/TrapProgressionManager.gd` — owns the undermining value
- `autoload/ProgressionManager.gd` — exposes `get_trap_armor_damage()`
- `scripts/game/actors/Trap.gd` — `perform_hit()` strips armor (clamped at 0), `[Undermining] strip` debug log, fires amber strip tint only when armor actually came off
- `scripts/game/actors/effects/EffectsManager.gd`, `scripts/game/actors/effects/UnderminingStripVFX.gd` (new) — amber UnderminingStripVFX tint
- `scripts/testing/HarnessActions.gd` — harness seams (`_trap_hit` add_childs the trap first)
- `tests/scenarios/undermining_progression.json`, `undermining_trap_armor.json`, `undermining_scope_isolation.json` (new)

## Criteria
- undermining-perk-definition — verified Done (undermining_progression pass; L1/L2/L3 -> 8.0/15.0/25.0 in log)
- undermining-trap-runtime — verified Done (undermining_trap_armor pass; live strips 60->52->37->12->0 clamped, unowned hit leaves armor exactly 60.0)
- undermining-hit-vfx — verified Done (tint fired via EffectsManager on stripping hits only; scenario passes)
- undermining-game-test-coverage — verified Done (all three new scenarios pass)

## Commands and results
All via run_project_cmd project=poke-defense-godot workspace=poke-defense-godot/issue-perk-undermining:
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/undermining_progression.json` — exit 0; [Harness] status=pass exit=0
- `... --harness=res://tests/scenarios/undermining_trap_armor.json` — exit 0; status=pass
- `... --harness=res://tests/scenarios/undermining_scope_isolation.json` — exit 0; status=pass
- `... --harness=res://tests/scenarios/enemy_armor_trap.json` — exit 0; status=pass
- `... --harness=res://tests/scenarios/trap_stats_attribution.json` — exit 0; status=pass
- `... --harness=res://tests/scenarios/traps_serrated_edges_progression.json` — exit 0; status=pass
- `godot --headless --editor --path . --quit-after 300` — exit 0

## Notes
- Fresh result.json files written under `.gen/harness/<scenario>/result.json` for all six runs.
- Key live evidence in the undermining_trap_armor log: `[Undermining] strip enemy=Orc Enemy_boss trap=trap_02 armor_damage=8.0 armor 60.0->52.0`, then trap_03 52->37, trap_05 37->12 and 12->0 (clamped); unowned trap_01 hit leaves armor exactly 60.0.
- Scope isolation: with undermining owned at L3, surface-tower scripted hits change armor only by their explicit armor_damage; no perk-derived loss.
- Pre-existing benign warnings (invalid UID ext_resources, missing GLB imports, RID leak at exit) appear in all scenarios including baseline ones; unrelated to this feature.
