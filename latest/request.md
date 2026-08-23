# Request

- request_id: req-124-cave-carved-path-torches-r3
- issue: https://github.com/daniell0gda/poke-defense-godot/issues/124
- project runner key: `godot-td` (never folder name)
- workspace: `poke-defense-godot/issue-cave-carved-path-torches`
- branch: `issue/cave-carved-path-torches` on origin/master
- prior run `req-124-cave-carved-path-torches-r2` archived: planner returned empty (no `.gen/plan.md`). Restart from scratch. Do not treat r2 as evidence.

## Problem

Not all carved cave path tiles get torches. Especially **curve / bent tunnels** stay dark.

## Required verification path (Daniel)

- Go underground.
- Carve a path **from side to side** (full crossing).
- Inspect **curve tunnels** — not only straight corridors.
- Missing torches on those curves is the failure.

## Hard constraint

- **Do not change torch light intensity.** Current intensity is correct. Fix placement/coverage only.

## Done when

- Every carved cave path tile that should be lit has a torch (or equivalent cave light)
- New carve operations also get torches on the new path, including curves
- No leftover dark carved corridors in the same cave as lit path (except intentional uncarved/dark rock)
- Torch OmniLight / energy / range / intensity values stay unchanged

## Planner must write

A real non-empty `.gen/plan.md` plus `.gen/clusters/*.md` and `.gen/ui_scenario.md`. Empty plan is a failed phase — do not proceed to code.

## Manual testing

required. Windowed screenshots, top-down underground, after a side-to-side carve that includes curves. Do not use `--headless` for manual tester. Camera must aim at `camera_target`.
