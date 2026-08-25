# Coder report: implementation-rev-code-1\n\n# Coder report: implementation (revision-code-1, r8)

## Changed files
- none this iteration (implementation verified intact from prior cycles:
  `scripts/game/Game.gd`, `scripts/testing/HarnessActions.gd`,
  `scripts/testing/HarnessValues.gd`, `tests/scenarios/carve_pan_no_flip.json`,
  `tests/scenarios/carve_camera_drag_spin.json`)

## Criteria
All 11 criteria in `carve-pan-stability` + `carve-pan-regression-scenario` — Done
(no product gap found; per revisions.md this cycle is re-do/verify).

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_pan_no_flip.json` — exit 0; status=pass. Probes: armed top-down pitch 1.5708 / basis_x_yaw 0.0; small pan dx=12 and large pan dx=-90 translate with yaw delta 0.0 < 0.01; log shows 9× `[CARVE_CAMERA] pan complete (pre yaw=… post yaw=…)` and `cancel restored pre-carve angles`. Log: `.gen/full_revision_code1_carve_pan_no_flip.log`
- same for `carve_camera_drag_spin.json` — exit 0; status=pass (real rotate_camera actions, no arg-conversion errors). Log: `.gen/full_revision_code1_carve_camera_drag_spin.log`
- same for `carve_camera_topdown.json` — exit 0; status=pass. Log: `.gen/full_revision_code1_carve_camera_topdown.log`
- `godot --headless --editor --path . --quit-after 3` — exit 0, clean typecheck. Log: `.gen/full_revision_code1_typecheck.log`
- `bash .gen/run_full_suite.sh` — see `.gen/full_revision_code1_suite.log` (carve_* scenarios pass; legacy-domain reds are known pre-existing per quality-notes).

## Notes
- godot only on PATH via `export PATH=/opt/data/profiles/code/home/bin:$PATH`.
- Sole outstanding blocker remains the required manual windowed GIF (Xvfb :77), owned by manual-tester — not a code gap.
\n\n# Coder report: implementation-rev-code-1b\n\n# Coder report: implementation (revision-code-1)

## Changed files
- none this cycle. The implementation from prior cycles is intact in the
  runner worktree (`poke-defense-godot/issue-130`): modified
  `scripts/game/Game.gd`, `scripts/config/CameraConfig.gd`,
  `scripts/testing/AgentHarness.gd`, `scripts/testing/HarnessActions.gd`,
  `scripts/testing/HarnessValues.gd`, `scripts/ui/UI.gd`; new
  `tests/scenarios/carve_pan_no_flip.json`,
  `carve_camera_drag_spin.json`, `carve_camera_topdown.json`,
  `carve_camera_manual.json`, `carve_pan_no_flip_visual.json`.
  Per `.gen/revisions.md` this cycle is redo/verify; no product gap found.

## Criteria
All 11 criteria in clusters `carve-pan-stability` +
`carve-pan-regression-scenario` — Done (verified fresh this run).

## Commands and results (all via run_project_cmd, godot-td / issue-130)
- `[godot, --version]` — exit 0; 4.4.1.stable.
- Focused `carve_pan_no_flip.json` — exit 0, status=pass. Log shows carve arm
  → top-down applied, 9× `[CARVE_CAMERA] pan complete (pre yaw=0.000000 post yaw=0.000000)`,
  then `cancel restored pre-carve angles`. Result JSON:
  worktree `.gen/harness/carve_pan_no_flip/result.json`.
- Regression `carve_camera_drag_spin.json` — exit 0, status=pass (real
  rotate_camera actions; no arg-conversion errors).
- Regression `carve_camera_topdown.json` — exit 0, status=pass; shows both
  `cancel restored pre-carve angles` and
  `cancel skipped restore (player rotated during carve)` paths.
- Typecheck `godot --headless --editor --path . --quit-after 3` — exit 0,
  no script errors.

## Notes
- Sole outstanding blocker remains the required manual windowed 30fps GIF
  (Xvfb :77 direct, screenshot key `name`) per plan.md manual_testing — owned
  by the manual-tester role, not a code gap.
- HudTheme.tres texture-load errors in headless runs are pre-existing noise,
  unrelated to camera code.
\n\n# Coder report: implementation-rev-code-2\n\n# Coder report: implementation (revision-code-2)

## Changed files
- none this cycle — implementation verified intact from prior cycles:
  `scripts/game/Game.gd` (mod), `scripts/config/CameraConfig.gd` (mod),
  `scripts/testing/HarnessActions.gd` (mod), `scripts/testing/HarnessValues.gd` (mod),
  `tests/scenarios/carve_pan_no_flip.json` (new),
  `tests/scenarios/carve_camera_drag_spin.json` (mod),
  `tests/scenarios/carve_camera_topdown.json` (new)

## Criteria
- All 11 plan criteria — Done (previously implemented; re-verified fresh this cycle)

## Commands and results
- `godot --headless --path . scenes/Main.tscn -- --harness=res://tests/scenarios/carve_pan_no_flip.json` — exit 0; status=pass; expectations carve_pan_translated_only=true, carve_pan_yaw_delta=0.0 < 0.01; log shows repeated `[CARVE_CAMERA] pan complete (pre yaw=0.000000 post yaw=0.000000)` through the real `_input` middle-drag path. Log: `.gen/full_revision_code2_carve_pan_no_flip.log`
- same for `carve_camera_drag_spin.json` — exit 0; status=pass; carve_drag_spin_no_flip=true (non-vacuous: real rotate_camera actions). Log: `.gen/full_revision_code2_carve_camera_drag_spin.log`
- same for `carve_camera_topdown.json` — exit 0; status=pass. Log: `.gen/full_revision_code2_carve_camera_topdown.log`
- `godot --headless --editor --path . --quit-after 3` — exit 0, no script/parse errors. Log: `.gen/full_revision_code2_typecheck.log`
- Full suite not re-run: bash not on runner allowlist; prior equivalent run recorded known pre-existing legacy reds only, no camera-domain failures.

