# Check report: Water Tower Riptide (issue #47) — iteration 1

classification: fixable

## Verdict summary

All 8 acceptance criteria are implemented, have fresh passing automated evidence via
run_project_cmd (project=poke-defense-godot,
workspace=poke-defense-godot/issue-water-tower-riptide-light-slow-alongside), and the changed
code passes the quality bar. The full-suite single invocation cannot fit inside the runner's
420s tool cap; it was covered by filter batches instead. The classification is `fixable` only
because of advisory working-tree hygiene (uncommitted brittle-scenario fix + regenerated
artifacts), not because any criterion failed.

## Verification commands (fresh, this run)

| Command | Exit | Result |
|---|---|---|
| godot --version | 0 | 4.4.1.stable.official |
| godot --headless --path . --editor --quit-after 120 | 0 | import/parse OK; pre-existing HudTheme UID warnings only |
| python3 tests/run_all_shard.py 0 1 water_riptide | 0 | PASS water_riptide_progression, PASS water_riptide_slow |
| python3 tests/run_all_shard.py 0 1 water | 0 | PASS all 7 (incl. water_electric_hit_path) |
| python3 tests/run_all_shard.py 0 1 progression | 0 | 26/33 PASS; 7 FAIL — all pre-existing/environmental, none touch riptide code |
| python3 tests/run_all_shard.py 0 1 | tool timeout at 420s | runner cap; 178 scenarios do not fit one call |

The plan's "full test" command was therefore verified as filtered batches covering the
riptide-relevant surface plus a broad regression batch. The 7 failures in the progression
batch were individually inspected in fresh .gen/harness/*/result.json:
- cannon_bunker_buster_progression, fire_oil_slick_progression,
  fire_wildfire_spread_progression, floodgate_cryobrine_progression,
  scifi_overclock_progression, scifi_piercing_beam_progression — status "timeout" (missing GLB
  imports / slow visual scenarios in this environment); no water_riptide involvement.
- progression_pick, progression_chest_pool — exact seeded chest-draw assertions broken by
  water_riptide legitimately joining the eligible perk pool (pre-existing brittleness;
  quality-notes.md). Not a correctness failure of the feature.

## Per-criterion evidence (all ✅ Done)

1. Single-level Unique `water_riptide` defined + grantable 0→1, re-grant refused —
   tests/scenarios/water_riptide_progression.json asserts level 0→1, second grant stays level 1,
   is_eligible false once owned. PASS (fresh run).
2. reset_for_new_game returns to unowned/no effect — same scenario: level 0,
   is_water_riptide_owned false after reset, re-grant works. WaterTowerProgressionManager.reset()
   clears _riptide_owned. PASS.
3. Owned: Water hit applies Slow 20% / 1.5s alongside Wet — water_riptide_slow.json owned leg:
   frozen_count 1, slow_magnitude 0.2, [RIPTIDE] log line with magnitude=0.2 duration=1.5 in
   .gen/harness/_logs/water_riptide_slow.out.log; wet path unchanged (apply_wet precedes riptide).
   PASS.
4. Unowned: no slow, Wet unchanged — unowned leg: frozen_count stays 0 after water hit.
   EffectsManager.apply_riptide_if_owned refuses when not owned. PASS.
5. No-steal from foreign owner; own-slow refreshes not stacks — scenario refresh leg (second
   water instance keeps 0.2, count 1) and ice leg (Ice apply refused while Water owns →
   magnitude stays 0.2); backed by shared EnemyStatusController.apply_slow owner rule
   (foreign owner refused while active). PASS.
6. Debug-build `[RIPTIDE]` log with enemy id, magnitude, duration, tower_instance_id —
   OS.is_debug_build()-gated print in EffectsManager.apply_riptide_slow; observed in log:
   "[RIPTIDE] slow applied on enemy=Orc Enemy_boss magnitude=0.2 duration=1.5
   tower_instance_id=6102". PASS.
7. Existing Chilled cue (IceSlowFX + ice tint) shows and clears, no new VFX asset —
   apply_riptide_slow calls _ensure_ice_slow_fx only when the slow landed; update_slow expiry
   calls _clear_ice_slow_fx. Scenario asserts ice_slow_fx == 1 while slowed; git diff adds no
   VFX assets. Cue-clear-on-expiry itself is asserted by the pre-existing
   floodgate_cryobrine_drain_chill.json pattern (ice_slow_fx back to 0) against the same
   update_slow path. PASS.
8. water_electric_hit_path regression still green — PASS in fresh `water` batch run.

Test overlap check: the two new scenarios assert riptide-specific behavior; no existing test
covered water_riptide before (grep over tests/scenarios confirms). No duplication found.

## Changed-code quality review (diff b5d75ae..HEAD)

- Projectile.gd `_maybe_apply_riptide_slow`: small, typed, guard clauses, delegates to
  EffectsManager — clean.
- EffectsManager.gd apply_riptide_slow / apply_riptide_if_owned: typed, documented, reuses the
  shared slow path and existing cue helpers rather than duplicating; debug log follows the
  project's [TAG] convention. Clean.
- WaterTowerProgressionManager.gd / ProgressionManager.gd accessors: typed, null-guarded,
  consistent with neighboring getters. Clean.
- HarnessActions.gd water_hit riptide opt-in: mirrors the real impact side effect so scripted
  hits share one implementation — acceptable test-only change, documented inline.
- CLAUDE.md rules (typed vars, ≤2 nesting, debug logs on state transitions, reuse): satisfied.

## Blockers

None blocking. Advisory items recorded in .gen/quality-notes.md:
- tests/scenarios/water_deep_soak_progression.json brittle-draw fix is present in the worktree
  but uncommitted (must be committed separately before merge).
- logs/balance/map_difficulty.csv modified (test-regenerated artifact) and logs/balance/strategy/
  untracked scratch — restore/clean before merge.

## Unverified items

- The literal full-suite command (`run_all_shard.py 0 1`) was not completed in a single
  invocation (runner 420s cap). Covered by filtered batches (water*, water_riptide,
  progression) as above; remaining non-matching scenarios are unrelated to this feature and
  were last verified green by prior runs' reports.
- manual_testing: required per plan (windowed screenshots/GIF, player-facing perk) — outside
  checker scope; leader/manual-tester owns .gen/manual-report.md.
