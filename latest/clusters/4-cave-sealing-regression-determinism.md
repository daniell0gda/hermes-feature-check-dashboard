# Cluster 4: cave-sealing-regression-determinism

Issue #124 follow-up: make `cave_pending_seals_entrance_instantly.json` deterministic without weakening its assertions. Known failure mode (clean HEAD too): map_9 has `caves.spawn.chance = 0.8`; with scenario seed 1, RNG-discovered caves can be placed and sealed over the hole↔exit corridor before the scenario's first route assertion, so `has_route_from == true` fails at action_index 4. The smallest fix is expected at the product/test-fixture boundary — e.g. suppressing RNG cave discovery for this harness run or making discovery deterministic under the scenario seed — NOT by deleting or loosening scenario assertions. Preserve all existing issue-124 torch behavior.

files: `scripts/testing/HarnessActions.gd`, `scripts/testing/HarnessValues.gd`, `tests/scenarios/cave_pending_seals_entrance_instantly.json`, `scripts/config/maps/map_9.json`
dependencies: none
parallel: true

## Acceptance criteria
- When `cave_pending_seals_entrance_instantly.json` runs, no cave other than the scenario's own fixture (id 9003) is discovered, carved, locked, or sealed before the scenario's first route assertion completes, regardless of RNG rolls.
- The scenario's initial hole-to-exit route assertion (`has_route_from == true`) succeeds deterministically on repeated runs from a clean import cache, with the same fixed scenario seed.
- At the moment the dangerous-cave confirmation question is presented (before any player decision), `has_route_from` for the scenario's hole returns false while the cave is marked pending, proving the entrance is physically sealed rather than logically locked.
- One second after sealing, the pending dangerous cave's interior contains zero active torches.
- Confirming "yes" restores the exact hole-to-exit route (`has_route_from == true`) and the cave interior again reports valid lighting (nonzero torch coverage within the confirmed cave).
- The scenario keeps its original assertions unchanged: no expectation, wait_for_condition, or threshold may be removed or loosened by the determinism fix.

## Verification commands
- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json"]
- Full test: rerun the focused token form with `cave_pending_seals_entrance_instantly.json` twice more consecutively (all runs pass), then ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"] and ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"] as regression guards
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Raw-output rule: scan fresh stdout/stderr independently for `SCRIPT ERROR`, `Parse Error`, `Invalid call`, and unexpected `Failed loading resource` (pre-existing HudTheme missing-texture noise on clean HEAD is not a failure).
