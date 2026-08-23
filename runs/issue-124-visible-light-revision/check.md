# Check report — issue-cave-carved-path-torches (iteration 5)

classification: fixable

## Verdict

All build/test gates pass through fresh `run_project_cmd` evidence this iteration.
Editor/import gate exit 0. Focused `cave_carved_path_torches` scenario: exit 0,
`status=pass`, 56/56 actions ok, 7/7 expectations pass, including 20 `count_near >= 1`
samples (~every 2 units along all four cross arms plus the exit corridor) and
`unlit_carved_in_cave == 0`. Full-suite scenarios `declined_cave_torches_extinguish`
and `cave_pending_seals_entrance_instantly` both exit 0 with `status=pass`; the
sealing-regression scenario was run three consecutive times by the checker and passed
identically each time (deterministic), with `[CAVE] discovery suppressed: harness
scenario forbids RNG cave discovery` logged on every carve event and only fixture
cave 9003 present. Raw stdout/stderr scanned independently of harness status: no
game-script `SCRIPT ERROR`, no GDScript parse errors outside the pre-existing
HudTheme.tres missing-texture noise (`wood_panel.png` etc., identical on clean HEAD,
already recorded in quality-notes.md as legacy); exit-time dummy-renderer leak warnings
are engine-shutdown noise per plan.

13 of 15 criteria are Done. One criterion is Pending: the `[TORCH]` debug log prints
the active torch count per recompute but does not name the trigger kind (initial
placement vs incremental carve), so that criterion's wording is not fully met.

## Commands executed (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-cave-carved-path-torches)

| Command | Exit | Result |
|---|---|---|
| `git status --short` | 0 | 9 modified files + new `tests/scenarios/cave_carved_path_torches.json` |
| `godot --headless --path . --editor --quit-after 300` | 0 | clean import, no script errors |
| `--harness=res://tests/scenarios/cave_carved_path_torches.json` | 0 | `status=pass`, 56 actions, 7/7 expectations pass |
| `--harness=res://tests/scenarios/declined_cave_torches_extinguish.json` | 0 | `status=pass`, 0 failed actions |
| `--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json` run 1 | 0 | `status=pass`, 2/2 expectations pass |
| same, run 2 (consecutive) | 0 | identical pass |
| same, run 3 (consecutive) | 0 | identical pass |

Fresh result JSONs written this iteration under `.gen/harness/<scenario>/result.json`.

## Criterion evidence

Cluster 1 (torch coverage): scenario timeline contains 20 `count_near >= 1`
wait_for_conditions at [x,-3,z] / [0,-3,z] samples ±2..±8 every ~2 units plus
[1.5/3.5/5.5,-3,-8] on the new corridor, radius 2.5 = Torch.LIGHT_RADIUS; all green
in result.json. Torch pool expands on demand (`[TorchManager] Expanded torch pool by
10`, up to 154 active); `unlit_carved_in_cave == 0` passes (TorchManager.count_unlit_carved_cells).
Incremental corridor carve triggers a recompute placing torches along its whole length
(active 148 → 154).

Cluster 2 (dark pending/declined caves): pending cave 9102 reports `count_in_cave == 0`;
declined caves 9102/9103 (and 9101 while pending in the focused scenario) report zero
interior torches including carved-path overlap cells.

Cluster 3 (harness/evidence): focused scenario green with full-arm sampling. Windowed
manual-tester evidence exists: `.gen/screenshots/*.png` (timestamps 08-23 09:20, current)
plus `.gen/manual-report.md` (PASSED, windowed gl_compatibility llvmpipe run, 56/56
actions). Checker independently pixel-inspected the fresh final PNG
(`open_cave_no_dark_corridor.png`): carved cross arms and connecting corridor show warm
torch-lit floors end to end; west and south arms lit; dark regions are solid uncarved
rock and sealed cave interiors only. An intermediate mid-run shot
(`dungeon_cross_carve_lit.png`) shows two arms before the later torch recompute landed;
the final-state screenshot satisfies the end-to-end visible-lighting criterion.

Cluster 4 (sealing regression determinism): three consecutive deterministic checker
passes; sequence per run: initial route found (distance 8.0, 17 waypoints) → confirmation
presented with route false (`No valid path found to any exit`, physically sealed) → 1 s
later `[TORCH] cave-path update active=31`, interior dark → confirm yes → exact route
restored (8.0, 17 waypoints) with lighting restored (active=50). Scenario JSON diff is
exactly one added line (`"suppress_rng_cave_discovery": true`); no assertions, waits,
or thresholds changed (verified via git diff). Suppression honored at every carve
cooldown and the save-time flush (`CaveSystem.prepare_for_save` gated by
`_rng_discovery_suppressed()`).

## Changed-file quality findings

Feature diff reviewed against `/opt/data/coding_rules.md` and worktree CLAUDE.md: no
demoting violations in new code. Suppression flag is opt-in, minimal, documented;
gap-fill coverage logic reuses existing helpers; harness fields documented in
HarnessValues header comments. Advisory items already open in quality-notes.md
(duplicated XZ-distance helper x3, HudTheme legacy breakage) remain unresolved and
advisory-only; no new quality notes appended. Test overlap: no new overlapping test;
`cave_carved_path_torches.json` is the plan-mandated scenario.

## Blockers

None infrastructural. Runner healthy throughout; every command ran through
run_project_cmd.

## Unverified / handoff

- Pending: extend the `[TORCH]` debug log to name the trigger kind (initial placement
  vs incremental carve) alongside the placed-torch count, per the plan criterion
  wording.
