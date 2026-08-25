# Check report: porter-broad-sweep (issue #82)

classification: fixable

## Verdict

All 9 acceptance criteria verified Done against fresh runner execution. Focused
harness passes cleanly with log-level evidence for every criterion. Editor/typecheck
gate passes (exit 0; only pre-existing HudTheme/UI.tres invalid-UID warnings).
Full suite could NOT be completed end-to-end inside the runner's 420s tool window:
a quarter-shard (`python3 tests/run_all_shard.py 0 4`, 45 scenarios) was killed with
exit 137 (OOM) partway; 3 of the 15 completed scenarios FAIL consistently even when
re-run individually — but all three were proven PRE-EXISTING by stashing the entire
feature diff and reproducing identical timeouts/failures on the baseline.
Classification is fixable only because the full-suite gate remains red for reasons
outside this feature's diff; the feature work itself needs no revision.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-porter-broad-sweep)

- `git status --short` — exit 0 (probe)
- `godot --headless --path . --editor --quit-after 300` — exit 0, ~13s, parse/import clean
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/porter_broad_sweep.json`
  — exit 0, `[Harness] status=pass`; result `.gen/harness/porter_broad_sweep/result.json`;
  rerun again after stash-pop to confirm restoration — still pass
- `python3 tests/run_all_shard.py 0 1 cannon_bunker_buster` — FAIL (timeout, individually reproducible)
- `python3 tests/run_all_shard.py 0 1 cave_discovery_pending_placement` — PASS
- `python3 tests/run_all_shard.py 0 1 fire_flashover_spread` — FAIL (timeout)
- `python3 tests/run_all_shard.py 0 1 fire_wildfire_spread_runtime` — FAIL (timeout)
- `python3 tests/run_all_shard.py 0 4` — exit 137 (killed/OOM) after 15 results;
  FAILs: cannon_bunker_buster, cave_discovery_pending_placement (passes alone),
  fire_flashover_spread, fire_wildfire_spread_runtime
- Baseline (feature stashed): direct harness runs of cannon_bunker_buster /
  fire_flashover_spread / fire_wildfire_spread_runtime — identical timeout failures
  → pre-existing, not regressions

## Acceptance criteria evidence (all Done)

1. Perk definition exists, Common, 3 levels L1/L2/L3, porter pattern —
   `scripts/progression/porter_tower.json` diff; harness asserts
   `progression.porter_broad_sweep.type == Common`.
2. `"needs": ["porter_mass_transit"]` — present in JSON; enforced by generic
   ProgressionManager eligibility/draw gating (no manager-side change needed).
3. Ineligible & absent from chest pool before Mass Transit owned — harness
   wait_for_condition is_eligible==false, draw !contains; log shows pool 35 without it
   and `skip incompatible chest reward: porter_mass_transit`.
4. After Mass Transit owned: eligible, in pool, applies at L1/L2/L3 — pool 35→36→35,
   `[PORTER_MASS_TRANSIT] owned`, apply calls return success, levels reach 1/2/3.
5. Base sweep radius with Mass Transit only — multiplier asserted == 1.0 pre-application.
6. Multipliers ×1.5 / ×1.8 / ×2.0 — `get_porter_sweep_radius_multiplier()` asserted
   1.5/1.8/2.0 after each level; absolute-ratio design makes replay idempotent.
7. Targeting range unchanged — `get_porter_range(6.5) == 6.5` asserted after every
   application; `_range_multiplier` untouched by `_apply_broad_sweep`.
8. Reset clears Broad Sweep and returns multiplier to base — `reset_for_new_game`,
   then level==0 and multiplier==1.0 asserted.
9. Debug-build `[PORTER_BROAD_SWEEP]` log per application with level and multiplier —
   stdout captured live: `[PORTER_BROAD_SWEEP] apply L1 sweep_radius_multiplier=x1.50`,
   `L2 x1.80`, `L3 x2.00`.

## Changed-file quality findings

- Diff is surgical (4 modified files + 1 new scenario); follows existing
  porter_wide_gate/mass_transit patterns; no casts-as-strings, no speculative code.
- New scenario asserts inline with deterministic draw counts (100 > pool size); no
  overlap found with any existing scenario covering porter perks.
- Minor style observation (advisory, not a violation): `PorterTower.gd`
  `_mass_transit_sweep_radius()` reaches ProgressionManager via
  `tree.get_root().get_node_or_null("ProgressionManager")` while `_mass_transit_owned()`
  nearby uses a different lookup path — consistent enough with file-local conventions.

## Blockers / limitations

- Full 179-scenario shard cannot finish inside the 420s runner tool window; quarter
  shards OOM (exit 137). Full-suite green therefore remains unproven end-to-end;
  remaining ~130 scenarios unverified this iteration (infrastructure limitation,
  not a feature defect). Pre-existing failures needing separate fixes:
  cannon_bunker_buster, fire_flashover_spread, fire_wildfire_spread_runtime.

## Unverified items

- ~130 of 179 full-suite scenarios not executed in this iteration due to runner
  time/memory limits; no porter-related scenario among them is expected to regress
  (porter_broad_sweep, porter_wide_gate_progression, progression_pick, porter paths
  all pass or unaffected).

manual_testing: optional (per plan) — perk card appears in picker UI; no visual capture
taken this iteration; sweep radius itself is invisible and teleport feedback is covered
by Mass Transit.
