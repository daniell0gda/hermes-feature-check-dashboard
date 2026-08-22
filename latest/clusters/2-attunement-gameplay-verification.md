# Cluster 2: attunement-gameplay-verification

parallel: false
depends_on: 1

## Owned files
- `tests/scenarios/elemental_attunement.json`
- `.gen/harness/elemental_attunement/result.json`

## Acceptance criteria
- In a live map, one scripted fire-typed direct hit on a Water-typed enemy removes exactly twice the baseline HP when the fire attunement is owned compared to the same hit without the perk (deterministic harness arithmetic, no projectile flight involved).
- In a live map, one scripted water-typed direct hit on a Water-typed enemy resolves at 2.0x while a scripted water-typed direct hit on a Fire-typed enemy still resolves at its normal multiplier once the water attunement is owned.
- The existing effectiveness-path gameplay regression (`floodgate_saltwater_purge`) still passes end-to-end after the change.

## Verification
- Focused: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/elemental_attunement.json"])`
- Full test: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/floodgate_saltwater_purge.json"])`
- Typecheck/build: `run_project_cmd(project="godot-td", workspace="poke-defense-godot/issue-elemental-attunement", cmd=["godot","--headless","--path",".","--editor","--quit-after","300"])`
