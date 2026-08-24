# Request

- request_id: req-124-cave-carved-path-torches-r6
- issue: https://github.com/daniell0gda/poke-defense-godot/issues/124
- project runner key: `godot-td`
- workspace: `poke-defense-godot/issue-cave-carved-path-torches`
- branch: `issue/cave-carved-path-torches` @ `ecef2a1` (extend; do not reset)

## Problem (Daniel, 2026-08-24)

Torch position is now good (wall-mounted), but **`TORCH_SPACING` has no visible effect** — changing the number does not change how dense the torches are.

## Root cause (verified by parent)

`TorchPlacer.calculate_torch_positions`:
1. `_spacing_torch_cells` strides unique corridor cells by `TORCH_SPACING` (now 4) — this part obeys the constant.
2. But `_repair_coverage` then adds torches until every "required" cell is within `Torch.LIGHT_RADIUS * 0.5` (≈ 1 cell) of a torch. With a half-radius coverage, repair re-densifies the corridor to roughly every 2 cells **regardless of TORCH_SPACING**, so the constant is effectively overridden and the density never changes visually.

The coverage radius is also wrong in kind: `Torch.LIGHT_RADIUS` is 1.0 world unit, but the repair only lights cells within 0.5 (half) of a torch, which is why it packs so tight.

## Required

- Make `TORCH_SPACING` **actually drive torch density** end to end. Changing it (e.g. 4 → 6 → 8) must produce visibly sparser torches.
- The coverage-repair pass must not silently undo spacing. It should only fill genuine gaps (bends/corners) left by spacing, using a coverage radius consistent with the real light reach (`Torch.LIGHT_RADIUS`), not a halved radius that forces near-full packing.
- Keep wall-mounting correct (every torch on a real cardinal wall face, no mid-corridor sticks).
- Keep live-grid torch budget (no hardcoded MAX_TORCHES).
- **Do not change torch light intensity** (`Torch.gd` energy/radius/color/omni unchanged). If the coverage radius needs to reflect light reach, derive it from `Torch.LIGHT_RADIUS` — do not edit Torch.gd.

## Done when

- Changing `TORCH_SPACING` to a larger value visibly reduces the number of torches along a straight corridor
- Curves/bends still stay lit (repair fills only real gaps)
- Torches remain wall-mounted
- Windowed underground screenshots: sparse vs dense difference is visible
- `ui_feels_broken: no`

## Manual testing

required. Windowed, no `--headless`. Underground side-to-side carve with a curve, camera at `camera_target`. Show that spacing visibly changes density.

## Runner

`godot-td` / `poke-defense-godot/issue-cave-carved-path-torches`. Write a real non-empty `.gen/plan.md`.