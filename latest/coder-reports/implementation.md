# Coder report: implementation (revision-code-1, revision 2)

## Root cause of the previous timeout (differs from check.md's geometry theory)

The prior check blamed cave-fixture geometry ("static spawns sit outside
trigger_radius"). Fresh evidence disproved this: result.json from the failing run
already showed trap_01 damage instrumentation firing in PAIRS (direct hit + chain
blast) — the chain code worked. The real blocker was the harness log capture:

- `AgentHarness.engine_out_log_path()` keys the per-run out.log by scenario id only,
  and `materialize_engine_out_log()` short-circuits when that file already contains
  this run's marker (`[Harness] scenario '<id>' from <path>`), which is identical
  across runs. Once one stale file existed under `.gen/harness/_logs/`, every later
  run grepped the STALE text. That is why even `[TrapProgression]` (which certainly
  printed every run) reported actual="".
- Secondary: a one-time materialization also freezes log polling at its first read,
  so prints landing after the first poll were invisible to wait_for_condition.

## Changed files

- `scripts/testing/AgentHarness.gd`
  - `_reset_stale_engine_out_log()`: delete the previous run's per-scenario out.log
    at activation (called from `_activate` after scenario load).
  - `materialize_engine_out_log(force := false)`: force re-slices the engine file log.
  - `wait_for_condition` gains optional `then_screenshot`: captures on the condition's
    first true frame, so short-lived VFX (0.15 s burst) are caught; skipped headless.
- `scripts/testing/HarnessValues.gd`: `log` source resolves with force=true so polling
  conditions see fresh prints instead of a frozen snapshot.
- `scripts/game/actors/Trap.gd`: typed locals (`ug_sys`, `enemy_node`, loop var)
  per open quality note `typed-vars-new-code`; no behavior change.
- `autoload/ProgressionManager.gd`: typed `cfg_any: Variant` (same quality note).
- `tests/scenarios/traps_buried_ordnance_progression.json`
  - Chain-blast arm: removed the 1.5 s pre-wait before the log wait and moved the
    screenshot into `then_screenshot`, so the capture lands inside the burst life.
    Kept an optional aftermath screenshot.
  - No-chain arm + end-of-run expectation: replaced unpassable blanket
    `"!contains [BURIED_ORDNANCE] trap=trap_03"` (trap_03 legitimately logs
    `chain=0 rolled=false caught=0`) with positive `chain=0 rolled=false caught=0`
    plus negative `!regex "trap=trap_03 chain=1"`.

## Criteria

All 9 plan criteria exercised green:
perk Unique eligibility/grant/idempotency (arm 1, actions 1–17); chance/radius chain
on underground hit (`caught=1|2` with ExplosionFX bursts); no chain without perk
(trap_03 arm); non-underground immunity (structural underground-only iteration +
underground-only source list); visible VFX (windowed screenshot shows the burst at
the blast site); determinism via `set_buried_ordnance_next_roll(0.0)` forced roll;
debug-only `[BURIED_ORDNANCE]` line; focused scenario headless pass with fresh
evidence; windowed screenshot at the chained-explosion moment.

## Commands and results (all via run_project_cmd)

- Focused (headless): `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_buried_ordnance_progression.json` — exit 0; `[Harness] status=pass exit=0`; 48 actions all ok; all 4 expectations pass; fresh `.gen/harness/traps_buried_ordnance_progression/result.json`.
- Windowed same scenario (screenshot run): exit 0; status pass; screenshots captured: `shots/buried_ordnance_chain_blast.png` (burst verified visible via image inspection) + `buried_ordnance_aftermath.png`.
- Full test: `... --harness=res://tests/scenarios/display_damage_surface_parity.json` — exit 0; status pass.
- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0; parse/import clean.

## Notes / gotchas for the tester

- The stale-out.log trap applies to ANY scenario reusing an id after a crashed or
  killed run; `_reset_stale_engine_out_log` now handles it automatically.
- Log-based waits are now live-polling; a scenario asserting `!contains X` where X
  is legitimately logged as a benign variant must scope the assertion to the
  meaningful form (see the `chain=1` regex change above).
- Screenshot timing: place captures via `then_screenshot` immediately after the
  trigger condition; a separate screenshot action after other awaits misses VFX
  shorter than ~1 s.
