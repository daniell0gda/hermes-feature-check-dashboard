# Check report: perk-sundering-bolts (iteration 2)

classification: fixable

## Verdict

Implementation is correct and all feature criteria are verified green through
the approved runner, but the plan's full-test command fails at its second leg
(`res://tests/tower/test_tower_armor_damage.tscn`, exit 1). Checker confirmed
independently (stash → clean tree @97ed515 → rerun) that the failure is
pre-existing on the pristine baseline — identical 15 ok / 3 failed with zero
mentions of `sunder` in the failing test. Because a full-suite leg fails, no
item may remain Done; all items moved to Pending.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-perk-sundering-bolts)

- Preflight `git status --short` — exit 0.
- Typecheck/build `godot --headless --path . --editor --quit-after 2` — exit 0,
  scripts compile clean.
- Focused harness `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/sundering_bolts_progression.json` — exit 0;
  `[Harness] status=pass exit=0`; result.json: 0 failed actions, status pass.
  Observed log lines:
  - `[SUNDERING_BOLTS] sunder enemy=Orc Enemy_boss level=1 base_damage=5.0 armor_damage=0.5`
  - `level=2 base_damage=5.0 armor_damage=1.0`
  - `level=3 base_damage=5.0 armor_damage=1.75`
  Armor assertions passed: 60→40 baseline, →39.5 / 39.0 / 38.25 at L1/L2/L3.
- Regression `godot --headless --path . res://tests/enemy/test_enemy_armor_damage.tscn`
  — exit 0; 18 ok, 0 failed.
- Regression `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/enemy_armor_ballista.json` — exit 0; status=pass.
- Full-suite leg `godot --headless --path . res://tests/tower/test_tower_armor_damage.tscn`
  — **exit 1** (with and without feature changes stashed): 15 ok, 3 failed.
  Cause: `[BalistaTower] WARNING: Cannot fire - no bolt found in model` —
  tower GLBs fail to import in this headless worker, so no bolt spawns. Not
  caused by this feature's changed files.

## Acceptance criteria

| # | Criterion | Status | Evidence |
|---|-----------|--------|----------|
| 1 | Perk defined, 3 levels 0.1/0.2/0.35, chest-eligible | pass (moved Pending by gate) | global.json diff + harness steps 2–5 (is_eligible true, draw contains) |
| 2 | Final post-perk damage as conversion base | pass (Pending by gate) | harness asserts armor drain on base 5 (= 10 × ARMOR_DAMAGE_REDUCTION), not 10 |
| 3 | Shared take_damage path; Porter 0 dmg → 0 sunder | pass (Pending by gate) | EnemyHealthController.gd hook after modifiers, `final_hit_damage <= 0.0` guard |
| 4 | Ballista flat 20 stacks additively | pass (Pending by gate) | armor 40→39.5/39.0/38.25 = 20 flat + 0.5/1.0/1.75 perk |
| 5 | Existing armor bar reflects drain, no new VFX | pass (Pending by gate) | `[ENEMYHEALTHBAR] show ... hp=1620/1625` updates during sunder legs; no new VFX files in diff |
| 6 | Editor gate + focused harness per level + no-perk baseline | partial | editor exit 0; harness pass; but full-suite tower leg fails (pre-existing) |

## Quality findings

Changed code reviewed against /opt/data/coding_rules.md: small typed helpers,
fast-escape guards, reuses existing `_consume_armor`/config-query pattern,
no duplication of existing perks' logic beyond required symmetry, debug log
gated behind `OS.is_debug_build()`. No violations found in changed files.

Manual testing (`manual_testing: required`, armor-bar drain screenshots to
`.gen/screenshots/`) not yet performed — pending for manual-tester profile.

## Blockers

None infra. Full suite red only due to pre-existing baseline failure
(worker headless GLB import state); fixable in principle by fixing the worker
image import or the BalistaTower test setup — outside this feature's scope.

## Unverified items

- Windowed manual evidence of visible armor-bar drain (screenshots).
