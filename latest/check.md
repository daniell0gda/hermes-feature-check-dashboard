# Check Report — req-124-cave-carved-path-torches-r3 (revision-check-1)

classification: pass

## Verdict

The revision fixed the previously red full-suite gate at its root cause (incidental
cave discoveries from recently-merged discovery-reliability changes sealing the test
corridors / capping carved tiles), via a new deterministic `cave_discovery_override`
harness action — production code untouched by that fix. All nine acceptance criteria
now have fresh passing evidence from this independent verification run. Torch.gd
remains byte-for-byte unchanged (hard constraint honoured).

## Commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-cave-carved-path-torches)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight probe | godot --version | 0 | 4.4.1.stable |
| Typecheck/build | godot --headless --path . --editor --quit-after 300 | 0 | No parse errors; TorchManager/TorchPlacer/HarnessActions/HarnessValues registered |
| Focused harness | godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/carve_curved_torches_coverage.json | 0 | status=pass; `[TORCH_PLACER] coverage pass: required_cells=105 torches=42 uncovered=0`; `[TorchManager] Updated torches: 42 active`; fresh result.json written |
| Full suite | each of 9 scenarios run individually through the runner | 0 ×9 | All PASS (see below) |

Note: the plan's `bash -lc` full-suite one-liner is not allowlisted on the runner
profile (`cmd executable is not allowed`); equivalent coverage achieved by running
every scenario individually through allowlisted `godot` invocations.

Per-scenario results (this check, fresh runs):
1. carve_stops_at_discovered_cave PASS (0)
2. carve_curved_torches_coverage PASS (0) — coverage log uncovered=0
3. cave_decline_seals_reveal_unseals PASS (0) — was timeout pre-fix
4. cave_pending_seals_entrance_instantly PASS (0) — was timeout pre-fix;
   observed `[TORCH_PLACER] coverage pass: required_cells=36/54 ... uncovered=0`
5. cave_discovery_long_carve PASS (0) — was FAIL 961<1000 pre-fix
6. cave_discovery_chance PASS (0)
7. cave_discovery_pending_placement PASS (0)
8. cave_reveal_only_unseals_carved_blocks PASS (0)
9. declined_cave_torches_extinguish PASS (0) — `coverage pass: required_cells=17 torches=8 uncovered=0`

Fresh `.gen/harness/carve_curved_torches_coverage/result.json`: status "pass",
all expectations passed (verified programmatically this run).

## Acceptance criteria evidence (plan wording preserved)

Cluster 1 — curved-path torch placement coverage
1. L-shaped corridor fully lit — focused harness recomputes coverage independently
   from live voxel_grid/cave_locked_grid in HarnessValues; uncovered_corridor_cells=0.
2. Straight-corridor regression — declined_cave_torches_extinguish and other
   straight-corridor scenarios pass with unchanged spacing behaviour.
3. Interior exemption / bend cells lit — implemented in TorchPlacer `_required_wall_cells`
   (`_is_corridor_cell`); harness mirrors independently; uncovered=0.
4. No torches in cave_locked_grid cells — locked-cell exclusion exercised by
   declined/pending/seals scenarios (all pass).
5. MAX_TORCHES cap thinned not abandoned — 42 active ≤ cap on large carve with
   uncovered=0 after `_thin_by_coverage`.
6. Debug [TORCH_PLACER] log line — observed live: event name, carved-cell count,
   torch count, uncovered count.

Cluster 2 — curved-torch harness verification scenario
7. Zero carved cells beyond one light radius asserted — expectation present and
   passing in result.json.
8. Torch count > 0 and update ran through [TorchManager] — count=42 > 0;
   `[TorchManager] Updated torches: 42 active` logged.
9. Exit 0 + fresh result.json status "pass" — verified this run.

No criterion demoted. No new-test overlap found: no existing scenario asserted torch
coverage on a bent path before `carve_curved_torches_coverage.json`.

## Changed-file quality findings

- scripts/game/underground/TorchPlacer.gd — typed GDScript, guard clauses, small
  functions, debug log per state transition per project CLAUDE.md rules. Advisory:
  O(torches×cells) repeated scans (quality-notes.md, acceptable at grid size).
- scripts/game/underground/TorchManager.gd — surgical cap plumbing; clean.
- scripts/testing/HarnessActions.gd — new `cave_discovery_override` action is a
  minimal test-only hook; no production behaviour changed. Clean.
- scripts/testing/HarnessValues.gd — independent coverage recomputation (genuine
  cross-check). Clean.
- tests/scenarios/*.json changes — fixture determinism only. Clean.
- scripts/game/underground/Torch.gd — git diff empty; light settings unchanged
  byte-for-byte (hard constraint honoured).

## Blockers

None. Runner reachable and healthy; every project command ran through run_project_cmd.

## Unverified items

- Manual testing (windowed screenshots, top-down underground view after a
  curve-including side-to-side carve): `.gen/manual-report.md` absent — owned by the
  manual-tester profile, outstanding for issue sign-off (not a plan criterion).
