# Check report: water-conductive-flood-wet-splash (issue #45)

classification: fixable

## Verdict

Implementation is complete and the focused harness is green, but the full test
suite does not pass end-to-end in this environment (runner OOM kill at exit 137
plus pre-existing scenario timeouts/failures unrelated to this feature). Per the
build-and-test gate, Done items cannot be confirmed while the full suite is not
green; criteria move to Pending with evidence recorded.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=godot-td/issue-water-conductive-flood-wet-splash)

| Command | Exit | Result |
|---|---|---|
| `git status --short` | 0 | 8 modified + 2 new scenario files |
| `godot --headless --path . --import --quit-after 5` | 0 | import gate PASS (pre-existing HudTheme UID warnings only) |
| `python3 tests/run_all_shard.py 0 1 water_conductive_flood` | 0 | PASS water_conductive_flood_aoe, PASS water_conductive_flood_progression |
| `python3 tests/run_all_shard.py 0 1` (full suite) | 137 | runner worker OOM-killed after ~41s / 4 scenarios; retried twice more: two 420s tool-timeouts with no output, then shard re-slices also exit 137 |

Full-suite evidence from fresh per-scenario logs (.gen/harness/_logs/*.out.log,
written incrementally by the modified run_all_shard.py): of ~100 scenarios that
produced logs across attempts, both new scenarios pass; 16 scenarios show
status=timeout or fail (cannon_bunker_buster, cannon_bunker_buster_progression,
cannon_heavier_shells_blast, cave_decline_seals_reveal_unseals,
cave_discovery_long_carve, cave_pending_seals_entrance_instantly,
curse_overheat_cycle, fire_oil_slick*, fire_wildfire_spread_*,
floodgate_corrosive_soak*, floodgate_cryobrine_progression, issue_35,
issue_86, projectiles_2x_roster, scifi_overclock_progression) — all outside this
feature's changed files. The implementor reported these fail identically on a
clean stash; I did not independently reproduce the clean-stash comparison
(worktree has no feature commit to diff against), so pre-existing status is
reported, not proven by me.

## Criteria evidence

1. Perk exists as Unique and eligible like other Water Uniques — implementation
   present (`scripts/progression/water_tower.json`, WaterTowerProgressionManager);
   scenario water_conductive_flood_progression passes (level 0 -> apply -> 1,
   save/reload persistence). Status moved to Pending only because the full-suite
   gate failed.
2. Apply raises level to 1, radius > 0 accessor — same scenario asserts enabled
   and radius == 1.5 via get_flood_config passthrough. Pending per gate.
3. Multi-enemy Wet in radius — water_conductive_flood_aoe passes: control arm
   wet_count == 1, perk arm wet_count == 2 via real Projectile._resolve_hit
   (HarnessActions simulate_projectile path). Pending per gate.
4. Without perk single-target only — control arm of same scenario. Pending per gate.
5. Outside-radius enemies stay dry — third GSB at 1.9m stays non-Wet while two at
   <=1.5m are Wet. Pending per gate.
6. Splash visual extends to radius — `_create_water_splash` raises splash_radius
   to perk radius and +8 droplets; asserted indirectly via code path exercised in
   the AoE scenario (visual pixel coverage itself requires windowed manual
   testing per plan Notes). Pending per gate.
7. Wet renders via existing EnemyHealthBar icon — reuses EffectsManager.apply_wet;
   no dedicated assertion beyond existing wet rendering tests; acceptable reuse.
   Pending per gate.
8. [WATER-FLOOD] debug log naming target and count — expectation "out.log
   contains [WATER-FLOOD]" passes in the AoE scenario result.json. Pending per
   gate.

## Changed-file quality findings

No rule violations found in the diff:
- Typed variables throughout; guard clauses keep nesting shallow; functions small
  and single-purpose (_apply_flood_wet, _alive_enemies_near_target).
- Debug logging follows OS.is_debug_build() + [WATER-FLOOD] tag per CLAUDE.md.
- Surgical changes: HarnessActions/HarnessValues additions are minimal and serve
  testing the feature; run_all_shard.py change fixes log materialization for
  value sources (test-infra, justified).
- Scope creep note (advisory): `logs/balance/map_difficulty.csv` was regenerated
  (map_1/5/6 rows changed). This looks like a side effect of running balance
  tooling during development rather than a requested change; recommend reverting
  it before commit unless intended.

## Blockers

- Full-suite run cannot complete inside the runner: worker OOM kill (exit 137)
  and repeated 420s command timeouts. This is an environment capacity issue, not
  a project-code failure and not a missing-runner blocker (focused runs work).

## Unverified items

- Clean-stash comparison proving the 16 failing/timeout scenarios are
  pre-existing (implementor claim only).
- Windowed screenshot evidence of splash radius covering enemies (manual-testing
  scope per plan; headless cannot capture pixels).