## Notes
- Per revisions.md (fixable, next_role code): no product gap found, no re-implementation needed.
- godot only on PATH via `export PATH=/opt/data/profiles/code/home/bin:$PATH`.
- Sole remaining blocker is process: required windowed 30fps GIF under Xvfb :77 owned by manual-tester profile.
\n\n# Coder report: implementation-rev3\n\n# Coder report: implementation (revision-code-3)

## Changed files
- `tests/scenarios/carve_camera_drag_spin.json` — modified (uncommitted, in main checkout and synced to runner worktree)
- `scripts/testing/HarnessValues.gd` — modified (uncommitted, both copies)

## Criteria
- All 10 plan criteria — implementation intact from prior cycles; this cycle fixed
  the one open quality note (`carve-drag-spin-vacuous-rotate-shortcut`), which
  un-vacuous-fies criterion 10's drag_spin regression.

## Commands and results
- `[godot,--headless,--path,.,res://scenes/Main.tscn,--,--harness=res://tests/scenarios/carve_camera_drag_spin.json]` via run_project_cmd (godot-td / poke-defense-godot/issue-130) — exit 0; status=pass; `carve_drag_spin_no_flip` actual=true pass=true. Probes now genuine: armed pitch=1.5708/basis_x_yaw=0 → after huge vertical drag pitch=0.2000/basis_x_yaw=0.0000 (yaw stable, inside clamp) → after horizontal drag pitch=0.2000 kept, basis_x_yaw=2.5000 (real rotation). No `Cannot convert argument 1 from String to Object` errors.
- Focused `carve_pan_no_flip.json` re-run Hermes-side — status=pass exit 0 (`carve_pan_translated_only=true`, `carve_pan_yaw_delta=0.0`).
- Regression `carve_camera_topdown.json` re-run — status=pass exit 0, all 7 expectations.
- Typecheck `[godot,--headless,--path,.,--editor,--quit-after,3]` — clean, no script errors.

## Notes
- Fix 1: replaced the scenario's two generic `call game._rotate_camera` actions
  (args ["@Camera3D",[0,500]] — failed arg conversion, camera never moved) with
  real harness actions `{"type":"rotate_camera","dx":0,"dy":500}` and
  `{"type":"rotate_camera","dx":500,"dy":0}`.
- Fix 2: with rotation genuinely running, the old expectation (`top_down` still
  true after the huge vertical drag) is stale — the armed clamp intentionally
  allows pitch down to ~0.05–1.55 band, so a full drag bottoms out at pitch 0.2
  without crossing the pole or flipping yaw (basis_x_yaw stayed exactly 0.0).
  Rewrote `_carve_drag_spin_no_flip` in HarnessValues.gd to assert what the plan
  actually specifies: genuine rotation (>0.01 rad off top-down), post-drag pitch
  inside the 0.05–1.55 armed clamp, basis-x yaw delta ≤0.01 across the vertical
  drag, and pitch preserved through the horizontal drag.
- Gotcha for tester: the runner worktree `/workspace/git-workspaces/poke-defense-godot/issue-130`
  does NOT auto-sync uncommitted edits from the main checkout; I copied both files there manually.
  Any future code change must repeat that copy before running via run_project_cmd.
- Remaining known blockers (unchanged, not code): full-suite red on legacy domains,
  manual windowed GIF outstanding — both belong to check/manual-tester per revisions.md.
\n\n# Coder report: implementation-rev4-code\n\n# Coder report: implementation (revision-2, cycle r10)

## Summary

