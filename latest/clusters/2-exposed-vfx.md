# Cluster 2: exposed-vfx

- Files: `scripts/game/actors/effects/EffectsManager.gd`, `scripts/game/actors/effects/ExposedVFX.gd`, `scripts/game/status/ExposedStatus.gd`, `tests/scenarios/exposed_plating_vfx.json`
- Dependencies: none
- Parallel: true

## Acceptance criteria

- While the Exposed status is active on an enemy, a visible cracked-shield emissive overlay effect is present on that enemy, following the same lazy-instantiation pattern as the existing burn/oil VFX.
- When the Exposed status expires or the enemy dies, the overlay is removed from that enemy with no leftover nodes or leaked resources.

## Verification

Run via `run_project_cmd` (`project=godot-td`, `workspace=poke-defense-godot/issue-exposed-plating`):

```json
["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/exposed_plating_vfx.json"]
```

Status (fresh, post-rebase 2026-08-24): pass, exit 0 — `.gen/harness/exposed_plating_vfx/result.json`. Headless proves state only; player-visible proof belongs to cluster 3.
