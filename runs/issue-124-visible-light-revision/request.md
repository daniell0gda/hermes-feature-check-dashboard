# Issue 124 follow-up: fix cave sealing regression

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/124
Workspace: poke-defense-godot/issue-cave-carved-path-torches
Runner: godot-td

## Required scope
Preserve the existing issue-124 torch implementation, but fix the regression exposed by `tests/scenarios/cave_pending_seals_entrance_instantly.json`.

The scenario must reliably prove:
- A hole-to-exit route exists before the dangerous cave is discovered.
- The instant confirmation is requested, the cave entrance is physically sealed and the route becomes unavailable.
- Pending cave interior has zero torches.
- Confirming yes restores the exact route and valid cave lighting.

Previous check evidence: the regression failed at the initial route assertion on clean HEAD because RNG-discovered caves on map_9 carved/locked over the test corridor before the assertion. This is now required work, not an acceptable blocker. Diagnose and fix the smallest product/test-fixture boundary that makes this scenario deterministic without weakening its assertions or hiding real sealing behavior. Do not simply remove the regression scenario or reduce assertions.

## Verification
Use native Godot through run_project_cmd. Run editor/import gate, issue-124 focused torch scenario, `declined_cave_torches_extinguish.json`, and `cave_pending_seals_entrance_instantly.json`. Scan raw stdout/stderr independently for script errors, parse errors, failed resources, and invalid calls. Then run the required windowed top-down issue-124 scenario and inspect fresh PNGs; stale screenshots/headless screenshot results are not proof.

Do not commit, push, merge, or close the issue.


## REVISION (Daniel confirmed): visual lighting still fails
The windowed top-down screenshot dungeon_cross_carve_lit.png shows only PARTS of
the carved cross lit; three arms read fully dark. Torch placement counts pass,
but the rendered light is not visible along the corridors. Required now:
- Make carved corridors VISIBLY lit end to end in gl_compatibility (llvmpipe):
  verify each torch's OmniLight actually renders on corridor floor at distance
  (radius 2.5 may be clipped by range_item/attenuation or the light may sit
  inside walls), increase effective visual coverage (e.g. larger radius/energy,
  light positioned into open corridor space, additional floor lights), and prove
  with fresh windowed PNGs inspected pixel-by-pixel.
- Do not weaken headless count_near/unlit assertions.
