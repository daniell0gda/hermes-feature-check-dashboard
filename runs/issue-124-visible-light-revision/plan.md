# Acceptance Plan: issue-124-cave-carved-path-torches (follow-up: cave sealing regression determinism)

## Verification

All project commands run through Hermes `run_project_cmd` (project `godot-td`, workspace `poke-defense-godot/issue-cave-carved-path-torches`). Token arrays are passed verbatim as run_project_cmd commands.

- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json"]
- Full test: rerun the focused token form twice more consecutively (all runs pass), then ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"] and ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"] as regression guards
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Raw-output rule: scan fresh runner stdout/stderr independently from harness status for `Parse Error`, `SCRIPT ERROR`, `Failed loading resource` not attributable to the pre-existing HudTheme missing-texture noise, and `Invalid call`; a harness `status=pass` alone is not acceptance evidence. Exit-time dummy-renderer leak warnings are engine shutdown noise on clean HEAD, not failures.

manual_testing: required

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
4. cave-sealing-regression-determinism — files: `scripts/testing/HarnessActions.gd`, `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessScenario.gd`, `tests/scenarios/cave_pending_seals_entrance_instantly.json`, `scripts/game/CaveSystem.gd` — depends on: none
- When `cave_pending_seals_entrance_instantly.json` runs, no cave other than the scenario's own fixture (id 9003) is discovered, carved, locked, or sealed before the scenario's first route assertion completes, regardless of RNG rolls (harness suppression of RNG cave discovery is honored for every carve event).
- The scenario's initial hole-to-exit route assertion (`has_route_from == true`) succeeds deterministically on repeated runs from the same fixed scenario seed.
- At the moment the dangerous-cave confirmation question is presented (before any player decision), `has_route_from` for the scenario's hole returns false while the cave is marked pending, proving the entrance is physically sealed rather than logically locked.
- One second after sealing, the pending dangerous cave's interior contains zero active torches.
- Confirming "yes" restores the exact hole-to-exit route (`has_route_from == true`) and the cave interior again reports valid lighting (nonzero torch coverage within the confirmed cave).
- The scenario keeps its original assertions unchanged: no expectation, wait_for_condition, or threshold may be removed or loosened by the determinism fix.

## Criteria

- After carving a 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius.
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.
- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and their pixels have been inspected (manual tester).
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.
- When `cave_pending_seals_entrance_instantly.json` runs, no cave other than the scenario's own fixture (id 9003) is discovered, carved, locked, or sealed before the scenario's first route assertion completes, regardless of RNG rolls (harness suppression of RNG cave discovery is honored for every carve event).
- The scenario's initial hole-to-exit route assertion (`has_route_from == true`) succeeds deterministically on repeated runs from the same fixed scenario seed.
- At the moment the dangerous-cave confirmation question is presented (before any player decision), `has_route_from` for the scenario's hole returns false while the cave is marked pending, proving the entrance is physically sealed rather than logically locked.
- One second after sealing, the pending dangerous cave's interior contains zero active torches.
- Confirming "yes" restores the exact hole-to-exit route (`has_route_from == true`) and the cave interior again reports valid lighting (nonzero torch coverage within the confirmed cave).
- The scenario keeps its original assertions unchanged: no expectation, wait_for_condition, or threshold may be removed or loosened by the determinism fix.

## Verification status (fresh run_project_cmd evidence)

- Editor/import gate: exit 0, no script/parse errors beyond pre-existing HudTheme missing-texture noise.
- `cave_pending_seals_entrance_instantly`: two consecutive runs, both `status=pass exit=0`, with `[CAVE] discovery suppressed: harness scenario forbids RNG cave discovery` on every carve event and the full seal → dark-torches → confirm-yes → route-restored sequence logged.
- `cave_carved_path_torches`: `status=pass exit=0` (`[TORCH] cave-path update active=154` full-cross coverage observed).
- `declined_cave_torches_extinguish`: `status=pass exit=0`.
- Remaining manual item: fresh windowed top-down screenshots with pixel inspection (cluster 3 criterion).
