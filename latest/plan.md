# Acceptance Plan: issue-124-cave-carved-path-torches

## Verification

All project commands run through Hermes `run_project_cmd` (project `godot-td`, workspace `poke-defense-godot/issue-cave-carved-path-torches`).

- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"] then repeat the same token form with `cave_pending_seals_entrance_instantly` (the focused scenario above plus these two regression scenarios constitute the full torch/cave harness pass)
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Raw-output rule: scan fresh runner stdout/stderr separately from harness status for `Parse Error`, `SCRIPT ERROR`, `Failed loading resource`, `Invalid call`; a harness `status=pass` alone is not acceptance evidence.

## Clusters

1. torch-coverage-along-carved-corridors — files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd` — depends on: none
- After carving a 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius.
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
2. dark-pending-and-declined-caves — files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`, `scripts/game/CaveSystem.gd` — depends on: none
- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.
3. harness-scenario-and-evidence — files: `tests/scenarios/cave_carved_path_torches.json` — depends on: 1, 2
- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and their pixels have been inspected (manual tester).
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.

## Criteria

- After carving a 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius.
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.
- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and their pixels have been inspected (manual tester).
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.

manual_testing: required — windowed top-down orthographic screenshots before/after the cross carve and after decline; actual PNG pixels inspected; stale screenshots or headless results are not visual evidence.
