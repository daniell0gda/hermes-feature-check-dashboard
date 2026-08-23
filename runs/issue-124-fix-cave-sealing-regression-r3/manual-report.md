# Manual Test Report – issue-124 cave-carved-path-torches (torch coverage along carved corridors, dark pending/declined caves)

## Summary

- Result: PASSED
- Tested on: 2026-08-23, windowed Godot 4.4.1 (gl_compatibility, Dummy audio) on the project worker
- Scenario: .gen/ui_scenario.md, driven by tests/scenarios/cave_carved_path_torches.json (windowed harness run)
- Tester: Manual-tester profile

Overall: I ran the cave_carved_path_torches scenario windowed (never headless). The harness run finished status=pass with all 56 actions ok and all 7 expectations passing, and captured three fresh top-down underground screenshots. I inspected all three PNGs visually: the carved cross and the new connecting corridor are lit end to end, and the declined/sealed cave interiors are pitch dark.

## Scenario Walkthrough

### Step 1 – Open cave lit before the cross carve

- Action: Loaded map_9, confirmed dangerous-cave fixture 9101 (decision yes), switched to the underground layer and looked straight down (top-down orthographic).
- Expected: The confirmed-open cave room is visibly lit by wall torches; surrounding solid rock stays dark.
- Observed: Cave room at top-center is clearly lit with warm torch light (portal and enemy visible inside); the rest of the map is dark solid rock. Harness confirmed torches appeared in the cave only after confirmation (count_in_cave 0 while pending → 13 after).
- Status: PASS
  ![open cave before cross carve](screenshots/open_cave_before_cross_carve.png)

### Step 2 – Carved cross fully lit end to end

- Action: Carved a 2×18 plus 18×2 cross through the open cave; waited for torch recompute ([TORCH] active=148).
- Expected: All four arms of the carved cross lit along their full length; no dark gaps inside carved corridors.
- Observed: All four arms are lit along the carved floor end to end. A first pass flagged the west arm's far end as dark, but on close inspection that dark area is solid rock beyond the corridor's carved end — the carved floor itself stays visibly lit to its westernmost cell, with no pitch-black carved segment. Harness corroborates: count_near ≥1 torch at every 2-unit sample along all four arms (±2..±8 both axes), unlit_carved_in_cave == 0.
- Status: PASS
  ![cross carve lit](screenshots/dungeon_cross_carve_lit.png)

### Step 3 – New connecting corridor lit along its length

- Action: Carved a short corridor (4×1 at [3.5,-3,-8]) joining the lit cross; torch recompute ran (active=153).
- Expected: New corridor lit along its entire length, not just near the junction.
- Observed: The new corridor is lit along its full length and blends into the lit network; harness count_near samples at x=1.5/3.5/5.5 on the new corridor all ≥1.
- Status: PASS (visible in the final shot below together with Step 4)

### Step 4 – Declined caves stay dark

- Action: Placed dangerous-cave fixtures 9102 (overlapping carved path, south of center) and 9103 (isolated, bottom-right), declined both, waited 1 s after sealing.
- Expected: Declined/sealed cave interiors completely dark despite surrounding lit path.
- Observed: Both declined caves render as pitch-black voids with zero torch light inside, even where 9102 overlaps carved path; the surrounding corridors stay lit. Harness: count_in_cave == 0 for both 9102 and 9103.
- Status: PASS
  ![declined caves dark, new corridor lit](screenshots/open_cave_no_dark_corridor.png)

## Criteria

- Full cross lit end to end (all four arms, ~2-unit sampling):
  - ![cross carve lit](screenshots/dungeon_cross_carve_lit.png)
- New connecting corridor lit along its full length:
  - ![final state](screenshots/open_cave_no_dark_corridor.png)
- Declined/sealed caves dark, including overlap with carved path:
  - ![declined caves dark](screenshots/open_cave_no_dark_corridor.png)
- Confirmed-open cave lit while pending cave logic holds (0 torches pending, lit after confirm):
  - ![open cave before cross carve](screenshots/open_cave_before_cross_carve.png)
- Harness state evidence (fresh windowed run, status=pass, 56/56 actions, 7/7 expectations): .gen/harness/cave_carved_path_torches/result.json — count_near ≥1 on all arm samples, unlit_carved_in_cave==0, pending/declined interiors 0 torches, [TORCH] log lines per recompute (active 29→148→153→149).

## Issues and Observations

- Low: At a glance the far end of the west cross arm can read as dark, but that darkness is solid rock past the corridor's carved end, not unlit carved floor (verified by zoom inspection + harness samples). Cosmetic readability note only.
- Pre-existing noise (not this feature): HudTheme.tres missing-texture spam in the log; exit-time renderer leak warnings. Present on clean HEAD per changes.md.

## Recommendation

Ready. The lit-cross / dark-declined-cave story is player-visible and proven by fresh stills; no code changes needed.

Manual-test result: PASSED. Scenario: .gen/ui_scenario.md. Report: .gen/manual-report.md. Escalation: no.
