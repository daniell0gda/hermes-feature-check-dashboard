# Team-leader report

- **Result:** completed
- **Classification:** unknown
- **Feature:** porter-one-way-ticket
- **Run:** issue-21-porter-one-way-ticket
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- `porter_one_way_ticket` is a Unique Porter perk that starts at level 0 and eligible, appears in a full chest draw while eligible, becomes level 1 after it is applied, and is absent from a full chest draw after that single level is taken.
- `porter_one_way_ticket` and `porter_wide_gate` keep independent state: taking either one does not own or change the other's level or effect.
- An owned `porter_one_way_ticket` remains owned after progression save and reload, and `reset_for_new_game` returns it to unowned.
- The existing `porter_wide_gate` progression contract still passes after `porter_one_way_ticket` is added.
- When `porter_one_way_ticket` is unowned, a successful Porter teleport moves a non-boss enemy underground without applying Stun or Wet.
- When `porter_one_way_ticket` is owned, a successful Porter teleport leaves a non-boss enemy underground with Stun remaining time greater than 0.8s and at most 1.0s and Wet remaining time greater than 2.8s and at most 3.0s immediately after underground arrival.
- After that owned teleport, Stun is gone by 1.1s while Wet remains, and Wet is gone by 3.1s.
- Owning `porter_one_way_ticket` does not apply Stun or Wet to an enemy that is underground without a Porter teleport.
- While Stun and Wet remaining times are both positive, the existing enemy health-bar status row shows the Stun icon and the Wet icon.
- Debug-build [PORTER] log line per one-way-ticket apply

## ⬜ Pending

## ❌ Impossible

## Check

# Check Report: issue-porter-one-way-ticket

## Verdict
pass

## Classification
pass

## Verification Commands (all via run_project_cmd project=godot-td workspace=poke-defense-godot/issue-porter-one-way-ticket)
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"] → exitCode=0
- Progression test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_one_way_ticket_progression.json"] → exitCode=0, [Harness] status=pass
- Wide gate test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_wide_gate_progression.json"] → exitCode=0, [Harness] status=pass
- One-way ticket test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_one_way_ticket.json"] → exitCode=0, [Harness] status=pass
- UI test: ["godot","--headless","--path",".","res://tests/ui/test_enemy_health_bar_stun_wet_icons.tscn"] → exitCode=0, 7 ok, 0 failed

## Acceptance Criteria Evidence
All 10 criteria verified Done via coder reports + fresh runs. Each has dedicated passing automated test harness or unit test that would fail if broken. No quality violations in changed files.

## Coder Reports Inspected
- 1-one-way-ticket-perk.md: 4 criteria Done, tests pass
- 2-one-way-ticket-teleport.md: 6 criteria Done, tests pass
- implementation.md: all 10 criteria Done, all commands exit 0

## Changed Files Quality
No rule violations found in /opt/data/coding_rules.md or worktree rules. All new code covered by tests that assert the criteria.

## Blockers
none

## Unverified Items
none

## Runner/Infra Notes
No runner failures. All gates passed via run_project_cmd.
