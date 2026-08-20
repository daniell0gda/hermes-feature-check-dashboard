# Request: map-available-tower-perks

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/119
Project: poke-defense-godot (runner key godot-td)
Workspace: poke-defense-godot/issue-map-available-tower-perks
Branch: issue/map-available-tower-perks
Request ID: issue-119-map-available-tower-perks

## Feature
Show tower/element/mechanic chest perks when the matching tower is available on the current map (build/place roster), not only when at least one instance is already placed. Follow-up to #64.

## Acceptance
- Non-Gold/Common tower/element/mechanic perks are offered when the matching tower is available on the current map, even with zero instances placed.
- Perks for towers that are not available on the current map stay filtered out (same intent as #64).
- Gold and Common rewards stay eligible regardless of tower coverage.
- Compatibility still comes from reward metadata, not per-reward-name special cases.
- Chest generation still has a deterministic fallback if filtering would empty the set.
- Tests cover: map-available unplaced tower → perk shown; map-unavailable tower → perk hidden; Gold/Common unchanged.

## Constraints
- Use run_project_cmd with project=godot-td and workspace=poke-defense-godot/issue-map-available-tower-perks.
- Visible chest/UI work: manual-tester windowed screenshots required if player-facing.
- Do not commit, push, merge, or close the issue.
