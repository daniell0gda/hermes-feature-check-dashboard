# Check Report: underground grid sized from map dimensions

## Classification
fixable

## Verdict
Focused harness and full suite pass via runner (exit 0). 8/9 criteria verified by automated harness results and logs. 1 criterion pending visual/windowed confirmation. No runner/infra blockers. Pre-existing HUD texture load errors present but do not affect harness pass. Quality: no new violations in changed files per coding_rules.md and CLAUDE.md (typed GDScript, small functions, debug logs present). 

## Runner Verification
- run_project_cmd project=poke-defense-godot workspace=poke-defense-godot/issue-underground-grid-fixed-at-20-units cmd=["godot","--version"] → exitCode:0 output:4.4.1.stable.official.49a5bc7b6
- run_project_cmd ... cmd=["godot","--headless","--path",".","--editor","--quit-after","300"] → exitCode:0 (import 9s)
- run_project_cmd ... cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/underground_grid_from_map.json"] → exitCode:0 status=pass (result.json all expectations pass; 23 conditions; logs confirm 20x20→40x40, 50x50→100x100, floor dims, out-of-grid carve log, custom_map edge carve)
- run_project_cmd ... cmd=["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/underground_diversion_proof.json"] → exitCode:0 (full suite pass, 66s)

## Criteria Evidence
- 20x20 grid 40x40 @0.5: verified (harness wait_for_condition + [UNDERGROUND] log)
- 50x50 grid 100x100 @0.5: verified (harness + log on custom_map load)
- Floor/boundary walls match: verified (underground.floor_width/depth conditions + debug planes)
- setup_as_menu_backdrop override removed: verified (no local override in Game.gd per coder report + backdrop test)
- Out-of-grid carve reports: verified ([UNDERGROUND] carve outside grid log + harness)
- custom_map edge carve: verified (harness action on custom_map beyond ±10)
- Init log: verified (log lines present)
- Out-of-grid log: verified (log present)
- Windowed edge tunnel visual: pending (headless only; requires windowed screenshot per request.md constraint)

## Changed Files Quality
- scripts/game/UndergroundSystem.gd, Game.gd, HarnessValues.gd, tests/scenarios/underground_grid_from_map.json, tests/menu/test_menu_backdrop_underground.gd: no quality violations (follows coding_rules: no casts, typed where applicable, surgical, debug logs, small funcs). Legacy texture warnings unrelated (pre-existing HudTheme missing assets).
- No scope creep, no duplicated patterns.

## Blockers / Unverified
- None (runner healthy). Unverified: windowed visual criterion only. No design_failure.

## Coder Reports Inspected
- implementation.md: 8 Done, 1 Pending (visual). Commands match.
- cluster 1 report: criteria and verification commands listed.

## Notes
All run via approved runner only. No host Godot. Status.md rewritten per spec. No dashboard publish.