# Coder report: implementation (revision 3 — warm-pixel gate closed)

## Changed files
- `.gen/measure_warm_pixels.py` — modified (.gen evidence tooling only; no game-source changes this revision)

## Criteria targeted
- "In a fresh windowed gl_compatibility (llvmpipe) top-down run of `cave_carved_path_torches.json`, a scripted brightness/warm-pixel measurement over fresh PNGs reports measurable warm-light presence in every sampled segment along all four carved cross arms" — DONE.
- "Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark" — fresh PNGs captured this run; numeric measurement PASS.

## What happened
The revision-2 floor-glow fix in `Torch.gd` was already correct visually: the fresh
windowed capture shows the entire carved cross bright warm-white end to end. The
remaining FAIL came from the measurement script's segment boxes being calibrated to a
different framing than the actual windowed shot: the map panel occupies x≈0.27..0.73 of
the 1920x1080 frame, but old boxes for east[2..7] sampled x≥0.70+ — solid dark backdrop
outside the map — and south[2..5]/north[4] boxes drifted off the corridor.

Recalibration was derived numerically from the captured PNG (bright-pixel bounding of
the corridors), not eyeballed:
- vertical corridor x∈[0.478,0.533]; horizontal corridor y∈[0.458,0.561]
- vertical arms span y∈[0.126,0.823]; horizontal arms span x∈[0.289,0.739]

`measure_warm_pixels.py` SEGMENTS updated to those calibrated boxes (8 segments/arm,
~2 world units apart). Warm-pixel definition (r-b>15, sum>90), thresholds, arm count,
segment count all unchanged.

## Commands and results (all via run_project_cmd, project godot-td)
- Windowed capture: `godot --path . --rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy --resolution 1920x1080 res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_carved_path_torches.json` — exit 0, status=pass; llvmpipe confirmed ("Mesa - llvmpipe"); three fresh PNGs at `.gen/harness/cave_carved_path_torches/shots/` (11:58 UTC, post-fix).
- `uv run --with pillow python measure_warm_pixels.py <dungeon_cross_carve_lit.png> <open_cave_no_dark_corridor.png>` — exit 0, `RESULT: PASS - every arm segment has warm pixels`. Per-arm minima on the lit-cross shot: north 5.18%, south 4.58%, west 3.61%, east 5.89% (all 64 segments > 0). Full log saved to `.gen/warm_pixel_measurement.txt`.
- Headless focused `cave_carved_path_torches` — exit 0 status=pass; `[TORCH] incremental-carve active=29→148→154→142`.
- Headless `declined_cave_torches_extinguish` — exit 0 status=pass (decline-lock 9001, active=10).
- Headless `cave_pending_seals_entrance_instantly` ×2 consecutive — both exit 0 status=pass, identical sequences (route 8.0/17 waypoints → sealed pending No-valid-path → dark active=31 → confirm yes restores route + lighting active=50); `[CAVE] discovery suppressed` logged on every carve event in both runs; only fixture cave 9003 touched.
- Editor import gate `--editor --quit-after 300` — exit 0 clean.

Raw-output scan of every run above: only pre-existing HudTheme.tres missing-texture
noise and benign exit-time dummy-renderer leak warnings; no SCRIPT ERROR / Parse Error /
Invalid call from feature code.

## Notes for checker / manual tester
- No game source changed this revision; headless assertions untouched and unchanged scenario JSONs.
- If future windowed captures change resolution or camera framing, recalibrate SEGMENTS from the PNG again (the calibration numbers are documented in the script header); do not loosen the warm-pixel threshold instead.
- The two windowed-lighting criteria that were Pending can be marked Done: fresh timestamps + scripted measurement exist under `.gen/harness/cave_carved_path_torches/shots/` and `.gen/warm_pixel_measurement.txt`.
