# Request: Issue #106 — Harness cannot reach runtime-instanced modals

- Repo: daniell0gda/poke-defense-godot
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/106
- Workspace: poke-defense-godot/issue-harness-cannot-reach-runtime-modals
- Branch: issue/harness-cannot-reach-runtime-modals (cut fresh from origin/master, d241462)
- Labels: status:in-progress, priority:medium, type:coverage-gap, type:harness

## Problem

`HarnessActions._resolve_target()` (scripts/testing/HarnessActions.gd:585-610) only matches a fixed
name list (`game`, `ui`, `underground`, `cave_system`, `progression`, `towers`, `hole_placement`,
`gamestate`, `strategy_record`). Runtime-instanced modals like the Options screen
(`UI.gd._on_pause_options()` → `preload(...).instantiate()`, reference never kept) are unreachable.
So scenarios cannot: switch Options to the Sound tab (themed HSliders never seen rendered), open a
WoodDropdown picker to screenshot its floating list / name plate / SpeedOption rows, or assert which
resolution/UI-scale row is checked.

## Done when (either approach)

- `UI.gd` keeps the instantiated options modal in a field (like `_setup_manage_towers_popup()`
  keeps `manage_towers_popup`) plus small `ui`-callable methods (e.g. options-tab setter,
  picker-opener); **or**
- `_resolve_target` gains a generic node-path target (e.g. `{"target":"node","path":"UI/OptionsScreen/..."}`).

Then extend `tests/scenarios/hud_other_panels.json` with checkpoints for the Sound tab and one open
WoodDropdown; inspect the PNGs.

## Redo notes (learned from prior runs)

- Runner names are exact: project key `godot-td`, workspace `poke-defense-godot/issue-harness-cannot-reach-runtime-modals`. Never invent variants (HTTP 422).
- Checker classification line must be exactly `classification: pass|fixable|blocked|design_failure` (no bold value).
- Manual testing: this issue is about rendered UI theming — windowed screenshots required (never `--headless` for manual evidence). Use runner flags `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` when Vulkan fails in worker. Overall UI-sanity pass on each final screenshot (`ui_feels_broken: yes|no`).
