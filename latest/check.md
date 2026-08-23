# Check report: Warlord's Doctrine (revision-check 1, iteration 2)

classification: pass

## Verdict
Revision 1 closed both automated-evidence gaps from iteration 1. All runner gates pass fresh
in this check run via `run_project_cmd` (project=poke-defense-godot,
workspace=poke-defense-godot/issue-warlords-doctrine). 8 of 9 criteria are Done with
adequate automated evidence; the windowed armor-bar visual check remains Pending and is
owned by the manual tester (`.gen/manual-report.md` absent).

## Fresh verification (all via run_project_cmd, exit codes real)
- Probe: `godot --version` — exit 0 (4.4.1.stable.official).
- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0, no script errors.
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json`
  — exit 0, `[Harness] status=pass exit=0`, result at `.gen/harness/warlords_doctrine/result.json`
  (all timeline actions ok=true, all 3 end-of-run expectations pass=true).
- Full-suite legs (same invocation): `enemy_armor_ballista`, `enemy_armor_trap`,
  `enemy_armor_bar_visual` — each exit 0, `status=pass`.

## Criteria evidence

Cluster 1 — perk-definition-and-damage-chain:
- Catalog definition: DONE — verified in `scripts/progression/global.json`: `warlords_doctrine`,
  type Common, global file, maxLevels 3, L1 0.05/0.08, L2 0.09/0.12, L3 0.14/0.15.
- Multiplier exactly 1.05/1.09/1.14 per level: DONE — fresh focused run log shows
  `[WARLORDS-DOCTRINE] applied L1/L2/L3 ... total_multiplier=1.05/1.09/1.14`; scenario's Level leg
  applies the perk three times and asserts each multiplier via progression_call wait_for_conditions.
- Additive stacking with tower_dmg: DONE — scenario asserts 1.10 after applying tower_dmg on top;
  log `[PROGRESSION] apply tower_dmg L1 multiplier=1.10`.
- reset_for_new_game → 1.0 / ratio 0: DONE — scenario asserts multiplier==1.0, level==0, and
  `get_warlords_armor_ratio()==0.0` after reset; `_warlds_damage_ratio` cleared in both
  `reset_for_new_game()` and `_load_state_and_apply()`.

Cluster 2 — spawn-bonus-armor-and-health-bar:
- Unarmored enemy armored by perk / zero without: DONE — fresh log
  `[WARLORDS-DOCTRINE] spawn_bonus level=1 granted_armor=1.76 on Mushnub` (8% × hp 22);
  pre-perk baseline proven by `enemy_armor_ballista` leg (same Mushnub spawn, armor 0.0 config,
  raw damage lands).
- Innate-armor enemy gets bonus added on top: DONE — new map_7 wave-6 leg spawns Orc Enemy_boss
  (innate armor 60, hp 1625) with perk at L3; fresh run log
  `[WARLORDS-DOCTRINE] spawn_bonus level=3 granted_armor=303.75 on Orc Enemy_boss`
  (= 60 + 15% × 1625); scenario asserts max_armor==303.75, then an armor_hit with
  armor_damage=243.75 strips exactly the granted portion, asserting armor back to innate 60 and
  hp 1620 (halved 5 HP damage per normal armor rules).
- Scripted armor hit strips granted first, bonus lands in HP: DONE — result.json actions:
  armor_hit(21 dmg / 5 armor_damage) → `[Armor] Mushnub depleted: 1.76 armor removed by 5.0 armor
  damage`, enemy.hp==1 asserted, instance_summary damage==21 asserted.
- Armor bar windowed visual: PENDING — headless `enemy_armor_bar_visual` leg passes, but the
  criterion explicitly requires a windowed visual check owned by the manual tester;
  `.gen/manual-report.md` absent. This is a reserved manual checkpoint, not missing code.
- [WARLORDS-DOCTRINE] debug lines: DONE — both line shapes observed verbatim in the fresh focused
  run output (`applied L%d ... total_multiplier=` and `spawn_bonus level=... granted_armor=... on ...`),
  gated by `OS.is_debug_build()`, marker-filterable.

## Changed-file quality review (diff vs 97ed515)
Files: `autoload/ProgressionManager.gd`, `scripts/game/actors/Enemy.gd`,
`scripts/progression/global.json`, `tests/scenarios/warlords_doctrine.json` (new).
Reviewed against /opt/data/coding_rules.md + worktree CLAUDE.md:
typed locals, enum-free simple logic, additive change to existing modifier chain without touching
legacy paths, no dead code, no speculative abstraction. Revision 1 touched only the test scenario.
No demotions.
- Test overlap: `tests/scenarios/warlords_doctrine.json` does not duplicate existing coverage —
  `enemy_armor_*` scenarios run pre-perk and serve as its zero-armor baseline.
- Open advisory (unchanged since iteration 1, still visible in diff):
  `global-json-reformat-noise` — whitespace-only reformatting of unrelated entries in
  `scripts/progression/global.json` inflates the diff. Advisory only, stays open in
  `.gen/quality-notes.md`.

## Blockers
None. Runner healthy throughout; no infra failures.

## Unverified items
- Windowed armor-bar visual check — awaiting `.gen/manual-report.md` from the manual tester.
