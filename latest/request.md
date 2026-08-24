# Request: issue #115 — create_ground_plane_material bypasses the resource cache

- **Project:** poke-defense-godot
- **Git workspace:** poke-defense-godot/issue-ground-material-ignores-cache
  (`/workspace/git-workspaces/poke-defense-godot/issue-ground-material-ignores-cache`)
- **Branch:** `issue/ground-material-ignores-cache` (rebased onto origin/master d7551d9)
- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/115
- **Labels at claim:** status:in-progress (already claimed)
- **Request ID:** issue115-ground-material-ignores-cache-r2
- **Retry note:** Daniel asked Retry 2026-08-23. Preserve existing source/harness; treat r1 `.gen` as historical; re-verify after rebase.

## Feature summary

`TextureAtlasUtils.create_ground_plane_material` (scripts/utils/TextureAtlasUtils.gd ~lines 145-158)
loads both ground textures with `ResourceLoader.CACHE_MODE_IGNORE`, defeating the resource cache and
the `AssetPreloader` startup preload of `underground_floor.jpg`. Switch to `CACHE_MODE_REUSE` — but
first find the real cause of the "shader only" map-switch bug the comment blames on caching.
Likely suspect: `EnvironmentUtils._update_ground_plane_color` reassigns `grass_tint` but never the
albedo samplers.

## Acceptance criteria

1. Ground plane material uses cached textures (`CACHE_MODE_REUSE`) for
   `res://textures/nature/map_grass.jpg` and `res://textures/underground_floor.jpg`.
2. Root cause of the original map-switch shader issue identified; if it was a missing sampler
   reassignment, that is fixed so the ground keeps its grass/dirt textures across a map switch.
3. Verified by switching maps twice through the game-test harness and confirming the ground still
   shows blended grass/dirt, not flat shader output. Manual test with windowed screenshots is
   required (visible player-facing surface).

## Runner notes (redo reminders)

- Runner key `godot-td`, workspace `poke-defense-godot/issue-ground-material-ignores-cache`.
- Native Godot commands via run_project_cmd; explicit scene argument before user args in harnesses;
  editor gate `godot --headless --path . --editor --quit-after 300`.
- Windowed evidence needs `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` when Vulkan fails.
- manual_testing: required (ground texture visible in-game).
