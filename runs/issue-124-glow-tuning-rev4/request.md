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


## REVISION 2 (hard pixel evidence): warm light still confined to one region
Numeric analysis of dungeon_cross_carve_lit.png (1920x1080): warm (torch) pixels
cluster ONLY around x=1000-1400 / y=450-750 (~1000 of 1226 warm pixels). The rest
of the carved cross has NO measurable warm light. The previous "visibly lit"
verdicts were wrong — vision-model summaries cannot be trusted for this gate.
Required:
- Fix actual rendered light coverage along the full carve (investigate whether
  lights render at all outside that region — e.g. per-light range/attenuation,
  light count culling, or lights parented to nodes that end up off-camera).
- Verification gate MUST be numeric, not vision-summary: script a brightness/
  warm-pixel measurement along each arm of the cross in the PNG and require
  warm-light presence in every arm segment before calling pass.


## REVISION 3 (Daniel): floor glow is way too strong
The current FloorGlow washes the entire carved path WHITE — it looks like a
floodlight, not torches. Required look: small, warm, localized light pools at
each torch (like real torches), with corridors visibly lit but keeping dark
contrast between pools. Reduce glow radius/intensity/additive strength so each
torch reads as a distinct warm pool; do NOT cover every carved pixel with glow.
Keep headless assertions unchanged. Prove with fresh windowed PNGs + numeric
measurement showing distinct per-torch pools (not uniform whiteness) and no
dark carved stretches.
