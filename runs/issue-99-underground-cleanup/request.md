# Request: #99 Underground._cleanup() leaks dynamically-named cave nodes

Project: poke-defense-godot (runner key `godot-td`)
Workspace: `poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes`
Branch: `issue/underground-cleanup-leaks-cave-nodes`
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/99
Slug: `underground-cleanup-leaks-cave-nodes`

## Problem

`UndergroundSystem._cleanup()` only frees a fixed allowlist of children under `Underground`. Dynamically added cave visuals (`Chest_Cave*`, other cave/spawner nodes) survive re-init. `CaveDarkness_*` already has a prefix sweep; generalize cleanup instead of enumerating leaked types one bug at a time.

## Done when

- `_cleanup()` sweep is generalized (free any Underground child not in a small keep-list, or a broader prefix match covering `Chest_Cave_` / `CaveDarkness_` / other dynamic cave names).
- Game-test scenario: create a cave chest fixture, force map re-init (load_map twice or equivalent), confirm no duplicate chest node afterward.

## Constraints

- Use `run_project_cmd` with project `godot-td` and workspace `poke-defense-godot/issue-underground-cleanup-leaks-cave-nodes`.
- Follow `/opt/data/coding_rules.md`.
- Do not commit, push, merge, or close the issue.
- Native Linux Godot verification through the runner.
