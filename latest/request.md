# Request

- request_id: req-124-cave-carved-path-torches-r5
- issue: https://github.com/daniell0gda/poke-defense-godot/issues/124
- project runner key: `godot-td`
- workspace: `poke-defense-godot/issue-cave-carved-path-torches`
- branch: `issue/cave-carved-path-torches` @ `f161e1d` (extend; do not reset)

## Problem (Daniel, 2026-08-24)

Coverage/lighting works, but some torches (he thinks maybe every 2nd) sit **in the corridor, not on the wall**. Screenshots: mid-path sticks floating in front of the wall while neighbors are wall-mounted.

Also set **`TORCH_SPACING = 4`**.

## Likely cause

`TorchPlacer._best_wall_torch_for` (coverage repair) returns
`wall_offset = Vector3(WALL_OFFSET * 0.5, 0, WALL_OFFSET * 0.5)` — diagonal into the walkway, not a real wall face.
Spacing-pass torches use a single cardinal wall offset; repair-pass torches do not.

## Required

- Every placed torch must mount on a **real adjacent solid wall** (cardinal wall offset from `_wall_cells`). No mid-corridor / in-front-of-wall sticks.
- `TORCH_SPACING = 4` (unique corridor-cell stride, not raw wall-face list).
- Keep live-grid torch budget (no hardcoded MAX_TORCHES). Keep curves lit via coverage repair, but repair must also pick a real wall mount.
- **Do not change torch light intensity** (`Torch.gd` energy/radius/color/omni unchanged).

## Done when

- No torch stands in the walkway; all hug a wall
- Spacing is 4
- Curves/side-to-side carved path still covered
- Windowed underground screenshots show wall-mounted torches (not floating mid-path)
- `ui_feels_broken: no` on those shots

## Manual testing

required. Windowed, no `--headless`. Underground, side-to-side carve with a curve, camera at `camera_target`.

## Runner

`godot-td` / `poke-defense-godot/issue-cave-carved-path-torches`. Write a real non-empty `.gen/plan.md`.
