# Check report: porter-broad-sweep (iteration 1)

classification: fixable

## Verdict

All 9 criteria are moved to Pending solely because the full-suite gate
(`python3 tests/run_all_shard.py 0 1`) cannot complete in this worker: it was
killed with exit code 137 (OOM) after ~4 scenarios, and the finer `0 8` shard
exceeds the runner's 420s tool window. The feature implementation itself is
verified green by a fresh focused harness run, the editor/import gate, and a
completed 16th-shard slice; there are no quality violations in the feature
diff.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-porter-broad-sweep)

- `["godot","--version"]` — exit 0 (runner reachability probe).
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0.
  Import/parse gate clean; only pre-existing invalid-UID warnings
  (HudTheme.tres / UI.tscn), unchanged from baseline.
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_broad_sweep.json"]`
  — exit 0, `[Harness] status=pass`, 30/30 actions ok. Fresh result:
  `.gen/harness/porter_broad_sweep/result.json`. Log evidence observed directly:
  chest pool 35 → 36 (after `[PORTER_MASS_TRANSIT] owned`, with
  porter_broad_sweep drawn) → 35 at L3; `[PORTER_BROAD_SWEEP] apply L1/L2/L3
  sweep_radius_multiplier=x1.50/x1.80/x2.00`.
- `["python3","tests/run_all_shard.py","0","1"]` — exit 137 (SIGKILL/OOM) after
  PASS×3 + FAIL burn_status_refresh_pending_damage (~4 scenarios). Full-suite
  gate FAILED → all Done items demoted to Pending per build/test gate.
- `["python3","tests/run_all_shard.py","0","8"]` — timed out at the 420s tool
  window (no completion recorded).
- `["python3","tests/run_all_shard.py","0","16"]` — exit 0 for its slice; 7/12
  PASS, FAIL: fire_wildfire_spread_runtime, issue_35_timed_hazards_map_change,
  porter_boss_runner, projectiles_10x_beam_cone.
- `porter_boss_runner` rerun individually — exit 1, status=timeout at action 75
  (`log contains [PORTER_BOSS_RUNNER] miss`). Baseline attribution: with the
  entire feature diff stashed (`git stash push -u` Hermes-side), the identical
  individual rerun reproduced the same timeout at the same action; diff
  restored afterwards (`git stash pop`). Pre-existing flake, not caused by this
  issue. Log shows wave 7 Ninja_boss spawns but the Porter never reaches
  charge-complete in that segment — consistent with the scenario's own note
  that "Porter outcomes are not byte-reproducible".

## Per-criterion evidence

Every criterion's implementing behavior is asserted by the focused scenario
`tests/scenarios/porter_broad_sweep.json`, which passed freshly this iteration
(30/30 actions). The only reason they are Pending is the failed full-suite
gate, which blocks keeping any item Done.

## Changed-file quality review

Diff (`git diff HEAD`, 4 files + new scenario): typed GDScript throughout,
guard-clause style, no nesting >2, debug-only `[PORTER_BROAD_SWEEP]` print per
CLAUDE.md logging rule, absolute-ratio application is idempotent on reload and
matches the porter_wide_gate pattern. No violations found; no scope creep;
no test overlap (new scenario asserts new behavior only).

## Quality notes status

- `full-suite-shard-oom` — still OPEN, confirmed this iteration: `0 1` exit
  137 OOM, `0 8` exceeds 420s tool window. Required change stands: split
  shards ≥ `0 16` (that slice completed within budget) or raise worker
  memory/timeout.
- `pre-existing-harness-failures` — still OPEN, extended: cannon_bunker_buster,
  fire_flashover_spread, fire_wildfire_spread_runtime plus now also observed
  issue_35_timed_hazards_map_change, projectiles_10x_beam_cone, and
  porter_boss_runner (baseline-reproduced with the feature diff stashed).

## Blockers

None infra-level: run_project_cmd works. The full-suite gate failure is a
worker resource limitation (OOM/timeout), classified fixable.

## Unverified items

- Complete single-pass full-suite result (`run_all_shard.py 0 1`) — blocked by
  worker OOM; needs finer sharding or more worker memory before any criterion
  can be restored to Done.
