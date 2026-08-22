# Check report: issue-39-buried-ordnance

classification: fixable
date: 2026-08-22
iteration: revision-check-2
checker: check worker (fresh verification via run_project_cmd)

## Commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-buried-ordnance)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable |
| Typecheck/build | `godot --headless --path . --editor --quit-after 300` | 0 | import/parse clean |
| Focused test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_buried_ordnance_progression.json` | **1** | **fail** — action 29 log-expectation timeout (see below) |
| Full test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/display_damage_surface_parity.json` | 0 | `[Harness] status=pass exit=0` |

## Fresh evidence

- `.gen/harness/display_damage_surface_parity/result.json` — status pass, exit 0.
- `.gen/harness/traps_buried_ordnance_progression/result.json` — status timeout, exit 1.
- `.gen/harness/_logs/traps_buried_ordnance_progression.out.log`

## Revision progress vs prior check (revision-check-1)

The prior blocker (only 1 underground enemy spawned) is FIXED: two `cave_fixture`s
(id 101, 102) now deterministically produce >= 2 underground enemies; action 23
(`enemies.underground >= 2`) passes. Arm 1 (progression: eligibility, grant, config
chance 0.5 / radius 2.0, idempotent re-grant, save/replay) still passes fully —
actions 0–17 ok. Trap placement, forced roll override, and the 1.5 s wait all pass
(actions 24–28 ok).

## Current failure

Action index 29: log regex
`\[BURIED_ORDNANCE\] trap=trap_01 chain=1 rolled=true caught=[1-9]` timed out.
The harness log contains NO `[BURIED_ORDNANCE]` line at all — the trap never hit
any enemy. Root cause is scenario-side: `cave_fixture` enemies spawn statically at
the fixture positions and never move, and the trap's step-on detection requires an
enemy within `trigger_radius = 0.3` world units of the trap at (0.25, -3.0, 0.25).
The nearest spawned enemy is ~0.05–0.35 units away horizontally but the overlap poll
(`_check_overlap_and_damage`) uses the same 0.3 trigger_radius; with the fixtures at
(0, -3, 0) and (0.3, -3, 0.1), distances are ~0.26 and ~0.14 — borderline, and the
run shows no hit fired within the 10 s window. Everything downstream (chain blast,
`[BURIED_ORDNANCE]` log, screenshot, no-chain arm, final snapshot) never executed.

Required fix (scenario side): place the trap exactly on an enemy position or widen
the trigger window — e.g. put `place_tower` at the exact fixture position
(0.0, -3.0, 0.0), or add a harness action that teleports/forces an underground enemy
into the trap's trigger radius, then rerun. Implementation code need not change.

## Acceptance criteria evidence

1. Perk defined as Unique, eligible/grantable/idempotent — VERIFIED by fresh arm-1
   actions 1–17 (all ok), but kept PENDING until the focused scenario passes
   end-to-end.
2. Chance/radius chain damage on underground hit — code present
   (`Trap.gd::_apply_chain_blast` iterates `get_all_underground_enemies`, distance
   filter, `take_damage`), but NOT EXERCISED (chain never triggered). PENDING.
3. No chain without perk — `roll_buried_trigger` returns false when config disabled;
   no-chain arm (trap_03) never ran. PENDING.
4. Non-underground immunity — structural only (underground-only source list plus
   per-enemy `is_enemy_underground` re-check); no runtime assertion ran. PENDING.
5. Visible small-explosion VFX — code calls
   `ExplosionFX.spawn_bazooka_explosion(..., 0.6)` per caught enemy; screenshot
   action never ran; manual_testing windowed screenshots absent. PENDING.
6. Determinism under fixed seed — `set_buried_ordnance_next_roll` override
   implemented; the forced roll was consumed by no roll site (no hit). PENDING.
7. Debug `[BURIED_ORDNANCE]` log line, absent in release — implemented behind
   `OS.is_debug_build()`; log-expectation never met because no chain event fired.
   PENDING.
8. Focused harness scenario passes with fresh evidence — FAILED (timeout, action 29).
   PENDING.
9. Windowed screenshot at the chained-explosion moment — never captured. PENDING.

## Quality notes

- Open entry `typed-vars-new-code` (revision-check-1) re-checked: new code in
  `Trap.gd` / `ProgressionManager.gd` still has untyped locals (`cfg_any`, `ug`,
  loop var `e`). Still open; advisory, does not demote criteria.
- No new cross-cutting violations found in the feature diff
  (`autoload/ProgressionManager.gd`, `scripts/game/actors/Trap.gd`,
  `scripts/progression/managers/TrapProgressionManager.gd`,
  `scripts/progression/trap.json`, `tests/scenarios/traps_buried_ordnance_progression.json`).
  Implementation follows existing trap/perk patterns; debug logging follows the
  CLAUDE.md `[TAG]` + `OS.is_debug_build()` convention.

## Blockers

None infrastructural. Single remaining gap is the scenario's trap-trigger setup
(static cave enemies never step on the trap), fixable in
`tests/scenarios/traps_buried_ordnance_progression.json` or via a harness
enemy-position action, then rerun the focused scenario.

## Unverified items

- All 9 criteria remain Pending per the gates above.
- `manual_testing: required` — windowed screenshots of the chained-explosion moment
  have not been produced (manual-tester profile owns that report).
