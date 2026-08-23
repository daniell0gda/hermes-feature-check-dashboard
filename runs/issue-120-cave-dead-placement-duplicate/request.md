# Request: cave-dead-placement-duplicate (GitHub issue #120)

- Project key: `godot-td`
- Workspace: `poke-defense-godot/issue-cave-dead-placement-duplicate`
- Branch: `issue/cave-dead-placement-duplicate` (cut from origin/master @ d241462)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/120
- Type: cleanup / priority:low

## Problem

`scripts/game/CaveSystem.gd` carries a private `_find_suitable_cave_position(near_position, radius)`
that nothing calls. All placement goes through `CaveUtils.find_suitable_cave_position(...)`.
The dead copy is behind the live one: it checks spacing against existing caves only and misses the
hole/entry spacing rule (`min_entry_spacing`). Two same-named functions with different rules is a trap.

## Done when

1. `CaveSystem._find_suitable_cave_position` is removed.
2. A search for `_find_suitable_cave_position` under `scripts/` returns nothing.
3. `cave_discovery_chance`, `cave_discovery_long_carve` and `cave_discovery_pending_placement`
   scenarios still pass (fresh runs through `run_project_cmd`, runner key `godot-td`,
   workspace `poke-defense-godot/issue-cave-dead-placement-duplicate`).

## Redo notes

- Workers MUST use runner key `godot-td` and workspace `poke-defense-godot/issue-cave-dead-placement-duplicate`.
  Invented workspace names produce HTTP 422 chdir failures.
- Headless harness invocation: explicit scene argument before user args; never rely on project.godot main scene.
- Manual testing: this is dead-code removal with no visible user-facing change — `manual_testing: none` is appropriate.
