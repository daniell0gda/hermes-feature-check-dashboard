# Check Report — req-124-cave-carved-path-torches-r3 (iteration 3)

classification: fixable

## Verdict

Implementation of curved/bent carved-path torch coverage is real and verified by a
fresh, independent-assertion headless harness run. The focused scenario passes;
the build/parse gate passes. However the full-suite gate is red: three cave
scenarios fail identically on the stashed baseline (pre-existing, not caused by
this change), so per the gate rules no item may remain Done while the full suite
is not green — even though the failures are unrelated to the torch work.
Additionally the required manual-testing evidence has not been produced yet.

## Commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-cave-carved-path-torches)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Typecheck/build | godot --headless --path . --editor --quit-after 300 | 0 | No parse errors; TorchManager/TorchPlacer/HarnessValues registered |
| Focused harness | godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json | 0 | status=pass; log `[TORCH_PLACER] coverage pass: required_cells=105 torches=42 uncovered=0`, `[TorchManager] Updated torches: 42 active`; fresh `.gen/harness/carve_curved_torches_coverage/result.json` |
| Full suite (per-scenario) | 9 scenarios individually through the runner | see below | 6 pass, 1 fail, 2 timeout |

Per-scenario results (this check):
- carve_stops_at_discovered_cave PASS (0)
- carve_curved_torches_coverage PASS (0)
- cave_discovery_chance PASS (0)
- cave_discovery_pending_placement PASS (0)
- cave_reveal_only_unseals_carved_blocks PASS (0)
- declined_cave_torches_extinguish PASS (0) — coverage pass logged: required=17 torches=8 uncovered=0
- cave_discovery_long_carve FAIL (1): carved_tiles 961 < 1000
- cave_decline_seals_reveal_unseals TIMEOUT (harness wait: underground.has_route_from == true never satisfied)
- cave_pending_seals_entrance_instantly TIMEOUT (same has_route_from wait)

Note: plan's full-suite one-liner uses `bash -lc`, which the runner profile does
not allow (`cmd executable is not allowed by the project profile`); scenarios were
run individually via allowlisted `godot` invocations — same coverage.

## Baseline isolation

With all feature changes `git stash`ed and fresh baseline runs through the
runner, the same three scenarios fail identically (long_carve FAIL 961<1000;
both timeouts on has_route_from). The failures are pre-existing and unrelated to
torch placement. Changes were restored with `git stash pop` and confirmed intact.

## Acceptance criteria status (plan wording preserved)

Cluster 1 — curved-path torch placement coverage
1. L-shaped corridor fully lit within light radius — implementation verified by
   focused harness (independent recomputation in HarnessValues), but full-suite
   gate red → moved Pending (gate rule, not an implementation failure).
2. Straight-corridor spacing regression — `_spacing_torch_cells` preserves legacy
   wall-spacing logic; declined_cave_torches_extinguish + carve_stops_at_discovered_cave
   pass → Pending under the same full-suite gate rule.
3. Interior exemption / bend cells lit — implemented in `_required_wall_cells`
   (`_is_corridor_cell`); harness mirrors it independently → Pending (gate).
4. No torches in cave_locked_grid cells — locked-cell exclusion reused in both
   passes; declined_cave_torches_extinguish passes → Pending (gate).
5. MAX_TORCHES cap thinned by coverage not abandoned — `_thin_by_coverage` greedy
   set cover; 42 active ≤ cap observed in focused run → Pending (gate).
6. Debug [TORCH_PLACER] coverage-pass log line — observed in live output:
   `[TORCH_PLACER] coverage pass: required_cells=105 torches=42 uncovered=0` → Pending (gate).

Cluster 2 — curved-torch harness verification scenario
7. Harness asserts zero carved cells beyond one light radius — result.json shows
   torch.uncovered_corridor_cells == 0 among passing expectations → Pending (gate).
8. Harness asserts torch count > 0 and update ran through [TorchManager] —
   torch.count=42 > 0, `[TorchManager] Updated torches: 42 active` in log → Pending (gate).
9. Exit code 0 + fresh `.gen/harness/carve_curved_torches_coverage/result.json`
   status "pass" — verified this run, exit 0, status=pass → Pending (gate).

All nine criteria move to Pending solely because the full-suite gate fails
(pre-existing baseline failures). None show an implementation defect in this diff.
Hard constraint honoured: scripts/game/underground/Torch.gd untouched (git diff empty);
light intensity/energy/radius/color unchanged.

## Changed-file quality findings

- scripts/game/underground/TorchPlacer.gd — clean typed GDScript, guard clauses,
  small focused functions, debug log per state transition per project rules.
  Advisory: O(torches×cells) repeated distance scans in coverage/thin/count
  helpers (see quality-notes.md; acceptable at current grid size).
- scripts/game/underground/TorchManager.gd — surgical: cap passed into placer,
  removed blind optimize_torch_placement call. Clean.
- scripts/testing/HarnessValues.gd — new `torch.count` /
  `torch.uncovered_corridor_cells` fields recompute coverage independently from
  live voxel_grid/cave_locked_grid (genuine cross-check, not trusting placer
  output). Clean.
- tests/scenarios/carve_curved_torches_coverage.json — new L-shaped side-to-side
  carve geometry with pre/post torch-count waits. No overlap found with existing
  scenarios (none assert torch coverage on a bent path before this).

## Blockers

None infra-related. Runner reachable and healthy throughout; every command ran
through run_project_cmd.

## Unverified items

- Manual testing (windowed screenshots, top-down underground after a curve-including
  side-to-side carve, camera aimed at camera_target) — `.gen/manual-report.md` absent.
  Owned by manual-tester profile; still outstanding for issue sign-off.
