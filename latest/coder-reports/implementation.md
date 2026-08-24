# Coder report: implementation (revision 4)

## Changed files
- `models/glb/Orc Enemy.glb` — restored real LFS blob (146,420 bytes, sha256 matches the
  git-LFS pointer; the worktree had only a 131-byte pointer file because git-lfs is absent
  in the runner image). Copied from a sibling worktree of the same repo/commit.
- `models/glb/Orc Enemy.glb.import` — stale `valid=false` remap repaired (re-import rewrote
  it to a valid remap pointing at the imported `.scn`).
- `.gen/screenshots/` — before/during/after PNGs + `exposed_window.gif` (ffmpeg 6 fps from
  the 6 real consecutive record frames).
- `.gen/manual-report.md` — new, ends with `ui_feels_broken: no`.
- `.gen/harness/{exposed_plating_once_per_shield,exposed_plating_vfx}/result.json` — fresh.
- No gameplay-source changes: ExposedVFX/HarnessActions/scenarios were already correct; the
  wash had nothing to render on because the boss model never loaded.

## Criteria
- Headless `exposed_plating_once_per_shield` pass — Done (fresh, 6/6).
- Headless `exposed_plating_vfx` pass incl. `[EXPOSED] triggered on`/`expire on` — Done (fresh, 7/7).
- Camera close-up before screenshot checkpoints — Done (distance 3.0, pitch 25°; with the
  model restored the enemy occupies a large part of the frame; prior "too far" shots were an
  artifact of the invisible model).
- Debug panel not covering enemy in captures — Done (`set_debug_panel visible=false` before
  all checkpoints; verified in fresh PNGs).
- `record_frames` spans the Exposed window, >0 real consecutive frames — Done (captured=6
  saved=6, windowed run).
- "During" still shows obvious amber wash differing from "before" — Done (green → bright
  amber body tint + emission; eye-verified on fresh captures).
- "After expiry" matches "before" — Done (green again, wash gone).
- Strengthen-until-visible clause — not needed; root cause was the missing model, not weak VFX.
- PNGs/GIF in `.gen/screenshots/` + `.gen/manual-report.md` ending `ui_feels_broken:` — Done (`no`).

## Commands and results (all via run_project_cmd, project=godot-td)
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0.
- `["godot","--headless","--path",".","--import"]` — exit 0; re-imported Orc Enemy.glb
  ("reimport: step 0: Orc Enemy.glb", `.scn` produced).
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
  — exit 0, status=pass, 6/6; log now shows "[ENEMY DEBUG] Loaded GLB model" (previously
  "GLB not found").
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
  — exit 0, status=pass, 7/7; record_frames skipped headless as designed.
- `["godot","--path",".","res://scenes/Main.tscn","--rendering-method","gl_compatibility","--rendering-driver","opengl3","--audio-driver","Dummy","--resolution","1920x1080","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
  — exit 0, status=pass, 7/7, `[HARNESS] record_frames captured=6 saved=6`.

## Notes
- The prior iterations' "camera too far / zero amber pixels" findings were misdiagnoses:
  the boss rendered as nothing at all (missing GLB), so no camera distance could show a wash.
- Windowed runs log `[EXPOSED] expire on` once per frame during expiry processing
  (5 lines); headless logs exactly one. Cosmetic; expectations use `contains`.
- r5-too-far shots were not copied to `.gen/screenshots/` (per plan).
