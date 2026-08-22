# Coder report: implementation (re-verification, 2026-08-22)

## Changed files
- No source changes this pass. Implementation unchanged from iteration 1:
  - `scripts/game/underground/TorchPlacer.gd` — mod
  - `scripts/game/underground/TorchManager.gd` — mod
  - `scripts/game/underground/Torch.gd` — mod
  - `scripts/game/Game.gd` — mod (`debug_look_down_underground`)
  - `scripts/testing/HarnessValues.gd` — mod (`count_near`, `unlit_carved_in_cave`)
  - `tests/scenarios/cave_carved_path_torches.json` — new

## Criteria
- Cross-arm coverage (~every 2 units) — Done
- New connected corridor lit along entire length — Done
- No unlit carved cells in connected network — Done
- Pending dangerous cave interior zero torches — Done
- Declined cave interior zero active torches incl. carved-path overlap — Done
- Headless `cave_carved_path_torches` passes with full-arm sampling — Done
- Windowed screenshot PNGs fresh + pixels inspected — Pending: manual tester owns visual evidence (headless run produces no PNGs)
- `[TORCH]` log line per recompute naming trigger and count — Done

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit 0; clean import, no script errors.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_carved_path_torches.json` — exit 0; harness `status=pass`; result at `.gen/harness/cave_carved_path_torches/result.json`. Raw stdout scanned: no SCRIPT ERROR / Invalid call / torch-related Parse Error. Pre-existing HudTheme.tres missing-texture errors only (also on clean HEAD).
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/declined_cave_torches_extinguish.json` — exit 0; harness `status=pass`.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json` — exit 1; harness `status=timeout`, unmet `underground.has_route_from == true` at action_index 4. Reproduced IDENTICALLY with all working-tree changes stashed (clean HEAD run, finished_at 2026-08-22T09:48:02) → pre-existing pathfinding/scenario failure on map_9 seed 1, unrelated to torch work. Torch code never executes before that step.

## Notes
- Focused scenario evidence in fresh stdout: pending cave 9102 count_in_cave==0 before confirm; after cross carve [TORCH] active=29 → 148 → 154 → 142 per recompute; corridor samples at ±2..±8 on both axes each ≥1 torch within radius 2.5; declined caves 9102/9103 interiors 0 torches; unlit_carved_in_cave==0 for open cave 9101.
- Tester handoff: the only failing plan command is `cave_pending_seals_entrance_instantly`, and it fails on baseline too — needs CaveSystem/RNG scoping outside our owned files. Visual-evidence criterion remains with manual tester (windowed run required).
