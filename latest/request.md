# Request: Ground the playable map on a continent of the existing stylized earth model

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/137
Slug: `earth-continent-map-integration`
Project: poke-defense-godot
Workspace: `/workspace/git-workspaces/poke-defense-godot/issue-earth-continent-map-integration`
Branch: `issue/earth-continent-map-integration`

## Problem

The game uses the existing stylized earth model (`res://models/stylized_earth_in_clouds.glb`,
driven by `scripts/game/visuals/BackdropEarth.gd`) only as a distant backdrop globe floating
off to the side of the board. The playable map field floats disconnected above/beside the
planet instead of belonging to it.

## Requirements (from issue + follow-up comment)

1. Pick **one continent** from the earth model's landmasses (the GLB contains named continent
   meshes — see `_continent_color()` in `BackdropEarth.gd`, e.g. Africa/Asia/Americas names)
   and position/orient the globe so the playable map's field lies flat on that continent.
2. The gameplay plane should visually merge with the chosen continent: scale relationship,
   terrain color continuity with the continent material, no visible gap or floating edge
   between board and globe surface.
3. Keep the rest of the globe (other continents, oceans, clouds, atmosphere) visible so the
   player still reads it as Earth; no regressions to the current backdrop look.
4. **The earth model must NOT rotate** once the map sits on it. Disable/stop the existing spin
   (`_start_earth_spin()` / `earth_spin_seconds` in `BackdropEarth.gd`) for the grounded
   configuration — a spinning planet would move the battlefield.

## Acceptance criteria

- [ ] A specific continent mesh is selected and documented (name of the GLB mesh used).
- [ ] Earth model positioned/scaled so the playable map field sits flush on the chosen
      continent, no floating gap, from the normal gameplay camera.
- [ ] Continent terrain colors/material around the board blend with the map field.
- [ ] Other continents/ocean/clouds remain visible; no visual regressions to the current
      cloud/atmosphere backdrop.
- [ ] Earth spin is disabled in the grounded configuration (globe does not rotate during play).
- [ ] Verified in-game with windowed screenshots from the normal gameplay camera showing the
      map sitting on the continent. Headless-only verification is not sufficient — this is
      player-facing visual work (`manual_testing: required`).

## Notes

- Key file: `scripts/game/visuals/BackdropEarth.gd` (placement in `_place_earth()`, spin in
  `_start_earth_spin()` / `_stop_earth_spin()`, continent naming in `_continent_color()`).
- Check `scenes/Game.tscn` / how `BackdropEarth.configure_for_map()` is invoked for map size.
- Manual testing gate: this is visible world work → manual-tester with windowed screenshots
  is required.

## Revision feedback (Daniel, after screenshot review of iteration 2)

Screenshot review of the grounded build shows two defects that ARE required scope:

1. **Globe not visible near the map.** `_place_earth_grounded()` places the rig at
   `pos=(-21.2, -83.2, -51.76)` — sunk a full body radius below the plane AND pushed
   ~52 units behind the board. It is entirely out of camera frame and buried under
   the ground. The globe must be raised and brought close so its horizon wraps the
   visible play area.
2. **The issue's core goal still missing visually.** The map renders as an isolated
   floating tile against plain sky; no continent terrain reads around/underneath the
   board. Flush placement on Continent_Africa must be visible from the normal gameplay
   camera: continent fills the space around/under the field, no floating gap.

Also required (carried over):
3. Update `tests/scenarios/backdrop_earth_visible.json` (+ glint scenario) expectations
   for the grounded configuration so the harness can pass legitimately.
4. Windowed screenshot evidence from the normal gameplay camera proving 1–2 are fixed,
   plus rotation-invariance (globe does not rotate).
