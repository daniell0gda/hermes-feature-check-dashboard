# Request: Issue #132 — Move tower details panel to the right side of the screen

- **Project:** poke-defense-godot
- **Runner key:** `godot-td` (never the folder name)
- **Workspace:** `poke-defense-godot/issue-move-tower-details-panel-right-side`
- **Branch:** `issue/move-tower-details-panel-right-side`
- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/132 (status:in-progress, assigned @me)
- **Revision budget:** 2

## Feature description
When a tower's details are shown, the tower details panel appears on the wrong side of the screen. It should be docked on the **right side** of the screen instead, so it does not overlap gameplay elements or the tower it describes.

## Acceptance criteria (from issue)
1. Showing a tower's details displays the panel docked on the right side of the screen.
2. Panel does not overlap gameplay-critical UI or the tower it describes.
3. Layout stays correct across window resize / different resolutions.

## Notes for workers
- Runner commands must use project key `godot-td`, workspace `poke-defense-godot/issue-move-tower-details-panel-right-side`. Invented workspace names cause HTTP 422.
- Visible player-facing UI change ⇒ manual_testing: **required** with windowed screenshots (no `--headless`; use `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` when Vulkan fails in the worker).
- Manual test must end with an overall UI-sanity pass per final screenshot: judge `ui_feels_broken: yes|no`; a yes fails even if geometry passes. State this in `.gen/manual_testing.md`.
- Layout assertions must observe rendered geometry (`get_global_rect`), never theme overrides; include at least one real `.tscn` scene in tests.
- Check panel placement against window size (anchor to right edge), and verify at a second resolution for criterion 3.
