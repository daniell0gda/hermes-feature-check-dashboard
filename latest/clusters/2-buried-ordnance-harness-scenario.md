# Cluster 2: buried-ordnance-harness-scenario

owned_files:
  - tests/scenarios/traps_buried_ordnance_progression.json

dependencies: 1
parallel: false

## Acceptance criteria

- A focused harness scenario passes headless with fresh evidence (`status: pass` in `.gen/harness/traps_buried_ordnance_progression/result.json`), covering: perk eligibility/grant, chained explosion triggering on an underground trap hit within radius, and no chain on non-underground targets.
- Windowed run of the same scenario captures screenshot checkpoint(s) at the chained-explosion moment showing the visible small-explosion VFX at the blast site.

## Verification

Run via `run_project_cmd`:

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_buried_ordnance_progression.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/display_damage_surface_parity.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
