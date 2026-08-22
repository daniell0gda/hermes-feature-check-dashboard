# Check report: issue-39-buried-ordnance

classification: fixable
date: 2026-08-22
checker: check worker (fresh verification via run_project_cmd)

## Commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-buried-ordnance)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable |
| Typecheck/build | `godot --headless --path . --editor --quit-after 300` | 0 | import/parse clean |
| Focused test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_buried_ordnance_progression.json` | **1** | **fail** — action 4 unmet |
| Full test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/display_damage_surface_parity.json` | 0 | `[Harness] status=pass exit=0`, fresh result written |

Fresh evidence:
- `.gen/harness/display_damage_surface_parity/result.json` — status pass.
- `.gen/harness/traps_buried_ordnance_progression/result.json` — status error, elapsed 3.385s,
  finished 2026-08-22T17:02:00. Actions: load_map ok; reset_for_new_game ok;
  level==0 ok; is_eligible==true ok; then FAIL:
  `unmet_condition: progression_call.get_buried_ordnance_config == <null>` with
  `actual: {"enabled": false}`.

## Root cause of focused failure

Scenario arm 1 expects `ProgressionManager.get_buried_ordnance_config()` to return null while
the perk is unowned, but `autoload/ProgressionManager.gd` (new code) returns
`{"enabled": false}` when unowned. Scenario and implementation disagree on the unowned-state
contract. Fix one side (prefer updating the scenario expectation to `{"enabled": false}` or
field-check `enabled == false`) and rerun; downstream actions (grant, forced-roll chain,
`[BURIED_ORDNANCE]` log regex, screenshot, no-chain arm) have never executed, so nothing past
eligibility is verified.

## Acceptance criteria evidence

1. Perk defined as Unique + eligible/grantable/idempotent — PARTIAL: data present in
   `scripts/progression/trap.json`; eligibility asserted ok in fresh run; grant/replay steps
   never ran (scenario aborted). PENDING.
2. Chance/radius chain damage on underground hit — NO EVIDENCE (never reached). Code exists in
   `Trap.gd::_apply_chain_blast`. PENDING.
3. No chain without perk — NOT VERIFIED (no-chain arm never ran). PENDING.
4. Non-underground enemies immune — structural only (code iterates underground enemies);
   no runtime assertion ran. PENDING.
5. Visible small-explosion VFX per chained blast — code calls
   `ExplosionFX.spawn_bazooka_explosion`; screenshot action never ran; windowed manual-testing
   screenshots absent (`manual_testing: required`). PENDING.
6. Deterministic under fixed seed — forced-roll override implemented
   (`set_buried_ordnance_next_roll`); not exercised. PENDING.
7. Debug `[BURIED_ORDNANCE]` log line per event, absent in release — implemented behind
   `OS.is_debug_build()`; log-expectation action never reached. PENDING.
8. Focused harness passes with fresh evidence — FAILED (see above). PENDING.
9. Windowed screenshot checkpoint at chained-explosion moment — NOT PRODUCED. PENDING.

## Quality findings (changed files)

- No blocking violations in the diff. New GDScript follows repo conventions (typed where the
  surrounding file types, debug-tag logging per CLAUDE.md, small functions). Minor advisory:
  a few untyped locals (`cfg_any`, `ug`, loop var `e`) mirror existing patterns in the same
  files — acceptable, noted only.
- Test-overlap: no pre-existing scenario covered buried ordnance; new scenario file is novel.

## Blockers

None infra. Runner healthy throughout; classification is fixable (implementation/scenario
contract mismatch on the unowned config return value).
