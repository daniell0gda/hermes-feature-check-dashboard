# Team-leader report

- **Result:** failed
- **Classification:** fixable
- **Feature:** perk-undermining
- **Run:** issue-91-perk-undermining-211435
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- (none)

## ⬜ Pending
- A progression named `undermining` exists in the trap progression pool with type Common, exactly 3 levels, and is offered only for traps (never for any surface tower).
- With `undermining` at levels 1/2/3, the exposed trap armor-damage bonus is 8/15/25 respectively; when the perk is not owned it is 0.
- Each hit from any of the four traps (`trap_01`, `trap_02`, `trap_03`, `trap_05`) on an armored enemy removes exactly the current `undermining` level's armor amount (8/15/25), clamped so armor never goes below zero, in addition to normal HP damage.
- Without the perk owned, a trap hit changes enemy armor by exactly zero (existing behaviour preserved).
- Surface tower hits are unchanged by `undermining`: owning it does not add armor damage to any non-trap tower's hits.
- Debug-build `[Undermining]` log line per armor-stripping trap hit
- When an owned-perk trap hit actually strips armor, the trap's existing hit-impact effect shows a distinct tint signalling the armor-strip portion, and the tint is absent on trap hits while the perk is unowned.
- A focused harness scenario proves definition and persistence: `undermining` resolves as Common/traps-only with 3 levels, applying levels yields the 8/15/25 armor values through the same accessor Ballista uses, re-applying past level 3 stays at 25, and save/reload restores the level and value.
- A focused harness scenario proves live trap behavior: a real trap hit on an armored underground enemy reduces armor by exactly the perk amount at each level, and by zero when unowned.
- A focused harness scenario proves scope isolation: while `undermining` is owned, scripted surface-tower hits apply no perk-derived armor damage.

## ❌ Impossible
- (none)

## Check

# Check Report — issue-perk-undermining (iteration 1)

classification: fixable

## Verdict

The feature is not implemented. The git-workspace is clean at origin/master
(d241462): zero commits, zero modified files, and no `undermining` references
anywhere in the repo (grep across *.gd / *.json excluding .gen/.godot returns
nothing). No cluster report content was found beyond the plan-derived files.
All 10 acceptance criteria are unmet.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-perk-undermining)

- Preflight: `["git","status","--short"]` → exit 0, empty output (clean tree at d241462).
- Typecheck/build gate: `["godot","--headless","--editor","--path",".","--quit-after","120"]`
  → exitCode 0, durationMs ~97s. Import noise only (pre-existing glb import
  errors on master: tower GLBs, portal/earth/shed glb, icon pngs); no GDScript
  parse errors from project scripts. Gate technically passes but proves nothing
  about this issue since no feature code exists.
- Focused test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/undermining_progression.json"]`
  → FAILED, exitCode 1 (scenario file does not exist). No result.json written.
- Baseline control (proves runner/harness healthy):
  `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/enemy_armor_trap.json"]`
  → exitCode 0, `[Harness] status=pass exit=0`, wrote
  `.gen/harness/enemy_armor_trap/result.json`. So the failure of the
  undermining harnesses is a missing-feature problem, not infra.
- Full suite: not run to completion — it would abort on the first missing
  `undermining_*` scenario file; outcome already established by the focused run.

## Missing deliverables

- `scripts/progression/trap.json` has no `undermining` entry (cluster 1).
- `Trap.gd` / `EnemyHealthController.gd` have no armor-strip logic or
  `[Undermining]` debug log (cluster 2).
- No armor-strip tint in Trap.gd / EffectsManager.gd (cluster 3).
- Missing scenario files: `tests/scenarios/undermining_progression.json`,
  `tests/scenarios/undermining_trap_armor.json`,
  `tests/scenarios/undermining_scope_isolation.json` (cluster 4).
- No `.gen/status.md`, no quality-notes.md, no coder reports with evidence.

## Blockers

None. Runner reachable (`git status` probe OK), worker image fine, Godot runs.
This is plain incomplete implementation → fixable.

## Unverified items

All 10 criteria (no implementation exists to verify).
