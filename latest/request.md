# Issue #98 — Cave carving discovery chance is unreliable

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/98
Project: godot-td
Workspace: godot-td/issue-cave-discovery-chance-unreliable
Worktree: /workspace/git-workspaces/godot-td/issue-cave-discovery-chance-unreliable
Branch: issue/cave-discovery-chance-unreliable (from origin/master @ 8406c6e)
Do not commit, push, merge, or close the issue.

## Checker / runner rule

Every Godot command must go through `run_project_cmd` with
`project=godot-td` and `workspace=godot-td/issue-cave-discovery-chance-unreliable`.
Host-shell `godot` (exit 127) is invalid evidence. Re-run via the runner.

Editor gate:
`["godot","--headless","--path",".","--editor","--quit-after","300"]`

Gameplay harness (always pass the scene before user args):
`["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/<name>.json"]`

Inspect raw runner stdout/stderr for Parse Error / Failed loading resource / Invalid parameter separately from harness status.

Follow `/opt/data/coding_rules.md` and project `CLAUDE.md`. Surgical typed GDScript only.

## Problem

Cave discovery while carving is not reliable, and the roll does not match the intended feel.

Observed: setting **spawner discovery chance to 1.0** still required carving a very long distance before anything was discovered. That should be nearly immediate if the configured chance is actually the discovery roll.

Current path (`scripts/game/CaveSystem.gd` `on_carving_completed` / `_check_cave_discovery`, `scripts/utils/CaveUtils.gd`):

- A discovery roll only happens after `cooldownTiles` more tiles are carved. A failed roll (or a silent skip) resets that counter, so the player must carve another full cooldown before the next attempt.
- Discovery uses `caves.spawn.chance` (`cave_config.discovery_chance`). `spawner_chance` is a **content** weight (spawner vs chest/enemies/boss), not whether a cave appears. Setting "spawner discovery" to 1 can therefore leave the real discovery chance at the default (~0.15) and feel like a broken dice roll.
- Even after a successful roll, `find_suitable_cave_position` can return `Vector3.ZERO` (spacing / attempts) and **silently skip** creating a cave. The cooldown is already spent.
- Docs (`CAVE_SYSTEM_README.md`) say chance goes **up** the more the player carves. The code uses a **flat** chance and does not scale with carved tiles or caves already found.

Intended curve (product):

- Early carving should make discovery relatively easy.
- As the player carves more **and** as more caves have already been discovered, the chance should drop a little — not stay flat, and not get easier.

## Done when (do not downgrade)

- Confirm and fix the roll: `discovery_chance >= 1.0` discovers a cave on the first eligible check (after any intended cooldown), unless `maxCaves` is already reached. No silent skip after a successful roll without a visible/logged reason.
- Separate clearly: **discovery chance** (will a cave appear?) vs **spawner chance** (what is inside?). UI / map config / debug logs must name the field that was actually used.
- Failed placement after a successful roll must not silently eat the attempt (retry nearby, or do not consume the cooldown).
- Discovery chance starts relatively high / easy, then decreases a little with (a) tiles carved this run and (b) caves already discovered. Document the curve and replace the README "more carving = higher chance" line.
- Focused test or `game-test` scenario: chance=1 discovers promptly; chance=0 never discovers; later discoveries are rarer than the first after the same cooldown.

Preserve exact acceptance criteria. Do not fabricate product states for testing.

## Known config split

- `Balance.caves.default_config.discovery_chance` (0.15) and map `caves.spawn.chance` / `cooldownTiles` drive discovery.
- `Balance.caves.spawning.spawner_chance` and map `spawningCaves.spawner_chance` are content weights.
- UI loader already maps `cave_discovery_chance` vs `cave_spawner_chance` in `MapCreatorConfigLoader.gd`; keep those names honest in UI/debug logs.

## Lifecycle

Do not commit, push, merge, or close the issue. Report verdict + evidence only.
