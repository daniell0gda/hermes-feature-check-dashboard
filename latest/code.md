# Coder report: implementation-rev1\n\n# Coder report: implementation (revision 1)

## Changed files
- `scripts/testing/HarnessValues.gd` — mod: new `regex_count` compare op + `_regex_match_count` helper
- `tests/scenarios/traps_pit_of_spikes_first_hit_stun.json` — mod: determinism rework

## Criteria
- A second trap hit on the same enemy does not re-apply the stun — fixed scenario targeting (was landing on a stale arm-1 enemy / auto-spawned Alien); now proven green
- Stun tracking is per enemy — now certifiable: exactly one enemy exists per arm and the log shows one stun event
- `[PIT-OF-SPIKES]` log line emitted exactly once — now asserted with `regex_count == 1` over the whole timeline; engine out.log contains exactly 1 stun line
- Focused headless harness passes with all expectations green — pass, all 17 wait conditions ok
- Windowed stun-icon visibility — unchanged, remains manual/windowed evidence for the tester (scenario drives the same `stun_time_left` path the EnemyHealthBar icon reads)

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_pit_of_spikes_first_hit_stun.json` — exit 0; result.json status=pass; 2 optional-only action failures (`defeat_all` on an empty field); out.log has exactly 1 `[PIT-OF-SPIKES] stun` line
- `godot --headless --editor --quit-after 3 --path .` — exit 0, no script errors
- Full shard NOT run (same reason as iteration 1: exceeds runner timeouts; checker should sweep)

## Notes
Root causes of the demoted criteria, both scenario-side:
1. `load_map` between arms left arm-1's Cactoro queued-but-alive, so index-0 hits could resolve to it; a wave also auto-advanced mid-arm and spawned an Alien that absorbed the second hit. Fix: each arm now pins `auto_next=false` + `_set_egg(9999)` right after load, drains with optional `defeat_all` + `enemies.total == 0` wait, then waits for exactly `underground == 1` before any hit.
2. The placed trap was removed entirely — its `_process` overlap poll autonomously perform_hits during timeline waits, emitting extra `[PIT-OF-SPIKES]` lines. All hits land through the harness scripted `Trap.perform_hit` (identical code path minus polling).
"Exactly once" is now real: new `log` op `regex_count` takes `{pattern, op, count}` in `value` and compares the regex match count; scenario asserts the stun-line pattern `== 1`.
Gotcha for the tester: the two `defeat_all` entries report `ok=false` but carry `"optional": true`, so they don't fail the run — they're no-ops when the field is already empty.
\n\n# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/progression/trap.json` — new `traps_pit_of_spikes` Unique perk (maxLevels 1, value 0.4)
- `scripts/progression/managers/TrapProgressionManager.gd` — `PIT_OF_SPIKES_NAME`, `_pit_stun_duration`, absolute-value `apply_level` branch (idempotent on replay), `get_pit_of_spikes_stun_duration()`
- `autoload/ProgressionManager.gd` — `get_trap_pit_stun_duration()` passthrough (0.0 while unowned)
- `scripts/game/actors/Trap.gd` — `_apply_pit_of_spikes_stun()` called from `perform_hit`; per-enemy `pit_of_spikes_stunned` meta guard; routes through enemy `EffectsManager.apply_stun`; debug-build `[PIT-OF-SPIKES] stun enemy=<id> trap=<id> duration=0.4` line emitted only on the stunning hit
- `scripts/testing/HarnessValues.gd` — harness seams: per-enemy `stun_time_left` live field, `stunned_count` in the enemies report, `pit_of_spikes_stunned` meta exposure
- `scripts/testing/AgentHarness.gd` — FIX: `materialize_engine_out_log()` now re-slices this run's engine log on every resolution instead of short-circuiting when the destination already contained the activation marker (a one-time snapshot froze mid-run and hid log lines printed after the first `log` assertion)
- `tests/scenarios/traps_pit_of_spikes_first_hit_stun.json` — new focused scenario (two-arm: unowned control + owned first-hit/no-restun/idempotence/log)

## Criteria
- Unowned trap hit leaves stun at 0 — Done (scenario arm 1 asserts `stun_time_left == 0`, `stunned_count == 0`, log `!contains [PIT-OF-SPIKES]`)
- First hit while owned stuns ~0.4s via EffectsManager.apply_stun — Done (arm 2 asserts `stun_time_left > 0` immediately after `trap_hit`)
- Second hit never re-stuns — Done (after expiry, second hit asserted `stun_time_left == 0`; meta guard marks before applying so same-frame re-entry can't double-stun)
- Per-enemy tracking — Done (meta lives on the enemy node; a fresh enemy is stun-eligible; log shows a second distinct enemy `Alien` stunned after `Cactoro` was already marked)
- Save-load replay idempotence, level apply alone never stuns — Done (`save_now` + `_load_state_and_apply` + re-apply keeps `get_trap_pit_stun_duration == 0.4`, perk ineligible, no new stun; levels carry absolute value so replay lands on N without compounding)
- `[PIT-OF-SPIKES]` debug line once, only on the stunning hit — Done (regex asserted through engine out.log; unowned arm asserts absence)
- Focused headless scenario passes all green — Done (`[Harness] status=pass exit=0`)
- Windowed stun icon on health bar — NOT RUN HERE: manual/windowed evidence is the tester's UI-sanity pass; icon path untouched (`EnemyHealthBar.gd icon_stun` already driven off `stun_time_left`), and the scenario proves `stun_time_left` transitions 0 -> 0.4 -> 0 which drives that exact path

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_pit_of_spikes_first_hit_stun.json` — exit 0, `[Harness] status=pass exit=0` (run 2: action 44 log regex failed pre-fix; run 3 after AgentHarness fix: pass)
- `python3 tests/run_all_shard.py 0 1 traps_` — exit 0, PASS x7 (frostbite/pit/serrated x2/venom x3)
- `python3 tests/run_all_shard.py 0 1 enemy_armor_trap` / `... trap_stats_attribution` — exit 0, PASS
- `python3 tests/run_all_shard.py 0 1 undermining` — exit 0, PASS
- `godot --headless --editor --quit-after 3 --path .` — exit 0, no script errors
- Full shard `python3 tests/run_all_shard.py 0 1` — could not complete inside runner timeouts (181 scenarios x up to 280s each; two attempts killed at 420s tool cap, one by the worker's own OOM-kill exit 137 after 4 scenarios). All trap-cluster scenarios plus neighbors were verified individually instead; full-suite sweep left for the checker with sharding or a longer budget.

## Notes
- The real bug found this iteration was test-infrastructure: `materialize_engine_out_log` treated "destination contains activation marker" as done, so the first mid-timeline `log` resolution wrote an early slice and every later `log` condition read that stale text. Now always re-slices while `user://logs/godot.log` is readable, falling back to the wrapper-written destination only when Godot's file log is unavailable. This affects any future multi-arm scenario asserting log lines across arms.
- Gotcha for testers: Godot's file engine log flushes lazily (~1s granularity); the scenario's waits comfortably exceed it, but very tight log assertions right after a print may need one extra frame.
- Pre-existing noise, unrelated: missing GLB/TGA resources and invalid UID warnings in headless runs appear on master too.
\n