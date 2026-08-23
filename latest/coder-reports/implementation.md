# Coder report: implementation (revision 1)

## Changed files
- `tests/scenarios/warlords_doctrine.json` — modified (evidence-only change; no production code touched)

## Criteria addressed (were Pending after iteration 1)
- Applying `warlords_doctrine` at L1/L2/L3 raises the global tower-damage multiplier to exactly 1.05 / 1.09 / 1.14 — Done: new "Level leg" re-applies the perk three times and asserts level 1/2/3 with multiplier 1.05/1.09/1.14 via `progression_call` wait_for_conditions.
- Innate-armor enemy: bonus added on top of innate — Done: new map_7 wave-6 leg spawns Orc Enemy King with perk at L3; asserts `max_armor == 303.75` (= innate 60 + 15% × 1625) and `armor == 303.75`, then a scripted armor_hit with `armor_damage=243.75` strips exactly the granted portion, asserting armor back to innate 60 and hp 1620 (halved 5 HP damage per ARMOR_DAMAGE_REDUCTION).
- Armor bar row windowed visual check — still owned by the manual tester (headless `enemy_armor_bar_visual` leg passes; `.gen/manual-report.md` remains the manual artifact). Not code-verifiable here.

Also added a final reset assertion `get_warlords_armor_ratio() == 0.0` so post-reset spawns are provably innate-only.

## Commands and results (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-warlords-doctrine)
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json` — exit 0, `[Harness] status=pass exit=0`; log shows `[WARLORDS-DOCTRINE] applied L1/L2/L3 ... total_multiplier=1.05/1.09/1.14` and `[WARLORDS-DOCTRINE] spawn_bonus level=3 granted_armor=303.75 on Orc Enemy_boss`.
- Full legs: same invocation for `enemy_armor_ballista`, `enemy_armor_trap`, `enemy_armor_bar_visual` — each exit 0, `status=pass`.
- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0, no script errors.

## Notes
- The enemy armor pool is single-valued (`armor == max_armor`, no separate granted-armor ledger), so an oversized armor hit would eat innate armor too. The innate-armor leg must strip exactly the granted 243.75 to leave innate 60 intact.
- End-of-run scenario `expectations` are evaluated against final state: after adding the map_7 leg the run ends on map_7 and stats were reset, so the old expectations (`map_3`, instance_summary.8801.damage==21) failed; replaced with map_7 + doctrine level 0 + multiplier 1.0.
- Harness float equality is epsilon-based (`HarnessValues._compare_numbers`), so 303.75 and 243.75 assert cleanly.
- No production-code changes in this revision; iteration-1 gotchas (int HP damage, reset-only removal) still apply.
