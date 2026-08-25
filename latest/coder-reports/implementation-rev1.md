# Coder report: implementation (revision 1)

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
