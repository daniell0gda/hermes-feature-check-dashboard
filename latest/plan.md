# Issue #32 continuation plan — verify and clean up fractional-damage retirement

## Starting state
- Workspace: `/workspace/git-workspaces/godot-td/issue-32`; branch `issue/32`; HEAD `30ea815374ce6b5489abfa179bf7f0cc7918956c` (`merge: issue 61 residual flush harness evidence`), rebased onto `origin/master`.
- Rules read: `/opt/data/coding_rules.md`, `CLAUDE.md`.
- Hermes-side Git inspection: staged diff is six files: four production Enemy/retirement files, `scripts/testing/HarnessActions.gd`, and the rename/update `issue_61_residual_flush.json` → `issue_32_fractional_damage_retirement.json`; the scenario also has an unstaged working-tree edit. `git diff --check` and `git diff --cached --check` pass.
- No production code is changed by this planning phase. Dashboard run `issue-32-retry-20260813` is existing/published and must be preserved.

## Goal and non-negotiable API boundary
Complete focused evidence and cleanup without changing the already-resolved #61 harness seam. Production calls the no-argument `EnemyHealthController.flush_pending_damage()` (and `Enemy.flush_pending_damage()` forwarding path) for actual death, surface retirement, and suction retirement. HarnessActions must continue to invoke only the harness-only `EnemyHealthController.harness_flush_pending_damage(tower_type_id, instance_id)` via the resolved #61 actions; do not rename, merge, or replace these methods.

`Enemy.hp` remains `int`. Ordinary gameplay damage remains floor/applied whole-point damage. Retirement flush uses `int(round(...))`, clears pending state even when rounded zero, records valid stored type/instance attribution once, and does not double-kill or duplicate egg/cave/tube events. The harness seam may expose/apply its deterministic test residual behavior, but it must not become the production caller.

## Current evidence and remaining scope
The renamed scenario now uses `inject_residual`, `flush_residual`, `tube_checkpoint`, `suction_enemy`, `surface_endpoint`, and `stats_checkpoint`. Fresh action evidence proves `.4 → applied 0, HP 40→40`, repeat flush no-op; `.6 → applied 1, HP 40→39`, repeat flush no-op; death kill; tube captured/exited `1/1`; and surface egg decrement. Its final aggregate expectations are stale against normal gameplay: current result observed fire/instance damage `17`, not `1`, because ordinary gameplay damage is also present. Tune expectations to assert the seam action details/deltas and non-regression bounds, not an impossible aggregate total of exactly one.

Remaining acceptance must be explicit from a fresh result on this revision:
1. `.4` action has `pending_before=0.4`, `applied=0`, `pending_after=0`, unchanged HP; its repeated flush has `pending_before=0`, `applied=0`.
2. `.6` action has `pending_before=0.6`, `applied=1`, `pending_after=0`, HP `40→39`; its repeated flush is zero/no-op. Stats attribution is fire/instance `6101` and the residual contributes exactly one event/point, while aggregate gameplay damage may exceed one.
3. Death action retires one enemy and produces exactly one kill; no second residual event/kill or HP re-entry is inferred from aggregate totals. Surface endpoint reduces egg HP once and retires the enemy. Suction/tube checkpoints show captured `1`, exited `1` and no duplicate exit after the wait.
4. The scenario passes with map `map_4`, normal time scale `1.0`, and all required actions `ok`; optional probes must not be mistaken for acceptance.

## Ordered clusters
1. **Focused cleanup/evidence** depends on the resolved seam already present; owns only the renamed focused scenario and its evidence note. Tune aggregate assertions to observed normal gameplay and preserve all #61 actions.
2. **Verification** depends on a clean focused result; runs native import plus focused and unchanged preservation scenarios, checks fresh artifact identity, and records exact acceptance details. No source edits.
3. **Final review** depends on verification; performs Hermes-side diff/status checks and stale-artifact classification. No production or harness edits.

## Exact native `run_project_cmd` payloads
Use `project=godot-td`, `workspace=godot-td/issue-32`, tokenized `cmd`; no shell wrapper, Docker, or absolute workspace in runner payloads:

```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--version"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_32_fractional_damage_retirement.json"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/ice_focus_cone_cadence.json"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_tower_roster.json"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/projectiles_10x_beam_cone.json"]}
```
The explicit `scenes/Main.tscn` form is required for gameplay evidence: default `--harness` startup through `LoadingScreen.tscn` previously timed out before actions. Do not call `release_project_worker` in this phase.

## Stale evidence rule
Do not treat prior `.gen/harness/*/result.json`, `.gen/check.md`, `.gen/status.md`, `.gen/revisions.md`, `.gen/report.md`, or old coder reports as fresh merely because they exist. In particular, prior reports describe pre-#61 schema limitations and revision `ad9385f`; the current focused result has normal gameplay damage and must be reinterpreted. Dashboard artifacts under `.gen/team-work-dashboard/` and run `issue-32-retry-20260813` are historical/published state and must be preserved, not overwritten or used as current verification. Only a result produced by the explicit Main-scene command after HEAD `30ea815`, with matching scenario id/seed and recorded action details, is fresh.

## Handoff
Write `.gen/coder-reports/retry-plan.md` with actual source/diff findings, exact acceptance, command payloads, and stale-artifact list. This phase writes only `.gen/plan.md`, `.gen/clusters/*.md`, and that report; production source is untouched.
