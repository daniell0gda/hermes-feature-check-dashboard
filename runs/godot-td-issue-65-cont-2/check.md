# Check Report: underground enemies ignore ground towers
Task ID: revision-check-2
Classification: fixable

## Verdict
fixable — godot unavailable in this check env (exit 127, command-not-found); focused harness had setup/evidence gaps per coder report (underground count 0 despite pass); scenario file present but full criteria not proven green in verification. Smoke and build passed in coder env via runner. No items Done while gate failed. Rerun through the runner required.

## Commands Run (verbatim from plan)
- godot --headless --path . --editor --quit-after 300
  exit_code: 127 (command not found)
- godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/underground_ground_tower_exclusion.json
  exit_code: 127 (command not found); prior coder run: exit 0, status=pass but underground=0 at expectations
- godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json
  exit_code: 127 (command not found); prior: exit 0, smoke passed

## Acceptance Criteria Evidence & Status
All criteria from plan.md in Pending or Impossible due to failed verification gate (no build/test execution possible here) and focused scenario evidence incomplete (no isolation of all transitions). No items remain Done.
- After a porter port into the underground section completes, the ported enemy reports as underground. — Pending (unverified here; code present per report)
- Ground-level combat towers do not acquire or attack an enemy that reports as underground. — Pending
- Ground-level projectiles already in flight do not damage a target after that target reports as underground. — Pending
- When an enemy is thrown or launched from the underground exit, that enemy reports as not underground. — Pending
- After that launch, a ground-level combat tower can acquire and damage the same enemy again. — Pending
- Underground-placed attackers still damage enemies that report as underground. — Pending
- Surface enemies that were never ported remain acquirable and damageable by ground-level combat towers. — Pending
- Debug-build [UNDERGROUND] log line per underground-flag set on port — Pending
- Debug-build [UNDERGROUND] log line per underground-flag clear on exit launch — Pending
- A focused harness scenario at tests/scenarios/underground_ground_tower_exclusion.json proves the underground-flag set after porter port, ground-tower targeting exclusion including in-flight projectiles, flag clear after exit launch, post-launch reacquisition, underground-attacker damage, and never-ported surface targetability. — Impossible (concrete technical blocker: godot unavailable + incomplete scenario evidence for all listed behaviors)

## Changed-file Quality Findings (from coder diff + worktree inspection)
No quality violations recorded per role (only feature diff). Changes in Enemy.gd, EnemyMovementController.gd, Projectile.gd, GenericTowerProjectile.gd, BalistaProjectile.gd, IceTower.gd, HarnessValues.gd, scenario json. CLAUDE.md rules followed for debug logs [UNDERGROUND], typed vars (GDScript), no deep nesting observed in flag logic. No applicable coding_rules.md (TS) violations. No quality: suffix needed. No cross-cutting scope creep. Scenario adjustment in coder run addressed Floodgate but left some assertions unproven.

## Blockers
- godot executable not present in check environment PATH (exit 127)
- Focused test harness evidence incomplete (underground count 0); scenario determinism gap, not code logic
- Cannot re-run focused test or typecheck gate in this env

## Unverified Items
- All 10 acceptance criteria (no passing automated test evidence for full underground exclusion transitions in this run; smoke only)
- Coder-reported implementation of is_underground flag, projectile guards, and debug logs (code present but unexercised here)

## Coder Report Summary
Coder: focused scenario exists, corrected exit geometry, harness exit 0 with partial pass (floodgate damage observed, generic damage observed, but underground=0 at final); smoke passed, build passed (exit 0), git diff clean. Added [UNDERGROUND] logs, is_underground harness value, underground guards in damage paths. No dashboard events.

Quality notes: no changes (no cross-cutting issues in feature diff; scenario setup/evidence is the gap, fixable by runner re-execution or scenario tweak).
