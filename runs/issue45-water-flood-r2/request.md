# Request: water-conductive-flood-wet-splash (issue #45)

Project: godot-td
Git workspace: godot-td/issue-water-conductive-flood-wet-splash (/workspace/git-workspaces/godot-td/issue-water-conductive-flood-wet-splash)
Branch: issue/water-conductive-flood-wet-splash (cut from origin/master @ b5d75ae)
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/45
Runner: project key `godot-td`, workspace `godot-td/issue-water-conductive-flood-wet-splash` (do NOT invent workspace names).

## Feature

New Unique progression perk `water_conductive_flood` (Water tower): Water splash hits apply
Wet status in a small radius around the hit target, not only to the direct target. Reuse the
small-radius AoE-application pattern proven in `IceTower._apply_cone_effects`. The hit's existing
splash effect should visibly cover that radius (same small-radius visual approach IceTower uses),
not just silently flag enemies. Wet renders per-enemy via `scripts/ui/EnemyHealthBar.gd` status
icons — no new asset needed.

## Acceptance criteria

1. A perk definition `water_conductive_flood` exists and is obtainable like other Water Uniques.
2. With the perk, a Water projectile hit applies Wet to enemies within a small radius of the hit
   target (multiple enemies verified Wet, not just the direct target).
3. Without the perk, behavior is unchanged (single-target Wet only) — no regression.
4. The splash effect visually covers the radius on hit.
5. Editor import gate passes; focused headless harness proves multi-enemy Wet application;
   windowed screenshot evidence shows the splash radius covering nearby enemies.

## Manual testing

manual_testing: required — visible player-facing perk with an AoE splash moment. Include overall
UI-sanity pass (`ui_feels_broken: yes|no`) on every final screenshot.

## Notes

- Fresh worktree; `.gen/` starts clean this run.
- Follow `/opt/data/coding_rules.md` and project context files.
