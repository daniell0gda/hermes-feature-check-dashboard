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
