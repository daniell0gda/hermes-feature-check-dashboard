# Manual Test Report – issue #124 cave-carved-path-torches (visible corridor lighting)

## Summary

- Result: **FAILED**
- Tested on: 2026-08-23, windowed Godot 4.4.1 gl_compatibility (llvmpipe), 1920x1080
- Scenario: `.gen/ui_scenario.md` via `tests/scenarios/cave_carved_path_torches.json` (windowed harness run)
- Tester: Manual-tester profile

Overall: Ran the full `cave_carved_path_torches` scenario windowed with `--rendering-method gl_compatibility --audio-driver Dummy`. Harness status=pass exit 0 (56/56 actions, all expectations pass), and three fresh PNGs were captured and pixel-inspected. Beat 1 (open cave lit) and the "declined caves stay dark" claims are proven, but the central acceptance claim — every carved cross arm visibly lit end to end — is NOT visible in the render: only one warm-lit region (east room area) exists; north/south/west arm floors read flat unlit grey even under brightness boost. This reproduces the exact planning-gate regression `dungeon_cross_carve_lit.png` was supposed to close.

## Scenario Walkthrough

### Step 1 – Before carve: open cave lit, rock dark

- Action: Windowed harness run loaded map_9, confirmed dangerous fixture 9101 ("yes"), switched to underground layer, top-down orthographic look-down, screenshot.
- Expected: Confirmed-open cave room visibly lit by wall torches; solid rock elsewhere still dark.
- Observed: Open cave room at top-center clearly lit by its wall torches (warm floor, distinct from surrounding near-black rock). PASS.
- Status: PASS

![before carve - open cave lit](screenshots/open_cave_before_cross_carve.png)

### Step 2 – After cross carve: all four arms lit end to end

- Action: Carved 2x18 + 18x2 plus-cross through the cave, waited for torch recompute ([TORCH] incremental-carve update active=148), look-down, screenshot `dungeon_cross_carve_lit.png`.
- Expected: All four carved arms show lit floor pixels along their entire length; no fully dark arm.
- Observed: FAIL. Pixel analysis of the map region: east/room area ~21% warm-orange pixels (clearly torch-lit); west arm 0.0%, north arm 0.6%, south arm 1.2% warm pixels — those floors read as flat unlit grey. Vision inspection of the full shot and per-arm crops agrees: only one warm light pool (east room). A brightness+contrast boosted re-inspection confirms no glow pools or sconce dots along N/S/W arms. Note: game-state torch data is correct (active=149 torches, count_near >= 1 at every sampled arm point, unlit_carved_in_cave == 0) — this is a rendering problem, not placement: with ~149 OmniLight3D nodes under gl_compatibility's per-mesh light limit over MultiMesh block geometry, most lights never reach the corridor floor meshes.
- Status: FAIL

![cross carved](screenshots/dungeon_cross_carve_lit.png)

### Step 3 – New connecting corridor lit along its length

- Action: Carved short connector (4x1 at x≈3.5..5.5, z=-8), waited for incremental update (active=153), final screenshot.
- Expected: New corridor fully lit along its length, blending into lit network.
- Observed: Headless count_near assertions for the connector all pass (torch state correct), but the connector region shows 0.0% warm-light pixels in the final PNG — same rendering gap as step 2. Visually it blends into an otherwise mostly-unlit network, so the player-visible claim is not met.
- Status: FAIL (state pass / pixels fail)

![final state incl. connector](screenshots/open_cave_no_dark_corridor.png)

### Step 4 – Declined cave sealed and completely dark

- Action: Fixture 9102 placed overlapping carved path at (0,-3,6), answered "no" (decline-lock, blocks=49); fixture 9103 at (8,-3,8) also declined; final screenshot inspected.
- Expected: Sealed cave interiors completely dark despite surrounding lit path.
- Observed: PASS. Sealed regions render as solid black with zero interior light, including where 9102 overlaps the carved path. Harness confirms count_in_cave == 0 for both 9102 and 9103 after sealing.
- Status: PASS

## Criteria

- Every carved cell within torch coverage / count_near along all four arms (~2-unit samples)
  - State-level: PASS (headless + windowed harness, all wait_for_condition count_near ok)
- In fresh windowed gl_compatibility top-down run, each of the four carved arms shows visibly lit floor pixels along entire length — no arm fully dark
  - **UNVERIFIED / FAIL**: ![cross](screenshots/dungeon_cross_carve_lit.png) — only east/room region reads warm-lit; W/N/S arms read unlit (0.0–1.2% warm pixels vs 20.9% east)
- New connecting corridor lit along its entire length
  - State PASS; pixel evidence FAIL (0.0% warm pixels in connector region of ![final](screenshots/open_cave_no_dark_corridor.png))
- Pending cave interior zero torches even when overlapping carved path
  - PASS by harness (`count_in_cave == 0` while pending); consistent with dark render before confirm
- After decline+seal, interior contains zero active torches incl. overlap cells
  - PASS: harness fields and solid-black sealed regions in ![final](screenshots/open_cave_no_dark_corridor.png)
- Fresh windowed PNGs exist with current timestamps showing full cross lit and declined caves dark, pixels inspected
  - PNGs exist and are fresh (this run, 2026-08-23 10:29 UTC); declined caves dark confirmed; "full cross lit" part FAILS inspection
- `[TORCH]` log line per recompute naming trigger and count
  - PASS: `[TORCH] initial-placement`-style labeled lines observed: `incremental-carve update active=29 / 148 / 153 / 149`

## Issues and Observations

- **High** — Visible corridor lighting regression persists (steps 2–3): torch *placement* data is complete (149 active, full coverage per state source) but under gl_compatibility the lights do not illuminate the corridor floors except around the east room. Likely cause: gl_compatibility per-mesh omni-light limit vs. large merged/MultiMesh underground geometry, so most of the 149 lights are dropped per mesh. Fix must make lighting reach the rendered floor (e.g. fewer/larger lights, baked glow decals/emissive floor tinting near torches, or splitting geometry) without weakening headless coverage assertions.
- **Low** — Harness `status=pass` can mask this entirely: every expectation is state-based, none asserts rendered brightness. Consider a screenshot-pixel heuristic guard for windowed runs.
- Pre-existing noise seen as documented: HudTheme.tres missing-texture spam; exit-time dummy/GLES leak warnings. No SCRIPT ERROR / Invalid call found in raw output.

## Recommendation

Not ready. The logic layer (placement, sealing, determinism) is solid and the dark-declined-cave behavior renders correctly, but the headline player-visible criterion — corridors visibly lit end to end — fails in actual pixels and needs a code fix targeting the compatibility-renderer light budget before re-check.

Manual-test result: FAILED. Scenario: tests/scenarios/cave_carved_path_torches.json (windowed).
Report: .gen/manual-report.md. Escalation: yes.
