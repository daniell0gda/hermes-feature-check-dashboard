# Check report — issue-cave-carved-path-torches (iteration 2)

Classification: **fixable**

## Verdict

The focused torch scenario passes cleanly, but the plan's full-test command fails:
`cave_pending_seals_entrance_instantly.json` exits 1 (harness `status=timeout`,
unmet `underground.has_route_from == true` at action_index 4). `.gen/request.md`
explicitly makes fixing this regression required scope for this pass ("This is now
required work, not an acceptable blocker"), and the implementor did not fix it —
only reproduced it on clean HEAD and documented it. Build/test gate therefore
fails; all previously-Done criteria are demoted to Pending per gate policy.

## Commands executed (all through run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-cave-carved-path-torches)

| Command | Exit | Result |
|---|---|---|
| `git status --short` | 0 | feature diff present (5 mod files + new scenario json) |
| `godot --headless --path . --editor --quit-after 300` | 0 | clean import; Torch/TorchManager/TorchPlacer/HarnessValues classes registered |
| focused `cave_carved_path_torches.json` | 0 | `[Harness] status=pass exit=0`; result.json 7/7 expectations pass |
| `declined_cave_torches_extinguish.json` | 0 | `[Harness] status=pass exit=0` |
| `cave_pending_seals_entrance_instantly.json` | **1** | `status=timeout`, `has_route_from actual=false` at action 4 |

Fresh results: `.gen/harness/{cave_carved_path_torches,declined_cave_torches_extinguish,cave_pending_seals_entrance_instantly}/result.json`
(finished_at 2026-08-22T09:55–09:56). Raw stdout scanned: no SCRIPT ERROR or GDScript
parse errors in feature code. Pre-existing HudTheme.tres missing-texture errors
(`res://textures/ui/hud/wood_panel.png`) spam every run — already recorded in
quality-notes as legacy, unchanged.

## Criterion evidence

Focused scenario proves most criteria individually (7/7 expectations green):
20 `count_near >= 1` samples at ±2..±8 units along all four cross arms (radius 2.5);
pending cave 9101 `count_in_cave == 0` before confirm; declined caves 9102/9103
zero interior torches incl. carved-path overlap; `unlit_carved_in_cave == 0`;
fresh stdout shows `[TORCH] cave-path update active=29→148→154→142` per recompute
(initial vs incremental carve distinguishable by carve events preceding each update).
These stay demoted only because the global full-suite gate failed — they are
expected to be re-promoted once the seals regression is fixed.

Not satisfied regardless of gate: windowed-run screenshot PNGs — headless runs
skip screenshots (`outcome: skipped, reason: headless`); manual tester owns visual
evidence. No fresh windowed PNGs exist.

## Root cause of remaining failure

map_9 discovery chance 0.8 + scenario seed 1 lets an RNG-discovered dangerous cave
(carve of 49 tiles, content 'enemies') fire during/immediately after the scenario's
own `carve_rectangle` (33 tiles), locking cells over the hole↔exit corridor before
the `has_route_from == true` assertion — see
`.gen/harness/_logs/cave_pending_seals_entrance_instantly.out.log` lines ~438–447
(`Carved 49 tiles ... caves_found=1 result=true`). Required fix per request.md:
scope cave discovery/RNG for this scenario deterministically (e.g. fixture-level
discovery override), without weakening assertions. Files outside the current
cluster ownership (`CaveSystem.gd` / scenario fixture).

## Changed-file quality findings

Feature diff reviewed against coding rules: no demoting violations in new code.
Advisory items already recorded in quality-notes.md (triple-duplicated XZ-distance
helper; HudTheme legacy breakage). No new quality notes appended this iteration;
the two open entries remain unresolved and are restated below for the leader.

## Blockers

None infrastructural — runner healthy throughout (all probes and commands returned).
Remaining blocker is product/test-fixture work: make `cave_pending_seals_entrance_instantly`
deterministic, then rerun the full command set and the windowed manual-tester pass.

## Unverified / handoff

- Manual tester must produce fresh windowed top-down PNGs (cross lit end-to-end,
  declined caves dark) and inspect pixels; headless evidence does not satisfy that
  criterion.
- Re-check required after the seals-regression fix before any criterion returns to Done.
