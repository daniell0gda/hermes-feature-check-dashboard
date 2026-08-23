# Coder report: implementation (iteration 3)

## Changed files
- `tests/scenarios/enemy_armor_bar_visual.json` — modified

## Criteria addressed (was Pending: armor-bar windowed visual on previously-unarmored enemy)
- The existing armor bar row becomes visible on a previously-unarmored enemy once the perk grants it armor:
  the visual scenario now opens with a doctrine leg — apply `warlords_doctrine` L1, load map_3,
  trigger wave 1 so Mushnub (config names no armor) spawns with granted armor = 8% × 22 = 1.76
  (log: `[WARLORDS-DOCTRINE] spawn_bonus level=1 granted_armor=1.76 on Mushnub`). Screenshots at
  full / partial (armor 0.76 after one armor_hit of armor_damage=1) / depleted (bar hidden),
  exactly the beats in `.gen/ui_scenario.md`. The pre-existing innate-armor leg (map_7 Orc
  Enemy_boss, armor 60) runs second after `reset_for_new_game`, preserving its regression coverage.
- The actual windowed pixel check remains owned by the manual tester (`manual_testing: required`
  in plan.md); this revision makes the harness timeline they run exercise the doctrine-granted
  case directly instead of only innate armor.

## Commands and results (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-warlords-doctrine)
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json`
  — exit 0, `[Harness] status=pass exit=0`; log shows `[WARLORDS-DOCTRINE] applied L1/L2/L3 ...
  total_multiplier=1.05/1.09/1.14` and `spawn_bonus level=1 granted_armor=1.76 on Mushnub`,
  `spawn_bonus level=3 granted_armor=303.75 on Orc Enemy_boss`.
- `enemy_armor_bar_visual.json` (new two-leg version) — exit 0, status=pass; log shows
  `[Armor] Mushnub depleted: 0.76 armor removed by 1.0 armor damage` and screenshots
  armor_full / armor_partial / armor_depleted taken during the doctrine leg.
- Full legs: `enemy_armor_ballista.json`, `enemy_armor_trap.json` — each exit 0, status=pass
  (armor arithmetic regression intact).
- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0, no script errors.

## Notes
- No production code changed this iteration; iteration-1 gotchas still apply (int HP damage,
  reset-only removal, single-valued armor pool).
- Harness float assertions are epsilon-based, so armor 0.76 asserts cleanly after the first strip hit.
- Open advisory from quality-notes.md unchanged: `global-json-reformat-noise` (whitespace-only diff in
  scripts/progression/global.json), functional no-op.
