# Request

**Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/68
**Project:** godot-td
**Workspace:** godot-td/issue-enemyhealthbar-iconboss-path-mismatch
**Branch:** issue/enemyhealthbar-iconboss-path-mismatch

## Goal

Fix EnemyHealthBar boss icon path mismatch so IconBoss resolves.

`scripts/ui/EnemyHealthBar.gd` looks up `$SubViewport/Root/BarRow/IconBoss`, but `scenes/ui/EnemyHealthBar.tscn` nests it under `BarRow/IconBossWrapper/IconBoss`. Every health bar logs `Node not found` and the boss icon never shows.

## Acceptance

- Update the `@onready` path to include `IconBossWrapper` (or remove a vestigial wrapper) so IconBoss resolves without error.
- A boss enemy health bar shows the boss icon in a real run.
- No `Node not found` error for `IconBoss` in the log.

## Constraints

- Use `run_project_cmd` with project `godot-td` and workspace `godot-td/issue-enemyhealthbar-iconboss-path-mismatch`.
- Do not commit, push, merge, or close the issue.
- Follow `/opt/data/coding_rules.md`.
