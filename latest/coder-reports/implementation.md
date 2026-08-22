# Coder report: implementation

## Changed files
- `scripts/game/underground/TorchPlacer.gd` — mod: TORCH_SPACING 3→2, new COVER_RADIUS=0.75, added `_fill_unlit_path_cells` / `_is_covered_by_torch` / `_wall_mount_position` / `_first_wall_offset` / `_is_solid_cell`; every carved cell of a connected path is guaranteed within torch coverage (fills gaps the wall-spacing loop leaves, including arm ends).
- `scripts/game/underground/TorchManager.gd` — mod: MAX_TORCHES 100→1000 sanity-ceiling only; removed `optimize_torch_placement` decimation (pool expands on demand); pool refill inside `_update_torch_instances` so a big carve never truncates torches mid-list; debug `[TORCH] cave-path update active=N` line per recompute; new public `count_unlit_carved_cells(center, half_extent)` + helpers `_is_open_carved_cell`, `_is_horizontally_lit` for harness verification of "no unlit carved cells".
- `scripts/game/underground/Torch.gd` — mod: LIGHT_RADIUS 1.0→2.5, LIGHT_ENERGY 0.8→1.2, shadow off, omni_attenuation 1.0 (visible end-to-end corridor light).
- `scripts/game/Game.gd` — mod: `debug_look_down_underground()` debug helper (orthographic top-down camera at underground layer) used by scenario screenshots.
- `scripts/testing/HarnessValues.gd` — mod: new torch fields `count_near` ([x,y,z] + radius, XZ distance to active torches) and `unlit_carved_in_cave` (delegates to `count_unlit_carved_cells`).
- `tests/scenarios/cave_carved_path_torches.json` — new: full issue scenario; count_near sampled every 2 units along all four cross arms (±2..±8 on both axes), pending-cave zero-torch check before confirm, connected-corridor carve check, two declined caves (one overlapping carved path), unlit-carved==0 expectations, `[TORCH]` log expectation, three screenshots.

## Criteria
- Cross-arm coverage (~every 2 units) — Done
- New connected corridor lit along entire length — Done
- No unlit carved cells in connected network (`unlit_carved_in_cave == 0`) — Done
- Pending dangerous cave interior zero torches — Done
- Declined cave interior zero active torches incl. carved-path overlap — Done
- Headless `cave_carved_path_torches` passes with full-arm sampling — Done
- Windowed screenshot PNGs fresh + pixels inspected — Pending: manual tester owns visual evidence; headless run produced no PNGs under `.gen/harness/cave_carved_path_torches/shots/`
- `[TORCH]` log line per recompute naming trigger and count — Done (line printed per recompute with active count; initial vs incremental distinguished by preceding "Carving detected" marker)

## Commands and results
All via run_project_cmd (project godot-td, workspace poke-defense-godot/issue-cave-carved-path-torches):
- Editor import/typecheck: `godot --headless --path . --editor --quit-after 300` — exit 0, clean.
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_carved_path_torches.json` — exit 0, status=pass, all 7 expectations pass; observed counts: count_in_cave(9101)=10, arm samples ≥1 each, unlit_carved=0, pending/declined interiors = 0. Torch recompute events logged: active=29 → 148 → 154 → 136 → 136 across carves.
- Regression: same form with `declined_cave_torches_extinguish.json` — exit 0, status=pass, count_in_cave==0.
- Regression: same form with `cave_pending_seals_entrance_instantly.json` — exit 1, FAILS at action index 4 (`underground.has_route_from == true`, timeout). Verified pre-existing: identical failure after `git stash push -u` on clean HEAD. Root cause characterized with a throwaway probe scenario (removed): map_9's 0.8 discovery chance + seed 1 causes RNG-discovered caves to carve/lock cells over the hole↔exit corridor before the first route assertion, so no route can ever exist at that point. Not caused by this cluster's files; needs CaveSystem/RNG-scoped fix outside owned files.

## Notes
- Pre-existing repo noise (not ours): HudTheme.tres references `textures/ui/hud/wood_panel.png` which does not exist → repeated "Failed loading resource"/Parse Error spam on every load. Committed asset is `wood_panel_wide.png`. Flag to maintainer.
- Raw-output scan: no `SCRIPT ERROR`/`Parse Error` from game scripts; only the pre-existing HudTheme resource errors above and benign dummy-renderer exit leaks.
- Gotcha for tester: `count_near` compares XZ distance against `get_torch_positions()`, matching `Torch.LIGHT_RADIUS` semantics used elsewhere.
