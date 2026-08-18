# Check Report: underground enemies ignore ground towers
Task ID: check
Classification: blocked

## Verdict
blocked — verification commands cannot execute (godot binary absent from PATH, exit 127 on all godot invocations); focused harness scenario file missing from repository (underground_ground_tower_exclusion.json not present, confirmed by ls and prior runner error HTTP 422 / "scenario file not found").

## Commands Run (verbatim from plan)
- godot --headless --path . --editor --quit-after 300
  exit_code: 127 (command not found)
- godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_ground_tower_exclusion.json
  exit_code: 127 (command not found); prior evidence from .gen/harness/.../result.json shows "scenario file not found"
- godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json
  exit_code: 127 (command not found); prior evidence shows smoke harness passed (status ok)

## Acceptance Criteria Evidence & Status
All criteria from plan.md moved to Pending or Impossible due to failed gate (build/test cannot run) and missing scenario file. No items remain Done.
- After a porter port into the underground section completes, the ported enemy reports as underground. — Pending (unverified; no harness execution possible)
- Ground-level combat towers do not acquire or attack an enemy that reports as underground. — Pending
- Ground-level projectiles already in flight do not damage a target after that target reports as underground. — Pending
- When an enemy is thrown or launched from the underground exit, that enemy reports as not underground. — Pending
- After that launch, a ground-level combat tower can acquire and damage the same enemy again. — Pending
- Underground-placed attackers still damage enemies that report as underground. — Pending
- Surface enemies that were never ported remain acquirable and damageable by ground-level combat towers. — Pending
- Debug-build [UNDERGROUND] log line per underground-flag set on port — Pending
- Debug-build [UNDERGROUND] log line per underground-flag clear on exit launch — Pending
- A focused harness scenario proves the underground-flag set, ground-tower targeting exclusion including in-flight projectiles, and flag clear after exit launch. — Impossible (concrete blocker: scenario file absent; see result.json error)

## Changed-file Quality Findings (from coder diff + worktree inspection)
No quality violations recorded in shared files (per role rules, only report on feature diff). Coder changes touch Enemy.gd, EnemyMovementController.gd, Projectile.gd, tower/projectile scripts, HarnessValues.gd. No applicable coding_rules.md overrides triggered in check (Godot/GDScript project; global rules are TS-focused). CLAUDE.md rules (e.g. typed vars, debug logs, indent) appear followed in the underground flag additions.

## Blockers
- godot executable not present in check environment PATH
- Required test scenario res://tests/scenarios/underground_ground_tower_exclusion.json does not exist in worktree (ls confirms; only smoke and other scenarios present)
- Cannot re-run focused test or typecheck gate

## Unverified Items
- All 10 acceptance criteria (no passing automated test evidence for the underground exclusion behavior beyond smoke)
- Coder-reported implementation of is_underground flag, projectile guards, and debug logs (code present but unexercised by harness)

## Coder Report Summary
Coder noted: "focused scenario file is absent from the checkout." Smoke passed, build gate passed in their env. No dashboard events. Implementation adds [UNDERGROUND] logs and guards.

Quality notes: none appended (no cross-cutting issues found in feature diff; missing scenario is the sole gap).