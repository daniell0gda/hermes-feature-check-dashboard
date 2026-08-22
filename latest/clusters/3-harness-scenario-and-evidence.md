# Cluster 3: harness-scenario-and-evidence

- owned files: `tests/scenarios/cave_carved_path_torches.json`
- dependencies: 1, 2
- parallel: false

## Acceptance criteria

- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and their pixels have been inspected (manual tester).
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.

## Verification

run_project_cmd tokens (project `godot-td`, workspace `poke-defense-godot/issue-cave-carved-path-torches`):

- Focused test (headless): ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Focused test (windowed screenshots): ["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Full test: rerun the headless form with `res://tests/scenarios/declined_cave_torches_extinguish.json` and `res://tests/scenarios/cave_pending_seals_entrance_instantly.json`
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Visual evidence rule: windowed run required for screenshots; inspect fresh PNG timestamps and actual pixels under `.gen/harness/<scenario>/shots/`; stale screenshots or headless results are not visual evidence. Scan raw stdout/stderr separately from harness status.
