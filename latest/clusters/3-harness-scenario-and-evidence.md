# Cluster 3: harness-scenario-and-evidence

files: `tests/scenarios/cave_carved_path_torches.json`
dependencies: 1, 2
parallel: false

## Acceptance criteria
- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and their pixels have been inspected (manual tester).
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.

## Verification commands
- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"] plus ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"]
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Manual evidence: fresh windowed top-down PNGs must be captured and pixel-inspected during check; headless pass alone does not satisfy the screenshot criterion.
