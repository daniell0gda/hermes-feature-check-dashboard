# Check report: issue-39-buried-ordnance

classification: fixable
date: 2026-08-22
iteration: revision-check-3
checker: check worker (fresh verification via run_project_cmd)

## Commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-buried-ordnance)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| Typecheck/build | `godot --headless --path . --editor --quit-after 300` | 0 | import/parse clean |
| Focused test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_buried_ordnance_progression.json` | **1** | **fail** — timeout at action 29 (see below) |
| Full test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/display_damage_surface_parity.json` | 0 | `[Harness] status=pass exit=0` |

## Fresh evidence

- `.gen/harness/display_damage_surface_parity/result.json` — status pass.
- `.gen/harness/traps_buried_ordnance_progression/result.json` — status timeout; last passing action index 29's predecessor chain: actions 0–28 ok, action 29 unmet (`log regex \[BURIED_ORDNANCE\] trap=trap_01 chain=1 rolled=true caught=[1-9]`).
- `.gen/harness/_logs/traps_buried_ordnance_progression.out.log` — contains zero `[BURIED_ORDNANCE]` lines.

## Failure analysis (unchanged from revision-check-2)

The scenario-side blocker is NOT fixed. `cave_fixture` enemies spawn statically at
fixture positions (0,-3,0)/(0.3,-3,0.1) while the trap_01 sits at (0.25,-3,0.25)
with trigger_radius 0.3. The trap's step-on overlap never registered a hit within
the 10 s window, so the forced roll was consumed by nothing: no chain blast, no
`[BURIED_ORDNANCE]` debug line, no screenshot checkpoint, and the no-chain arm
(actions 31–46) never executed because the timeline aborted at 29.

Required fix (scenario side, per prior check): place the trap exactly on an enemy
position or force/teleport an underground enemy into the trigger radius, then rerun.
Implementation code need not change for this step.

## Acceptance criteria evidence

All 9 criteria remain Pending:

1. Perk defined as Unique, eligible/grantable/idempotent — arm-1 actions 0–17 pass,
   but kept Pending until the focused scenario passes end-to-end.
2. Chance/radius chain damage on underground hit — code present in
   Trap.gd `_apply_chain_blast`; never exercised (no hit fired). PENDING.
3. No chain without perk — no-chain arm (trap_03) never ran. PENDING.
4. Non-underground immunity — structural only; no runtime assertion ran. PENDING.
5. Visible small-explosion VFX — code calls ExplosionFX.spawn_bazooka_explosion;
   screenshot action never ran; manual_testing windowed screenshots absent. PENDING.
6. Determinism under fixed seed — `set_buried_ordnance_next_roll` override exists;
   roll site never consumed. PENDING.
7. Debug `[BURIED_ORDNANCE]` log line — implemented behind OS.is_debug_build();
   expectation never met. PENDING.
8. Focused harness scenario passes with fresh evidence — FAILED (timeout, action 29).
   PENDING.
9. Windowed screenshot at chained-explosion moment — never captured. PENDING.

## Quality notes

Open entry `typed-vars-new-code` (revision-check-1): untyped locals
(`cfg_any`, `ug`, loop var `e`) in autoload/ProgressionManager.gd and
scripts/game/actors/Trap.gd remain — advisory, mirrors legacy style. No change this
iteration; entry stays open. No new cross-cutting issues found in the diff
(4 modified files + 1 new scenario JSON, matching declared cluster scope).

## Verdict

fixable — scenario fixture geometry must be corrected so the trap actually hits an
underground enemy; then rerun focused harness + windowed screenshots before next check.
