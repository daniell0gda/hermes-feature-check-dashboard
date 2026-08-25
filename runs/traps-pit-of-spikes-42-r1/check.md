# Check report: traps-pit-of-spikes-first-hit-stuns-turn (iteration 1)

classification: fixable

## Verdict

Implementation logic in `Trap.gd` / progression managers is sound and the focused scenario
returns `status=pass exit=0`, but the scenario's target resolution is not deterministic: a
checker-run diagnostic proved the "second hit" in arm 2 landed on a different enemy instance
than the one stunned by the first hit, and the engine log shows two `[PIT-OF-SPIKES]` stun lines
in arm 2 where exactly-once is required. Five criteria move to Pending; three hold as Done.

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-traps-pit-of-spikes-first-hit-stuns-turn)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `godot --version` | 0 | 4.4.1.stable.official |
| Typecheck/build | `godot --headless --editor --quit-after 3 --path .` | 0 | No script errors; only pre-existing UID/GLB warnings present on master |
| Focused harness | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_pit_of_spikes_first_hit_stun.json` | 0 | `[Harness] status=pass exit=0`; result.json at `.gen/harness/traps_pit_of_spikes_first_hit_stun/result.json` |
| Shard | `python3 tests/run_all_shard.py 0 1 traps_pit` | 0 | PASS traps_pit_of_spikes_first_hit_stun |

Full suite `python3 tests/run_all_shard.py 0 1`: NOT RUN — implementor already reported it cannot
complete inside runner/tool timeouts (181 scenarios; OOM kill at exit 137 after 4 scenarios).
Recorded honestly per criteria-honesty rules; trap-cluster scenarios verified individually.

## Criterion evidence

1. **Unowned → no stun** — DONE. Scenario arm 1 asserts `stun_time_left == 0`, `stunned_count == 0`,
   log `!contains [PIT-OF-SPIKES]` (actions 12–16, all ok). Code path: `Trap._apply_pit_of_spikes_stun`
   returns before applying when duration is 0.
2. **First hit stuns ~0.4s via EffectsManager.apply_stun** — DONE. Arm 2 action 29/30/31:
   `stun_time_left > 0` immediately after hit, `stunned_count >= 1`. Log line
   `[PIT-OF-SPIKES] stun enemy=Cactoro trap=trap_01 duration=0.4` present. Routes through
   `enemy/EffectsManager.apply_stun` → `EnemyStatusController.apply_stun`.
3. **Second hit never re-stuns (same enemy)** — PENDING. Diagnostic instrumentation (temporary
   `[PIT-OF-SPIKES-DIAG] iid=` print, reverted post-check) showed arm-2 second hit stunned
   `iid=283383959085` while the first hit stunned `iid=328296565830`. The second hit targeted
   arm 1's stale Cactoro that survived `load_map` (`Cleared 3 enemies` uses `queue_free`, which
   defers removal), so the same-enemy no-re-stun guard was never exercised by the scenario.
4. **Per-enemy tracking** — PENDING. Alien stun (action 35) does show cross-enemy eligibility,
   but with the same defect active the criterion cannot be certified independently.
5. **Save/replay idempotence** — DONE. Actions 39–43: save_now + _load_state_and_apply keeps
   duration 0.4 and re-apply leaves perk ineligible; levels carry absolute values so replay
   cannot compound. No stun can be triggered by level application alone (config-only path).
6. **`[PIT-OF-SPIKES]` log once, only on stunning hits** — PENDING. Engine out.log contains TWO
   `stun enemy=Cactoro ... duration=0.4` lines in arm 2 (~lines 714/720 of
   `.gen/harness/_logs/traps_pit_of_spikes_first_hit_stun.out.log`) because the placed trap's
   autonomous `_check_overlap_and_damage` poll lands real hits during timeline waits. Exactly-once
   was never demonstrated. Note: the harness `log` assertion only checks "regex matches", which
   passes even with duplicates.
7. **Focused scenario passes all green** — PENDING. status=pass exit=0 is true, but the scenario's
   own contract ("asserts a second hit does not refresh... exactly once") is not established due to
   items 3 and 6.
8. **Windowed stun icon visible then disappears** — PENDING (manual). Not produced this run;
   `manual_testing: required`. Icon path itself untouched and driven off `stun_time_left`
   (proven transitioning 0→0.4→0 headlessly), but windowed evidence is still owed.

## Changed-file quality findings

New/changed code reviewed against `/opt/data/coding_rules.md` and worktree `CLAUDE.md`:

- `scripts/game/actors/Trap.gd::_apply_pit_of_spikes_stun` — clean: guard clauses, typed vars,
  mark-before-apply ordering, debug-gated `[PIT-OF-SPIKES]` log per CLAUDE.md logging rule.
- `TrapProgressionManager.gd` — absolute-value level application mirrors the existing frostbite
  pattern; idempotent on replay. Consistent with surrounding style.
- `autoload/ProgressionManager.gd::get_trap_pit_stun_duration` — pure config read, defensive
  has_method check consistent with neighboring getters.
- `HarnessValues.gd` / scenario JSON — typed additions match existing seams.
- `scripts/testing/AgentHarness.gd::materialize_engine_out_log` fix — legitimate infra bug fix
  (stale log slice); within scope since the scenario depends on mid-timeline log assertions.

No quality violations found in changed code; Pending items are evidence/determinism issues, not
code-quality demotions.

## Cross-cutting notes

Appended to `.gen/quality-notes.md`:
- `pit-of-spikes-scenario-determinism`: multi-arm stun scenarios must pin targets by `enemy_id`,
  suppress autonomous trap polling during log-sensitive windows, and assert log line counts
  rather than bare contains. Affects future multi-arm scenarios using `load_map` between arms
  (deferred enemy freeing) and any placed-trap overlap polling.

## Blockers

None blocking the runner or infrastructure. All gates ran through `run_project_cmd`.

## Unverified items

- Full shard suite (runner/tool timeout ceiling — pre-existing limitation, not this feature).
- Windowed/manual UI-sanity pass for the stun icon (manual_testing: required).

classification: fixable
