# Check report: issue-39-buried-ordnance

classification: pass
date: 2026-08-22
iteration: revision-check-1
checker: check worker (fresh verification via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-buried-ordnance)

## Gates (all via run_project_cmd — host shell never used for project commands)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| Typecheck/build | `godot --headless --path . --editor --quit-after 300` | 0 | import/parse clean |
| Focused test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_buried_ordnance_progression.json` | 0 | `[Harness] status=pass exit=0`; all actions ok; all 4 expectations pass |
| Full test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/display_damage_surface_parity.json` | 0 | `[Harness] status=pass exit=0` |

The prior revision's "action 29 timeout" is confirmed fixed by the stale-out.log
reset (`AgentHarness._reset_stale_engine_out_log` + forced log re-slicing in
`HarnessValues._engine_log_text(true)`): this run's fresh out.log contains the
live `[BURIED_ORDNANCE] trap=trap_01 chain=1 rolled=true caught=1` line.

## Fresh evidence

- `.gen/harness/traps_buried_ordnance_progression/result.json` — status `pass`, zero failed actions, all 4 expectations pass:
  - regex `[TrapProgression] traps_buried_ordnance -> chance 0.50 radius 2.00` ✓
  - regex `[BURIED_ORDNANCE] trap=trap_01 chain=1 rolled=true caught=` ✓ (actual log line: `caught=1`)
  - !regex `trap=trap_03 chain=1` ✓ (trap_03 logs only `chain=0 rolled=false caught=0`)
  - gamestate current_wave ≥ 0 ✓
- `.gen/harness/_logs/traps_buried_ordnance_progression.out.log` — fresh this run; contains the chain event plus benign no-perk/no-roll lines.
- Windowed screenshots: `.gen/harness/traps_buried_ordnance_progression/shots/buried_ordnance_chain_blast.png` and `buried_ordnance_aftermath.png` captured this session; chain-blast image inspected — a small white burst particle effect is visible at the blast site on the underground map.

## Acceptance criteria — all 9 Done

1. Perk Unique/eligible/grantable/idempotent — `scripts/progression/trap.json` defines `traps_buried_ordnance` type "Unique", maxLevels 1; scenario arm 1 exercises reset → grant → apply → save → reload with level/chance/radius stable (actions ok).
2. Chance/radius chain damage on underground hit — `Trap.gd::_apply_chain_blast` damages every other valid underground enemy within radius of hit position; exercised live (`chain=1 ... caught=1`).
3. No chain without perk — `roll_buried_ordnance_trigger` returns false when perk unowned; trap_03 arm asserts positive `chain=0 rolled=false caught=0`.
4. Non-underground immunity — chain loop iterates only `get_all_underground_enemies` and re-checks `is_enemy_underground()` per enemy.
5. Visible small-explosion VFX — `ExplosionFX.spawn_bazooka_explosion` called per caught enemy; burst visible in inspected screenshot.
6. Determinism — harness override `set_buried_ordnance_next_roll(0.0)` forces the roll; scenario asserted reproducibly via forced roll.
7. Debug-only `[BURIED_ORDNANCE]` line — printed behind `OS.is_debug_build()` with trap id, rolled outcome, caught count; verified in fresh log.
8. Focused scenario passes headless with fresh evidence — status pass, fresh result.json (see gates).
9. Windowed screenshot at chained-explosion moment — captured via `then_screenshot` inside the burst lifetime and image-inspected.

## Changed-file quality review

Diff scope matches plan clusters: `autoload/ProgressionManager.gd`,
`scripts/game/actors/Trap.gd`, `scripts/progression/managers/TrapProgressionManager.gd`,
`scripts/progression/trap.json`, `scripts/testing/{AgentHarness,HarnessActions,HarnessValues}.gd`,
plus new `tests/scenarios/traps_buried_ordnance_progression.json`. No scope creep;
no workflow artifacts counted. New code is typed, guard-clause style, debug-tagged
logging per CLAUDE.md conventions. Prior open quality note `typed-vars-new-code`
is resolved by this diff (marked RESOLVED in quality-notes.md); remaining untyped
`cfg_any` occurrences are pre-existing legacy outside the feature diff.

No new-test overlap found: the focused scenario JSON is the only buried-ordnance
test in `tests/scenarios/`; no existing scenario covers this behavior.

## Verdict

pass — build gate green, full suite green, focused scenario green with fresh
evidence, all 9 criteria promoted to Done, quality note resolved.
