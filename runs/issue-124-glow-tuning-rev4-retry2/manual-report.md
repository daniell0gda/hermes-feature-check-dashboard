# Manual Test Report – issue #124 cave-carved-path-torches (corridor torch lighting)

## Summary

- Result: PASSED
- Tested on: 2026-08-23 15:21 UTC, Godot 4.4.1 windowed gl_compatibility (llvmpipe), map_9
- Scenario: tests/scenarios/cave_carved_path_torches.json (run windowed via run_project_cmd, project godot-td)
- Tester: Manual-tester profile

Overall: Ran the full harness scenario windowed (no --headless): status=pass, exit 0,
with fresh PNGs captured at each beat (15:21 UTC timestamps match this run). The scripted
warm-pixel gate (.gen/measure_warm_pixels.py) reports every sampled segment of all four
carved cross arms nonzero-warm, localized pools (no floodlight saturation) on the full-cross
screenshot. Declined/sealed cave interiors render completely dark, including where their
seal overlaps the previously carved path.

## Scenario Walkthrough

### Step 1 – Open confirmed cave lit, surrounding rock dark (before carve)

- Action: Harness loads map_9, confirms fixture cave 9101 ("yes"), switches to the
  underground layer, calls debug_look_down_underground (top-down orthographic),
  screenshots.
- Expected: The open cave room visibly lit by wall torches; solid rock elsewhere dark.
- Observed: Cave room shows warm torch glow; surrounding uncarved rock stays dark.
- Status: PASS



### Step 2 – Cross carve: all four arms lit end to end

- Action: carve_rectangle 2x18 + 18x2 through the cave (plus-sign cross); wait for
  count_near >= 1 at every ±2..±8 sample point on both axes; look down; screenshot.
- Expected: Entire carved cross visibly lit along walls; no dark gaps inside corridors;
  small distinct warm pools, not floodlight.
- Observed: All four arms read as chains of small warm light pools over the carved floor.
  Numeric gate on this PNG: all 32 arm segments warm (minima north 60.4%, south 66.6%,
  west 72.4%, east 26.1%); luminance-spread 12.6–86.1 (localized pools, not flat);
  max bright-pixel fraction 0.27% (no washout). Harness [TORCH] incremental-carve
  updates logged: active=29 → 148 → 153.
- Status: PASS



### Step 3 – New connecting corridor lit along its full length

- Action: carve_rectangle at (3.5,-3,-8) size 4x1 joining the cross near the north arm;
  count_near checks at x=1.5/3.5/5.5, z=-8; unlit_carved_in_cave == 0; screenshot.
- Expected: New corridor lit end to end, blending into the existing lit network.
- Observed: count_near assertions green at junction, middle and far end; no unlit carved
  cell reported; the short spur is visibly lit into the network.
- Status: PASS



### Step 4 – Pending then declined dangerous caves stay dark

- Action: Fixture cave 9102 placed overlapping carved path at (0,-3,6), pending verified,
  declined and sealed; second isolated cave 9103 at (8,-3,8) likewise declined;
  final top-down screenshot.
- Expected: Both cave interiors completely dark despite surrounding lit path; overlap
  cells contain zero active torches.
- Observed: Two large pitch-black sealed regions visible at the bottom/right of the map.
  Harness asserts count_in_cave == 0 for 9102 and 9103 after sealing (both pass);
  log shows `[CAVE] Decline-lock cave=9102 blocks=49` and `cave=9103 blocks=49`
  re-covering the overlapped path cells with solid rock.
- Status: PASS

## Criteria

- Full carved cross visibly lit end to end, warm localized pools, no dark arm stretch:
  - ![full cross lit](screenshots/dungeon_cross_carve_lit.png)
  - Numeric: .gen/warm_pixel_measurement.txt — dungeon_cross_carve_lit.png RESULT section:
    all 32 segments >0% warm, pool-shape ok (lum spread 12.6–86.1), saturation ok
    (max bright 0.27%). PASS.
- Open cave lit / rock dark before carving:
  - ![before carve](screenshots/open_cave_before_cross_carve.png)
- New connecting corridor lit; declined caves dark, overlap cells sealed:
  - ![after declines](screenshots/open_cave_no_dark_corridor.png)
  - Harness state assertions: unlit_carved_in_cave == 0; count_in_cave(9102) == 0;
    count_in_cave(9103) == 0 — all pass in .gen/harness/cave_carved_path_torches/result.json.
- [TORCH] debug lines name trigger + count: `[TORCH] incremental-carve update active=N`
  observed at N=29, 148, 153, 149 in this run's stdout.
- Headless scenario status: pass (this same scenario also ran headless earlier in the run
  history; today's windowed run returned `[Harness] status=pass exit=0`).

### Measurement note (honest caveat)

On the post-decline PNG (open_cave_no_dark_corridor.png) the generic arm sampler reports
south[4] and south[5] as 0.00% warm. These two boxes sit exactly where declined cave 9102's
decline-lock re-sealed 49 tiles of carved path (log: `Decline-lock cave=9102 ... blocks=49`;
cave center (0,-3,6)). Those cells are sealed cave interior at capture time, which the
acceptance criteria explicitly require to be dark (zero torches inside declined caves,
including overlap cells). The same corridor segments measure 67–82% warm on the pre-decline
cross screenshot. Verdict: correct behavior, not an unlit carved segment; the full-cross
gate image passes 32/32 segments. No code change requested.

## Issues and Observations

- Pre-existing noise (not ours, Low): HudTheme.tres references missing
  textures/ui/hud/*.png → repeated "Failed loading resource" spam every launch; benign
  dummy-renderer leak warnings at exit. No SCRIPT ERROR / Invalid call from feature code.
- Measurement-script scope (Low): measure_warm_pixels.py samples fixed arm boxes without
  knowing about decline seals, so any future decline overlapping an arm will flag false
  positives there. Documented above; no action required this iteration.

## Recommendation

Ready. The player-visible lighting story holds: carved corridors show distinct warm torch
pools along all four arms end to end, new connections light up fully, and pending/declined
dangerous caves stay completely dark even where they overlap carved path. Ship as is.
