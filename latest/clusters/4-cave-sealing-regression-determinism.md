# Cluster 4: cave-sealing-regression-determinism

files: `scripts/testing/HarnessActions.gd`, `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessScenario.gd`, `tests/scenarios/cave_pending_seals_entrance_instantly.json`, `scripts/game/CaveSystem.gd`
dependencies: none
parallel: true

## Acceptance criteria
- When `cave_pending_seals_entrance_instantly.json` runs, no cave other than the scenario's own fixture (id 9003) is discovered, carved, locked, or sealed before the scenario's first route assertion completes, regardless of RNG rolls (harness suppression of RNG cave discovery is honored for every carve event).
- The scenario's initial hole-to-exit route assertion (`has_route_from == true`) succeeds deterministically on repeated runs from the same fixed scenario seed.
- At the moment the dangerous-cave confirmation question is presented (before any player decision), `has_route_from` for the scenario's hole returns false while the cave is marked pending, proving the entrance is physically sealed rather than logically locked.
- One second after sealing, the pending dangerous cave's interior contains zero active torches.
- Confirming "yes" restores the exact hole-to-exit route (`has_route_from == true`) and the cave interior again reports valid lighting (nonzero torch coverage within the confirmed cave).
- The scenario keeps its original assertions unchanged: no expectation, wait_for_condition, or threshold may be removed or loosened by the determinism or lighting fixes.

## Verification commands
- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json"]
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json"] run twice consecutively (both must pass), plus ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"]
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]
