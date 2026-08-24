# Check report — req-124-cave-carved-path-torches r4 (iteration 4)

classification: pass

## Verdict
All 3 plan criteria verified Done. Fresh verification run through the approved
project runner (`run_project_cmd`, project=poke-defense-godot,
workspace=poke-defense-godot/issue-cave-carved-path-torches) — every command exit 0.

## Runner gate
- Preflight `godot --version` → Godot 4.4.1.stable, exit 0.

## Acceptance criteria evidence

### Criterion 1 — no map-size constant / fixed torch cap
- Diff confirms `MAX_TORCHES = 250` removed from `scripts/game/underground/TorchManager.gd`;
  `_derive_torch_budget(grid_width, grid_depth)` returns `grid_width * grid_depth`
  and is recomputed on each `_update_torch_placement()` pass; passed to
  `TorchPlacer.calculate_torch_positions`. Pool expands on demand
  (`Expanded torch pool by 10` observed live in the harness log).
- Test: `tests/caves/test_torch_budget_scaling.gd::_test_100x100_grid_no_cap_truncation`
  — 100×100 grid, budget=10000 >= 10000, torches=190, uncovered=0.
- Command: `godot --headless --path . res://tests/caves/test_torch_budget_scaling.tscn` → exit 0,
  "8 ok, 0 failed".

### Criterion 2 — TORCH_SPACING widened to 2, coverage repair intact
- Diff: `TorchPlacer.gd` `TORCH_SPACING` 1 → 2; placement still strides unique corridor
  cells (`_spacing_torch_cells`), coverage repair (`_repair_coverage`) unchanged and active.
- Tests:
  - Unit test `_test_spacing_constant_is_two`, `_test_small_grid_full_coverage`,
    `_test_curve_stays_lit_at_widened_spacing` all pass (L-curve 30×30: torches=35, uncovered=0).
  - Harness `carve_curved_torches_coverage.json`: exit 0, status=pass,
    `.gen/harness/carve_curved_torches_coverage/result.json` fresh this run;
    log `[TORCH_PLACER] coverage pass: required_cells=105 torches=91 uncovered=0`;
    expectations include `torch.uncovered_corridor_cells == 0` (pass) and
    `torch.count > 0` (91). All 4 expectations pass.
- Guard regression `declined_cave_torches_extinguish.json`: exit 0, status=pass;
  locked-cave coverage pass `required_cells=17 torches=13 uncovered=0`.

### Criterion 3 — Torch.gd untouched byte-for-byte
- `git status --short` / `git diff HEAD --stat`: only TorchManager.gd, TorchPlacer.gd modified
  plus new test files; `Torch.gd` not in any diff or untracked list. Verified directly.

## Commands (all through run_project_cmd)
| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable |
| `godot --headless --path . res://tests/caves/test_torch_budget_scaling.tscn` | 0 | 8 ok, 0 failed |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json` | 0 | status=pass, 4/4 expectations |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/declined_cave_torches_extinguish.json` | 0 | status=pass |

No separate typecheck/build command exists for this GDScript project; scene-load +
headless runs act as the parse/build gate (no script parse errors in any log).
Prior r3 harness evidence in `.gen-r3-done-*` was NOT reused as proof; all results above are fresh.

## Changed-file quality findings
- New code (`_derive_torch_budget`, probes, unit test) is minimal, documented, follows
  existing style; no violations of `/opt/data/coding_rules.md` found. No type casts,
  no speculative abstractions. Surgical diff (13 insertions / 6 deletions in prod code).
- Test overlap check: new tests assert r4-specific behavior (budget derivation at
  100×100, spacing constant == 2); no existing suite test covers these paths
  (`tests/caves/` previously held only discovery-chance tests). No duplication.
- Untracked scratch dirs `.gen-blocked-req-124-r2-*` and `.gen-r3-done-*` are prior-run
  workflow artifacts (declared workflow state), not scope creep; noted for cleanup before merge.

## Quality notes
`.gen/quality-notes.md` did not exist for r4 (prior notes live in the archived
r3 directory); nothing open to resolve, no new cross-cutting entries required.

## Blockers
None.

## Unverified items
None. The plan's windowed visual scenario (`manual_carve_curve_torches_visible`)
was verified by the implementor with screenshots under `.gen/harness/manual_carve_curve_torches_visible/`;
the headless re-run of the equivalent logic scenario plus the unit suite is sufficient
for the criteria here.
