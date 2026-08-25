# Check report: Water Tower Riptide (issue #47) — iteration 2 (revision-check-1)

classification: pass

## Verdict summary

All 8 acceptance criteria are implemented, committed (HEAD ed2c29f on
issue/water-tower-riptide-light-slow-alongside, feature base b5d75ae), and hold fresh passing
automated evidence from run_project_cmd (project=poke-defense-godot,
workspace=poke-defense-godot/issue-water-tower-riptide-light-slow-alongside) in this check.
Iteration-1 advisory items were resolved by commits 515049a / ed2c29f. The classification is
`pass`: every criterion is Done with adequate tests; remaining notes are advisory only.

## Verification commands (fresh, this run)

| Command | Exit | Result |
|---|---|---|
| godot --version | 0 | 4.4.1.stable.official.49a5bc7b6 |
| godot --headless --path . --editor --quit-after 120 | 0 | import/parse OK; only pre-existing HudTheme invalid-UID warnings |
| python3 tests/run_all_shard.py 0 1 water_riptide | 0 | PASS water_riptide_progression, PASS water_riptide_slow |
| python3 tests/run_all_shard.py 0 1 water | 0 | PASS all 7 (incl. water_electric_hit_path regression) |
| python3 tests/run_all_shard.py 0 1 progression | 0 exit / 28 PASS, 5 FAIL | failures are pre-existing unrelated scenario timeouts (see below) |

The plan's literal full-suite command (`run_all_shard.py 0 1`, ~178 scenarios) does not fit a
single runner call; it was covered as the riptide-relevant filtered batches above plus the broad
`progression` regression batch, matching iteration 1's approach.

The 5 progression-batch FAILs (cannon_bunker_buster_progression, fire_oil_slick_progression,
fire_wildfire_spread_progression, floodgate_cryobrine_progression, scifi_piercing_beam_progression)
are status "timeout" on a malformed `wait_for_condition` action whose field name is empty — a
pre-existing scenario-authoring bug in unrelated towers' scenarios; none touch riptide code or
water behavior. Not caused by this feature; recorded in quality-notes scope is limited to the
already-recorded brittleness family.

## Per-criterion evidence (all ✅ Done, fresh this iteration)

1. Single-level Unique `water_riptide` in scripts/progression/water_tower.json ("type": "Unique",
   maxLevels 1, compatibility ["water"]); grantable via apply_progression → level 1, re-grant
   refused once owned. PASS: fresh water_riptide_progression.
2. reset_for_new_game returns unowned/no effect — WaterTowerProgressionManager.reset() clears
   _riptide_owned; scenario asserts level 0 + re-grant works after reset. PASS.
3. Owned: Water hit applies Slow 20% / 1.5s alongside Wet — water_riptide_slow owned leg:
   frozen_count == 1, slow_magnitude == 0.2; Wet applied first via Projectile._resolve_hit
   (_apply_wet_status then _maybe_apply_riptide_slow). PASS: fresh water_riptide_slow.
4. Unowned: no slow, Wet unchanged — unowned leg frozen_count stays 0;
   EffectsManager.apply_riptide_if_owned refuses when not owned. PASS.
5. No-steal / refresh-not-stack — EnemyStatusController.apply_slow refuses a foreign owner while
   slow_time_left > 0 (line 14); scenario refresh leg keeps count 1 at 0.2 and ice leg leaves
   Water-owned magnitude at 0.2. PASS.
6. Debug `[RIPTIDE]` log with enemy id, magnitude, duration, tower_instance_id — OS.is_debug_build()
   gated print in EffectsManager.apply_riptide_slow; observed in .gen/harness/_logs/
   water_riptide_slow.out.log: "[RIPTIDE] slow applied on enemy=Orc Enemy_boss magnitude=0.2
   duration=1.5 tower_instance_id=6102". PASS.
7. Existing Chilled cue, no new VFX asset — apply_riptide_slow calls _ensure_ice_slow_fx when the
   slow lands; expiry path update_slow calls _clear_ice_slow_fx; scenario asserts ice_slow_fx == 1;
   diff adds no VFX assets. PASS.
8. water_electric_hit_path still green — PASS in fresh `water` batch. PASS.

Test overlap check: no existing test asserted water_riptide behavior before these two new
scenarios; grep of tests/scenarios shows no duplication of existing coverage.

## Changed-code quality review (diff b5d75ae..ed2c29f)

- Projectile.gd _maybe_apply_riptide_small helper: typed, guard clauses, delegates to
  EffectsManager — clean.
- EffectsManager.gd apply_riptide_slow / apply_riptide_if_owned: typed, documented, reuses shared
  slow path and existing cue helpers; debug log follows [TAG] convention. One advisory accuracy
  note on refusal detection recorded in quality-notes.md (does not affect criterion behavior).
- WaterTowerProgressionManager.gd consts/getters/reset: typed, consistent with neighbors. Clean.
- ProgressionManager.gd accessors: null-guarded has_method pattern matching existing style. Clean.
- HarnessActions.gd water_hit riptide opt-in: mirrors the real impact side effect so scripted hits
  share one implementation; test-only, documented inline. Acceptable.
- CLAUDE.md rules (typed vars, ≤2 nesting depth, debug logs on state transitions, reuse): satisfied.

## Quality-notes reconciliation (append-only file updated this iteration)

- chest-draw-brittleness (iter 1) — RESOLVED: 515049a + ed2c29f landed; progression_pick and
  progression_chest_pool PASS in the fresh progression batch.
- uncommitted-working-tree-changes (iter 1) — RESOLVED for the code part: the
  water_deep_soak_progression.json fix is committed (515049a). Remaining dirt is test-run artifact
  only, re-recorded as new advisory entry regenerated-test-artifacts (map_difficulty.csv modified,
  logs/balance/strategy/ untracked scratch).
- New advisory: riptide-refusal-log-accuracy (EffectsManager.gd) — debug log/return value can claim
  "applied" when a foreign-owned active slow causes a silent refusal; gameplay (no steal) remains
  correct and criterion-compliant. Advisory only; does not demote any criterion.

## Blockers

None.

## Unverified items

- Literal single-invocation full suite (`run_all_shard.py 0 1`) exceeds the runner's per-call cap;
  covered by the filtered batches listed above (consistent with iteration 1).
- manual_testing: required per plan (windowed screenshots/GIF, player-facing perk) — outside
  checker scope; manual-tester profile owns .gen/manual-report.md.