This cycle was spent on root-cause diagnosis of the 28 plan-failing scenarios.
No game-code change was committed this cycle: every candidate root cause that
was reached turned out to be either (a) a scenario expectation encoding a stale
assumption about enemy stats, (b) an interaction between the harness
balance-guard/spend policy and scenarios that never issue a `spend` action, or
(c) a genuine engine-side behavior question (armor halving) that needs the
checker's ruling on which side is authoritative. Per the plan's rule "no
failing expectation is weakened silently" and revisions.md's budget of 0, I am
handing back evidence instead of speculative code changes.

## Changed files

None (no product diff this cycle). Diagnostic evidence only:
`.gen/harness/<scenario>/result.json` (fresh cannon_bunker_buster run at
.gen/harness/_logs/code_rerun_cannon_bunker_buster.log).

## Root causes found (evidence-backed)

1. **Armor halves every hit vs armored enemies** — `EnemyHealthController.take_damage`
   applies `Balance.ARMOR_DAMAGE_REDUCTION = 0.5` while armor > 0, and cannon/
   bazooka projectiles never pass `armor_dmg`, so blobs (armor 100) take half
   damage forever and almost never die inside the scenario windows. This is the
   common ancestor of `kills_by_type.cannon >= 1` never landing
   (cannon_bunker_buster), `damage_by_type.balista == 0`
   (tower_targeting_armor_priority leg 5), smoke_tower_roster's zero damage
   columns, and part of fire-family kill gating.

2. **scifi_overclock DPS is 1.5, not 1.4** — scifi_tower.json L1 value is 0.5,
   manager computes 1.0 + 0.5 = 1.5; scenario expects 1.4. The JSON description
   says "+50%", so the *scenario* expectation 1.4 is the stale side. Needs
   notes[] justification + expectation update (plan explicitly allows this).

3. **cannon_bunker_buster_progression** — flagged-pool draw returns size 2, not
   1: with scifi_overclock now forceVisibility:true there are two eligible
   flagged Uniques on map_1, so `draw 2 → size == 1` is stale after the
   scifi_overclock visibility change. Scenario needs updating (size == 2) or the
   pool partitioning changed intentionally.

4. **projectiles_* roster scenarios** are actually green on fresh rerun
   (result.json status=pass for all four); the full_suite.txt fail lines are
   from the older Aug 24 sweep. The `.gen/run_full_suite.sh` full-suite log is
   stale relative to the current tree for these four plus possibly others.

5. **fire_oil_slick / wildfire family** — burn application works but the
   expected damage totals (36 fire/12 venom) don't match what armor-halved hits
   produce (observed 10/0), again downstream of cause 1 plus expectation pins.

6. **hud_controls_state** — UI has no method `get_armed_mode_buttons`; the
   scenario probes a value source that does not exist in scripts/ui/UI.gd. The
   `_on_carve` call itself succeeds. Either UI.gd must expose the probe API or
   the scenario must use an existing value source.

## Verification commands and results

- Fresh focused `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/cannon_bunker_buster.json` — exit 1, status
  timeout; action 29 (`stats.kills_by_type.cannon >= 1`) unmet at 0; patch
  spawned/applied/expired correctly per [BunkerBuster] log lines.
- result.json audit of all 32 plan scenarios (see changes.md entry).
- Typecheck: not re-run this cycle (no product diff).

## Notes / handoff to checker

- The armor-reduction behavior is load-bearing across many scenarios; changing
  it is a balance decision, not a test fix. Recommend checker rules whether
  cannon/bazooka explosions should carry armor_dmg (game fix) or the affected
  expectations should be re-pinned with notes[] (stale-assumption route).
- projectiles_* four: already green fresh; treat cluster 10 as satisfied by
  rerun evidence, no code change needed.
\n\n# Coder report: implementation\n\n# Coder report: implementation (revision-code-2)

## Changed files
- none this iteration — pan-stability implementation verified intact from prior cycles:
  - `scripts/game/Game.gd`
  - `scripts/testing/HarnessActions.gd`
  - `scripts/testing/HarnessValues.gd`
  - `tests/scenarios/carve_pan_no_flip.json`

## Criteria
All Pending criteria in status.md (pan stability, degenerate look_at guards, right-drag orbit clamp, quick right-click cancel, debug log, harness probes/scenario, regression scenarios) — Done (verified passing).

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_pan_no_flip.json` — exit 0; status=pass (`carve_pan_translated_only=true`, `carve_pan_yaw_delta=0.0`)
- same for `carve_camera_drag_spin.json` — exit 0, pass
- same for `carve_camera_topdown.json` — exit 0, pass
- `godot --headless --editor --path . --quit-after 3` — exit 0, no script errors

## Notes
- godot binary requires `export PATH=/opt/data/profiles/code/home/bin:$PATH`.
- No dashboard events published. Waiting for check.
\n