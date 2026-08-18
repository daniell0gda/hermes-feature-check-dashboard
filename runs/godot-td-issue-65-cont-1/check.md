# Check Report: underground enemies ignore ground towers
Task ID: check
Classification: fixable

## Verdict
fixable — godot unavailable in this check env (exit 127, command-not-found); focused harness fails on scenario setup (Floodgate path_blocked) per coder report; scenario file now present but test not green. Smoke and build passed in coder env. No items Done while gate failed.

## Commands Run (verbatim from plan)
- godot --headless --path . --editor --quit-after 300
  exit_code: 127 (command not found)
- godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_ground_tower_exclusion.json
  exit_code: 127 (command not found); prior coder run: exit 1, harness fail, path_blocked on Floodgate
- godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json
  exit_code: 127 (command not found); prior: exit 0, smoke passed

## Acceptance Criteria Evidence & Status
All criteria from plan.md in Pending or Impossible due to failed verification gate (no build/test execution possible here) and focused scenario not completing green. No items remain Done.
- After a porter port into the underground section completes, the ported enemy reports as underground. — Pending (unverified here; code present per report)
- Ground-level combat towers do not acquire or attack an enemy that reports as underground. — Pending
- Ground-level projectiles already in flight do not damage a target after that target reports as underground. — Pending
- When an enemy is thrown or launched from the underground exit, that enemy reports as not underground. — Pending
- After that launch, a ground-level combat tower can acquire and damage the same enemy again. — Pending
- Underground-placed attackers still damage enemies that report as underground. — Pending
- Surface enemies that were never ported remain acquirable and damageable by ground-level combat towers. — Pending
- Debug-build [UNDERGROUND] log line per underground-flag set on port — Pending
- Debug-build [UNDERGROUND] log line per underground-flag clear on exit launch — Pending
- A focused harness scenario at tests/scenarios/underground_ground_tower_exclusion.json proves the underground-flag set after porter port, ground-tower targeting exclusion including in-flight projectiles, flag clear after exit launch, post-launch reacquisition, underground-attacker damage, and never-ported surface targetability. — Impossible (concrete technical blocker: godot unavailable + harness path_blocked on setup; scenario file now exists)

## Changed-file Quality Findings (from coder diff + worktree inspection)
No quality violations recorded per role (only feature diff). Changes in Enemy.gd, EnemyMovementController.gd, Projectile.gd, GenericTowerProjectile.gd, BalistaProjectile.gd, IceTower.gd, HarnessValues.gd, new scenario json. CLAUDE.md rules followed for debug logs [UNDERGROUND], typed? (GDScript), no deep nesting observed in flag logic. No applicable coding_rules.md (TS) violations. No quality: suffix needed.

## Blockers
- godot executable not present in check environment PATH (exit 127)
- Focused test harness fails on Floodgate placement (path_blocked) — scenario setup issue, not code logic
- Cannot re-run focused test or typecheck gate in this env

## Unverified Items
- All 10 acceptance criteria (no passing automated test evidence for underground exclusion in this run; smoke only)
- Coder-reported implementation of is_underground flag, projectile guards, and debug logs (code present but unexercised here)

## Coder Report Summary
Coder: focused scenario exists but setup blocker (Floodgate path_blocked); smoke passed, build passed (exit 0), git diff clean. Added [UNDERGROUND] logs, is_underground harness value, underground guards in damage paths. No dashboard events.

Quality notes: no changes (no cross-cutting issues in feature diff; scenario setup is the gap, fixable by adjusting placement or map in scenario).