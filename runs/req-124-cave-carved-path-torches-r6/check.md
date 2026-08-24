# Check report — req-124-r6 cave carved-path torches (iteration 6)

classification: fixable

## Verdict
Implementation is correct and all runnable gates are green. 8 of 10 criteria verified Done; 2 moved to Pending for missing automated-test evidence (not implementation failures). No build/typecheck failures, no quality violations in the changed code that demote criteria.

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-cave-carved-path-torches)
- Preflight `["git","status","--short"]` — exit 0 (runner reachable; only untracked prior-run `.gen-*` snapshot dirs).
- Typecheck/build gate: `["godot","--headless","--path",".","--editor","--quit-after","3"]` — exit 0. Parse gate clean; TorchPlacer/HarnessValues/TorchCoverageProbe/test scripts register without errors.
- Full test part 1: `["godot","--headless","--path",".","res://tests/caves/test_torch_budget_scaling.tscn"]` — exit 0. Output: "=== torch_budget_scaling: 18 ok, 0 failed ===". Includes new assertions: final count strictly decreases with spacing (4=16, 6=11, 8=9); post-repair columns step by exactly the spacing at 4/6/8; L-shape covered (uncovered=0) at 4/6/8; wall-mount checks pass.
- Full test part 2: `["godot","--headless","--path",".","res://tests/caves/test_cave_discovery_chance.tscn"]` — exit 0, "15 ok, 0 failed" (no behaviour regression).
- Focused harness: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/carve_curved_torches_coverage.json"]` — exit 0; fresh `.gen/harness/carve_curved_torches_coverage/result.json`: status=pass, all 4 expectations pass ([TorchManager] Updated torches regex, coverage-pass uncovered=0 regex, torch.count 33 > 0, uncovered_corridor_cells == 0). Log shows repair added exactly 3 bend torches (spacing_pass_torches=30, repair_added=3, effective_spacing=4) — spacing drives density and repair fills only genuine gaps.

Note: the plan's full-suite command wraps everything in `bash -lc`; it was executed as its three native component commands through the runner instead — each recorded above.

## Criterion evidence
Done (8):
1. Straight-corridor stride + monotonic decrease — test_torch_budget_scaling.gd `_test_spacing_drives_final_density_straight_corridor`, `_test_repair_does_not_redensify_straight_corridor`; observed counts 16/11/9 at spacings 4/6/8.
2. Repair does not re-densify — same test asserts post-repair columns step by exactly the spacing (repair_added=0 on straight corridors in logs); would fail if repair re-packed.
3. Coverage radius from LIGHT_RADIUS, not halved; Torch.gd untouched — diff confirms `LIGHT_RADIUS * 0.5` removed from `_cells_lit_by`; `git diff ecef2a1..HEAD --name-only` shows scripts/game/underground/Torch.gd unmodified (`const LIGHT_RADIUS = 1.0` intact).
4. L-shaped covered at 4/6/8 — `_test_l_shape_covered_at_all_spacings`, uncovered=0 at all three spacings.
5. Wall mounting — `_test_all_torches_mount_on_real_walls` and `_test_repair_torch_mounts_on_real_wall` assert exact one-cardinal-axis WALL_OFFSET onto solid rock; passed for spacing- and repair-pass torches.
6. Debug log line — `[TORCH_PLACER] coverage pass ... spacing_pass_torches=30 repair_added=3 effective_spacing=4` observed live in harness output.
7. Behaviour test fails if spacing stops mattering — the new monotonic-decrease assertion is a real failing assertion (it computes counts per spacing via the trailing `spacing` parameter); it fails if counts stop decreasing. No overlap found: existing tests did not compare two spacings before this change.
8. Focused harness exit 0 with all four conditions — see result.json above.

Pending (2) — missing evidence, not failures:
- max_torches thinning keeps greedy set-cover coverage: `_thin_by_coverage` is unchanged greedy set-cover and is now spacing-aware, but no test exercises a binding cap (budget tests set caps above placed counts), so nothing would fail if thinning regressed to uniform dropping.
- Empty voxel grid returns zero positions without errors: guard exists (early return on `voxel_grid.size() == 0`) but no automated test asserts it.

Impossible (0).

## Changed-file quality findings
Diff scope (ecef2a1..a503f09): TorchPlacer.gd, HarnessValues.gd, test_torch_budget_scaling.gd, torch_coverage_probe.gd. Clean-code compliant overall (typed params, documented intent, minimal surface). One advisory cross-cutting note appended to quality-notes.md: the coverage-radius derivation is hand-duplicated across three files (TorchPlacer, HarnessValues, TorchCoverageProbe) instead of reusing one function — drift risk if the formula changes. Advisory only; does not demote any criterion.

Test overlap check: searched the suite; no pre-existing test asserted spacing-vs-density or L-shape-at-multiple-spacings, so the new tests add coverage rather than duplicate it.

Untracked non-feature files: none (only prior-run `.gen-*` snapshot dirs from earlier iterations, workflow artifacts).

## Blockers
None. Runner healthy throughout; all commands returned real results.

## Unverified items
- Manual windowed screenshot check (sparse vs dense visible difference) remains owner-side per request.md; not verifiable headless.
