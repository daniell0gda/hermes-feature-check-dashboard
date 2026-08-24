# Manual report — Exposed Plating close-up visual evidence

Windowed run of `tests/scenarios/exposed_plating_vfx.json` via `run_project_cmd`
(project=godot-td, workspace=poke-defense-godot/issue-exposed-plating), command:
`godot --path . res://scenes/Main.tscn --rendering-method gl_compatibility
--rendering-driver opengl3 --audio-driver Dummy --resolution 1920x1080 --
--harness=res://tests/scenarios/exposed_plating_vfx.json` — exit 0, status=pass,
all 7 expectations met, `record_frames captured=6 saved=6`.

Root cause fixed this iteration: the worktree's `models/glb/Orc Enemy.glb` was a
131-byte git-LFS pointer (git-lfs unavailable in the runner), so the boss rendered
as an invisible node — earlier "during" stills showed zero wash because there was
no body to tint. The real 146,420-byte blob (sha256 verified against the LFS
pointer) was restored, the stale `.import` remap repaired, and the GLB re-imported.

## Evidence (all in `.gen/screenshots/`)

### Before breach — no wash
![before](screenshots/before_breach_no_wash.png)
Enemy fills a large part of the frame (camera_focus distance 3.0, pitch 25°),
debug panel hidden. Body is its natural green; no amber anywhere.

### During Exposed — amber wash
![during](screenshots/exposed_wash_on_breach.png)
Same framing moments after the full-armor breach hit: the enemy body is washed in
an unmistakable bright amber/orange (body-tint lerp + emission pulse from
`ExposedVFX`), clearly different from the before still. `exposed_vfx == 1`
asserted in the same run.

### After expiry — wash gone
![after](screenshots/wash_cleared_after_expiry.png)
After `[EXPOSED] expire on Orc Enemy_boss`, the body is back to natural green,
matching the before still. `exposed_vfx == 0` asserted.

### GIF — whole Exposed window, real consecutive engine frames
![gif](screenshots/exposed_window.gif)
Exported (ffmpeg, 6 fps) from the 6 real consecutive viewport frames captured by
the harness `record_frames` action across the Exposed window while zoomed on the
enemy (`.gen/harness/exposed_plating_vfx/record/exposed_window_0000..0005.png`).
The green→amber→green transition plays over the window.

## Verdict

ui_feels_broken: no
