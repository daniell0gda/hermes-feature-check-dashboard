# Coder report: implementation

## Changed files
- `scripts/game/visuals/BackdropEarth.gd` — mod (grounded continent rig; carried from prior iteration, verified this run)
- `scripts/game/Game.gd` — mod (harness observability: `debug_sample_backdrop_earth_transform`, `debug_backdrop_earth_rotation_invariant`, `backdrop_earth_grounded`)
- `scripts/testing/HarnessValues.gd` — mod (`backdrop_earth` expectation source: present/grounded/center_y/rotation_invariant)
- `tests/scenarios/backdrop_earth_visible.json` — mod (grounded + rotation-invariance expectations, transform samples a/b around 3s gap)
- `tests/scenarios/backdrop_earth_glint.json` — mod (backdrop_earth source expectations incl. grounded)
- `models/stylized_earth_in_clouds.glb`, `logs/balance/map_difficulty.csv` — mod (pre-existing worktree changes, not touched by this cluster)

## Criteria
- Exactly one grounded continent mesh + debug warning when absent — Done (`GROUNDED_CONTINENT="Continent_Africa"`, `_verify_continent_mesh()` push_warning)
- Continent apex flush at playable plane (center_y == 0) — Done (apex-based sink; harness center_y = -1.28e-05)
- Globe horizon inside normal gameplay camera frustum — implemented; windowed visual confirmation is manual-tester scope
- Grounded spin never starts — Done (rotation_invariant=true across 3.0s sample gap; `_place_earth_grounded` never calls `_start_earth_spin`)
- Debug `[BACKDROP EARTH]` grounding log line — Done (continent name + pos/scale/rot_deg per grounding event)
- Both focused scenarios pass headless green — Done
- Windowed backdrop look / surface screenshot — Pending: requires windowed build; headless worker cannot capture

## Commands and results
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/backdrop_earth_visible.json` — exit 0; `.gen/harness/backdrop_earth_visible/result.json` status=pass; all 6 expectations pass (present, grounded, center_y=-1.28e-05, rotation_invariant, map_id, current_layer); log line: `[BACKDROP EARTH] grounded continent=Continent_Africa pos=(-21.2, -83.7824, -51.76) scale=41.6 rot_deg=(15.39, 21.67, 83.15)`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/backdrop_earth_glint.json` — exit 0; status=pass; present/grounded/center_y all green
- Typecheck/build `godot --headless --path . --editor --quit-after 300` — exit 0; no script errors
- Full suite (plan command, DEVNULL-wrapped): ran ~50/95 scenarios in the 900 s runner cap before being killed; every scenario it reached passed except pre-existing unrelated failures/timeouts (cannon_bunker_buster*, cave_discovery_long_carve carve_tiles 961>=1000, fire_oil_slick/wildfire timeouts, projectiles_10x/2x/5x egg-death, progression_pick venom_miasma_bloom). None of these touch BackdropEarth.gd or the two backdrop scenarios; both backdrop scenarios re-ran and passed inside that sweep at 23:07.

## Notes
- Flush-at-y=0 contract depends on baking scale into `earth_rig.basis`; assigning `.scale` after `.basis` resets scale to unit and breaks center_y.
- Sink depth measured from GLB apex constant (2.014 native), so land — not ocean sphere — meets the board.
- The full-suite FAILs above are out-of-cluster regressions visible to the tester; they reproduce on the untouched baseline paths (cave/fire/projectiles/progression systems).
