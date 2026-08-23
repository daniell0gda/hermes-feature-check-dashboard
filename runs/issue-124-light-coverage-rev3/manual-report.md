# Manual Test Report – issue-124 cave-carved-path-torches

## Summary

- Result: PASSED
- Tested on: 2026-08-23, windowed Godot 4.4.1 gl_compatibility (llvmpipe), map_9, seed 1
- Scenario: tests/scenarios/cave_carved_path_torches.json
- Tester: Manual-tester profile

Ran the harness scenario `cave_carved_path_torches.json` in a real windowed
gl_compatibility run (never headless) through `run_project_cmd`. The run finished
with `status=pass` (exit 0, 56/56 actions, all 7 expectations pass). Three fresh
PNG screenshots were captured by the harness at the scenario's scripted beats and
pixel-inspected both visually and with a scripted warm-pixel measurement.

## Scenario Walkthrough

### Step 1 – Before the cross carve (open cave lit, rock dark)

- Action: Confirmed dangerous cave 9101 ("yes"), switched to the underground
  layer, looked straight down top-down (`debug_look_down_underground`).
- Expected: The confirmed-open cave room is visibly lit by torch light; solid
  rock elsewhere is dark.
- Observed: One small brightly lit open room at top-center of the play area;
  everything else is dark unlit rock. Numeric check: only 5,433 warm pixels in
  the play area (18.6% of image rows contain warm light) — a single warm region.
- Status: PASS
- ![open cave before cross carve](screenshots/open_cave_before_cross_carve.png)

### Step 2 – After the plus-cross carve (all four arms lit end to end)

- Action: Carved the 2x18 vertical + 18x2 horizontal cross through the open cave,
  waited for torch recompute ([TORCH] incremental-carve active=148), looked down.
- Expected: The entire carved cross — all four arms, end to end — visibly lit by
  warm torch light; no dark gaps inside carved corridors.
- Observed: All four arms of the plus-shaped corridor are illuminated with warm
  yellow torch light from the center intersection to each outer end; no unlit
  stretches inside the carved path. Warm pixels jumped to 42,150 (~8x step 1),
  spread across 64.5% of rows / 60.3% of columns; mean brightness of carved floor
  rose from 184 to 209. Harness count_near assertions passed at every 2-unit
  sample along all four arms (±2..±8 both axes).
- Status: PASS
- ![cross carve fully lit](screenshots/dungeon_cross_carve_lit.png)

### Step 3 – New connecting corridor lit along its length

- Action: Carved the short connecting corridor (carve_rectangle at [3.5,-3,-8],
  4x1); torch recompute active=153; verified count_near >= 1 at x=1.5/3.5/5.5 on
  the new corridor and unlit_carved_in_cave == 0.
- Expected: The new corridor is fully lit along its length, blending into the
  existing network.
- Observed: In the final screenshot the whole excavated network (cross + new
  corridor) reads as one continuous lit path; vision inspection found no unlit
  carved segment. Warm pixels grew further to 48,497 (73.6% row spread).
- Status: PASS
- ![network fully lit incl. new corridor](screenshots/open_cave_no_dark_corridor.png)

### Step 4 – Declined caves stay dark

- Action: Declined and sealed overlapping cave 9102 and isolated cave 9103;
  harness asserted count_in_cave == 0 for both after sealing (both passed).
- Expected: Declined cave interiors completely dark despite surrounding lit path.
- Observed: Two solid-black sealed interiors visible at the bottom of the final
  screenshot (lower-middle with orange sealed entrance glow, lower-right fully
  dark). Dark-rock pixels in the play area rose from 191 to 43,846 between step 2
  and step 4 — exactly the newly sealed dark interiors next to the lit network.
- Status: PASS
- ![declined caves dark](screenshots/open_cave_no_dark_corridor.png)

## Criteria

- Carved cross fully lit along all four arms end to end (player-visible)
  - ![cross carve fully lit](screenshots/dungeon_cross_carve_lit.png)
- Open cave lit / rock dark before carving
  - ![open cave before cross carve](screenshots/open_cave_before_cross_carve.png)
- New connected corridor lit along its entire length
  - ![network fully lit incl. new corridor](screenshots/open_cave_no_dark_corridor.png)
- Pending and declined cave interiors contain zero torches / stay dark even when
  overlapping carved path (harness: count_in_cave == 0 for 9102 and 9103 after
  decline; visual: black sealed interiors)
  - ![declined caves dark](screenshots/open_cave_no_dark_corridor.png)
- Headless-side criteria (count_near coverage every ~2 units on all arms,
  unlit_carved_in_cave == 0, [TORCH] log lines per recompute) verified via fresh
  `.gen/harness/cave_carved_path_torches/result.json`: status=pass, exit 0,
  56/56 actions, all expectations pass, [TORCH] recomputes active=29→148→153→149.
- Scripted warm-pixel measurement (manual tester run, not a vision summary):
  saved at `screenshots/warm_pixel_measurement.json`. Key numbers:
  - before carve: 5,433 warm px, 18.6% row spread (single warm region)
  - after cross: 42,150 warm px, 64.5%/60.3% row/column spread, carved-floor
    mean luminance 209 — measurable warm-light presence along all four arms
  - final: 48,497 warm px AND 43,846 dark interior px — lit network + dark
    declined caves in the same frame

## Issues and Observations

- Low, pre-existing: heavy HudTheme.tres "Failed loading resource" noise in
  stdout (missing textures/ui/hud/*.png). Known repo noise, not related to this
  issue; no SCRIPT ERROR / Invalid call seen.
- Low, cosmetic: exit-time dummy-renderer/GLES leak warnings after run end
  (PagedAllocator, RID leaks). Known noise, does not affect gameplay frames.
- Low, UX note: the lower-middle declined cave shows a small orange glow at its
  sealed entrance while the interior itself stays fully dark — reads as the seal
  marker, not interior lighting.

## Recommendation

Ready. The carved corridor lighting renders correctly on screen end-to-end, and
pending/declined caves stay dark even where they overlap carved path. No code
changes needed from this manual pass.
