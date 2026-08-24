# Request

- request_id: req-124-cave-carved-path-torches-r4
- issue: https://github.com/daniell0gda/poke-defense-godot/issues/124
- project runner key: `godot-td`
- workspace: `poke-defense-godot/issue-cave-carved-path-torches`
- branch: `issue/cave-carved-path-torches` (already has r3 placement work at `b5092fc`; extend it, do not reset)

## Problem (Daniel, 2026-08-24)

A hardcoded `MAX_TORCHES = 250` is not future-proof. A 40×40 grid can fit; a 60×60 (or larger) map will run out. Also the current spacing (`TORCH_SPACING = 1`, every corridor cell) is too dense — **make the distance between torches a little bit bigger**.

## Required solution

- **No fixed torch cap that a larger map can exhaust.** Compute the budget from the live grid (e.g. carved corridor cell count, or `grid_width * grid_depth`) so 40×40, 60×60, and bigger maps all keep full corridor coverage. Do not leave dark carved segments because a constant cap was hit.
- **Widen spacing a little** vs r3 (every cell). Keep walls/curves lit; do not go back to the old clump-every-3rd-wall-face bug. Unique-cell stride along the corridor, not raw wall-face list.
- **Do not change torch light intensity** (`Torch.gd` energy / radius / color / OmniLight settings stay byte-for-byte unchanged).

## Done when

- Every carved cave path tile that should be lit still has torch coverage, including curve / bent / side-to-side tunnels
- New carves still get torches
- A 60×60 (or equivalent larger) grid does not hit a hardcoded cap that leaves uncovered corridor cells
- Spacing is visibly a bit farther than r3 every-cell packing
- `Torch.gd` intensity unchanged

## Manual testing

required. Windowed, underground, side-to-side carve with curves, top-down aimed at `camera_target`. No `--headless` for manual tester.

## Runner

`godot-td` / `poke-defense-godot/issue-cave-carved-path-torches`. Write a real non-empty `.gen/plan.md`.
