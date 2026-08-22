# Check report: issue-39-buried-ordnance

classification: fixable
date: 2026-08-22
iteration: revision-check-1
checker: check worker (fresh verification via run_project_cmd)

## Commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-buried-ordnance)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable |
| Typecheck/build | `godot --headless --path . --editor --quit-after 300` | 0 | import/parse clean |
| Focused test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_buried_ordnance_progression.json` | **1** | **fail** — enemy-spawn precondition unmet (action 21) |
| Full test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/display_damage_surface_parity.json` | 0 | `[Harness] status=pass exit=0`, fresh result written |

## Fresh evidence

- `.gen/harness/display_damage_surface_parity/result.json` — status pass, exit 0.
- `.gen/harness/traps_buried_ordnance_progression/result.json` — status fail (exit 1).
- `.gen/harness/_logs/traps_buried_ordnance_progression.out.log`

## Revision progress vs prior check

The prior blocker is FIXED: the unowned-config contract now agrees
(`get_buried_ordnance_config()` returns `{"enabled": false}` and the scenario asserts field
`enabled == false`). Arm 1 (progression) fully passes fresh: eligibility true on reset,
grant to level 1 via apply_progression, config enabled/chance 0.5/radius 2.0 while owned,
re-grant idempotent, save_now + _load_state_and_apply replay keeps level 1 and radius 2.0.
The `[TrapProgression] traps_buried_ordnance -> chance ... radius ...` debug line pattern is
in place (log-expectation itself still unmet because the run aborts before its grep window).

## Current failure

Action index 21: `wait_for_condition enemies.underground >= 2` timed out with actual 1 —
`cave_fixture(id=101, outcome=enemies)` + `force_spawn_cave_enemies` produced only ONE
underground enemy on map_1 (Cactoro pool), so no second neighbor exists for the chain blast.
Everything downstream never ran: forced roll, `[BURIED_ORDNANCE]` log line, screenshot,
no-chain arm, final snapshot. Expectations 1–2 failed only because their log content was
never produced.

Required fix (scenario side): make the fixture deterministically spawn >= 2 underground
enemies (e.g. two fixtures, a pool/outcome that guarantees multiples, or repeat
force_spawn_cave_enemies until count met within timeout) — then rerun. Implementation code
need not change for this step.

## Acceptance criteria evidence

1. Perk defined as Unique, eligible/grantable/idempotent — VERIFIED by fresh arm-1 actions
   1–17 (all ok), but kept PENDING until the focused scenario passes end-to-end.
2. Chance/radius chain damage on underground hit — NOT EXERCISED (chain never triggered:
   only 1 underground enemy). Code present (`Trap.gd::_apply_chain_blast`). PENDING.
3. No chain without perk — NOT EXERCISED (no-chain arm aborted). PENDING.
4. Non-underground immunity — structural only (iterates get_all_underground_enemies +
   re-checks is_enemy_underground); no runtime assertion ran. PENDING.
5. Visible small-explosion VFX — code calls ExplosionFX.spawn_bazooka_explosion(0.6);
   screenshot action never ran; manual_testing windowed screenshots absent. PENDING.
6. Determinism under fixed seed — set_buried_ordnance_next_roll override implemented;
   never exercised. PENDING.
7. Debug `[BURIED_ORDNANCE]` log line, absent in release — implemented behind
   OS.is_debug_build(); log expectation never reached. PENDING.
8. Focused harness passes headless with fresh evidence — FAILED (see above). PENDING.
9. Windowed screenshot checkpoint of chained explosion — NOT PRODUCED. PENDING.

## Quality findings (changed/new files)

- autoload/ProgressionManager.gd, scripts/game/actors/Trap.gd,
  scripts/progression/managers/TrapProgressionManager.gd,
  scripts/progression/trap.json, tests/scenarios/traps_buried_ordnance_progression.json.
- No blocking violations; structure, debug-tag logging per CLAUDE.md, and surgical scope are
  fine.
- Advisory (CLAUDE.md "typed variables everywhere"): several NEW untyped locals in changed
  code — `cfg_any` (ProgressionManager.gd, Trap.gd x2), `ug` (Trap.gd), loop var `e`
  (_apply_chain_blast). They mirror adjacent legacy style but are new lines; prefer explicit
  types or casts-free Dictionary/Node typing next pass. Advisory only; does not demote
  criteria (they are already Pending on test failure).
- Test overlap: no existing scenario covered buried ordnance; new scenario file is novel.
  quality-notes.md: appended advisory entry.

## Blockers

None infra. Runner healthy; classification fixable (scenario spawn-count determinism).
