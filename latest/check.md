# Check report: Warlord's Doctrine (iteration 3, fresh verification)

classification: pass

## Verdict
Fresh verification this iteration confirms all automated gates green via `run_project_cmd`
(project=poke-defense-godot, workspace=poke-defense-godot/issue-warlords-doctrine).
8 of 9 criteria remain Done with adequate automated evidence; the windowed armor-bar
visual criterion stays Pending — it is an explicit windowed checkpoint owned by the
manual tester (`manual_testing: required` in plan.md; `.gen/manual-report.md` absent),
not missing implementation or missing harness coverage.

## Fresh verification (this run, all exit codes from run_project_cmd)
- Probe: `godot --version` — exit 0 (4.4.1.stable.official.49a5bc7b6).
- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0,
  no script errors.
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json`
  — exit 0, `[Harness] status=pass exit=0`; fresh result at
  `.gen/harness/warlords_doctrine/result.json`: all 47 timeline actions ok=true, all 3
  end-of-run expectations pass=true.
- Full-suite legs (each its own invocation): `enemy_armor_ballista.json` — exit 0,
  status=pass; `enemy_armor_trap.json` — exit 0, status=pass;
  `enemy_armor_bar_visual.json` — exit 0, status=pass (doctrine leg first: Mushnub with
  granted armor 1.76, screenshots at full/partial/depleted beats; innate-armor map_7 leg
  second after reset).

## Criteria evidence

Cluster 1 — perk-definition-and-damage-chain:
- Catalog definition: DONE — `scripts/progression/global.json` contains `warlords_doctrine`,
  type Common, maxLevels 3, L1 value 0.05/armor_bonus 0.08, L2 0.09/0.12, L3 0.14/0.15.
- Multiplier exactly 1.05/1.09/1.14 per level: DONE — fresh focused run log shows
  `[WARLORDS-DOCTRINE] applied L1/L2/L3 tower_damage_bonus=... total_multiplier=1.05/1.09/1.14`;
  result.json progression_call assertions pass for each level.
- Additive stacking with tower_dmg: DONE — log `[PROGRESSION] apply tower_dmg L1 multiplier=1.10`
  (tower_dmg on top of doctrine); implementation adds `_warlords_damage_ratio` to
  `get_global_damage_multiplier()` alongside `_global_damage_ratio_sum`, never overriding.
- reset_for_new_game → multiplier 1.0 / armor ratio 0: DONE — result.json final expectations:
  progression_call == 0 (level) and == 1.0 (multiplier) both pass;
  `_warlords_damage_ratio` cleared in both `reset_for_new_game()` and `_load_state_and_apply()`.

Cluster 2 — spawn-bonus-armor-and-health-bar:
- Unarmored enemy armored by perk / zero without: DONE — fresh log
  `[WARLORDS-DOCTRINE] spawn_bonus level=1 granted_armor=1.76 on Mushnub` (= 8% × hp 22);
  zero-armor baseline proven by pre-perk legs (`enemy_armor_ballista`/`enemy_armor_trap`:
  same Mushnub spawns with config armor 0.0 and raw damage lands).
- Innate armor additive: DONE — map_7 leg spawns Orc Enemy_boss (innate armor 60, hp 1625)
  at doctrine L3: fresh log `[WARLORDS-DOCTRINE] spawn_bonus level=3 granted_armor=303.75 on Orc Enemy_boss`
  (= 60 + 15% × 1625); scenario asserts max_armor==303.75 then strips exactly the granted
  portion back to innate 60. Code: `max_armor += float(max_hp) * armor_ratio` — additive.
- Scripted armor hit strips granted first / bonus lands in HP: DONE — result.json actions
  pass: armor_hit on doctrine-armored Mushnub → `[Armor] Mushnub depleted: 1.76 armor removed by 5.0
  armor damage`, hp==1 asserted, instance_summary damage==21 asserted.
- Armor bar windowed visual: PENDING — headless `enemy_armor_bar_visual` passes end-to-end
  including the doctrine-granted leg with screenshot beats, but the criterion explicitly
  requires a windowed pixel check owned by the manual tester; `.gen/manual-report.md` absent.
  Reserved manual checkpoint, not missing code.
- `[WARLORDS-DOCTRINE]` debug lines: DONE — both shapes observed verbatim in fresh output
  (`applied L%d tower_damage_bonus=%.2f total_multiplier=%.2f`,
  `spawn_bonus level=N granted_armor=X on <id>`), gated by `OS.is_debug_build()`, marker-filterable.

Cluster 3 — harness-scenarios:
- Scenario activates perk + asserts armor>0/max_armor at spawn: DONE — warlords_doctrine.json
  actions/expectations all pass (fresh result.json).
- Tower damage bonus asserted end-to-end at one level: DONE — progression_call assertions of
  1.05/1.09/1.14 pass through the harness timeline.
- Pre-existing scenarios still pass unchanged: DONE — enemy_armor_ballista and enemy_armor_trap
  each exit 0, status=pass fresh this run.

## Changed-file quality review (diff vs baseline 97ed515)
Files changed: `autoload/ProgressionManager.gd`, `scripts/game/actors/Enemy.gd`,
`scripts/progression/global.json`, `tests/scenarios/enemy_armor_bar_visual.json`;
new file `tests/scenarios/warlords_doctrine.json`.
Reviewed against `/opt/data/coding_rules.md` + worktree `CLAUDE.md`: typed locals throughout
new code, small additive branches inside existing apply/spawn paths, no dead code, no
speculative abstraction, debug logs follow the project's `OS.is_debug_build()` + `[TAG]`
convention, indentation valid. No demotions.
- Test overlap: `warlords_doctrine.json` does not duplicate existing coverage — the
  `enemy_armor_*` scenarios run without the perk and serve as its zero-armor baseline.
- Open advisory (unchanged since iteration 1): `global-json-reformat-noise` — whitespace-only
  reformatting of unrelated entries in `scripts/progression/global.json` inflates the diff.
  Advisory only; remains open in `.gen/quality-notes.md`.

## Blockers
None. Runner healthy throughout; no infra failures.

## Unverified items
- Windowed armor-bar visual check — awaiting `.gen/manual-report.md` from the manual tester.
