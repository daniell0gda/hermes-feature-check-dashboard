# Check report: grass-grid-misses-far-edges (iteration 1)

classification: fixable

## Verdict

All six acceptance criteria are verified Done by fresh runner execution. One advisory
quality note was appended; it does not demote any criterion. No build/test failures.
Classification is `fixable` only because of that single advisory cleanup item (a stray
duplicate comment block); functionally the work is green.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-grass-grid-misses-far-edges)

- Typecheck/build: `godot --headless --editor --quit-after 2 --path .` — exit code 0, no parse errors.
- Full test (import): `godot --headless --import --path .` — exit code 0.
- Focused: `godot --headless --path . res://tests/visuals/test_nature_visibility_range.tscn`
  — exit code 0, prints `=== nature_visibility_range: 16 ok, 0 failed ===`.
  Fresh [NATURE] debug line observed twice (once per path):
  `[NATURE] grass grid sampling: 289 base positions, min/max X (-24.00, 24.00), min/max Z (-24.00, 24.00)`.
  Edge-band checks pass on both paths: grid extent x [-24.48, 24.50], z [-24.47, 24.50]
  (4079 placed instances read from real node world transforms); multimesh extent x/z [-24, 24].
  Pre-existing runner exit-leak warnings (dummy renderer RID/ObjectDB leaks) appear after
  the summary line and are unrelated to this change.

## Criterion evidence

1. Full-extent sampling — `NatureDecoration.gd:_sample_grass_grid_base_positions()` walks a
   float while-loop over [-half+margin, half-margin] inclusive on both axes with epsilon
   clamp; sampler output spans exactly [-24, 24] on a 50x50 map (focused run). PASSING TEST:
   `_test_per_instance_grass_reaches_all_four_edges` / `_assert_grass_reaches_all_edges`
   would fail if any +X/+Z band were unsampled (the old `range()` bound produced max ≈ +19).
2. Shared helper — both `_generate_grass_groups` and the multimesh grid branch call the same
   `_sample_grass_grid_base_positions()`; the duplicated range() walks at :222-223/:649-650
   are deleted. Verified by diff inspection plus both-path tests exercising the helper.
3. 50x50 four-edge coverage — asserted numerically by the focused test on both paths; limits
   ±22 with observed extents reaching ±24.x / ±24.5.
4. Debug log — `[NATURE]` line emitted inside the helper under `OS.is_debug_build()`,
   once per generation pass, with count + min/max X/Z; seen verbatim in fresh runner output.
5. Numeric extent assertion — new `_test_*_reaches_all_four_edges` tests assert min/max within
   one grid step (tolerance = GRID_SIZE = 3.0) of all four edges on both paths and fail via
   `_check` if a band is empty. No overlap with pre-existing cull tests (different behavior).
6. Existing assertions still pass — both original no-distance-cull tests green (grid: 4106
   instances, culled: []; multimesh: culled: []), 4 of 4 tests completed, 16 ok / 0 failed.

## Changed-file quality findings

- scripts/game/NatureDecoration.gd: clean typed helper, guard clause, debug-only logging
  per CLAUDE.md conventions; duplication removed as required. One cosmetic issue: two
  adjacent doc comments above `last_grass_base_positions` (one stale "debug logging" wording)
  — see quality-notes.md.
- tests/visuals/test_nature_visibility_range.gd: new tests assert real numeric criteria;
  documented headless-MultiMesh limitation (dummy renderer drops instance buffers) is handled
  honestly by checking sampler base positions on the multimesh path while reading real node
  transforms on the per-instance path.

## Blockers

None. Runner reachable and used for every project command; no host-shell Godot.

## Unverified items

None.
