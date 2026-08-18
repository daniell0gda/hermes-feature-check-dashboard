classification: fixable

## Verification summary
- Typecheck/build: run_project_cmd godot-td ... --editor --quit-after 300 → exit 0
- Focused test (panel): run_project_cmd ... traps_serrated_edges_panel.json → exit 0, Harness status=pass
- Full test (progression): run_project_cmd ... traps_serrated_edges_progression.json → exit 0, Harness status=pass
- All 7 criteria pass in harness but implementation uses forbidden type cast float() in scripts/ui/UI.gd:1474
- No runner/infra failure; missing tests or host godot not applicable here.

## Quality findings
- Clear violation in changed file only: type casts forbidden (coding_rules.md line 19)
- No other cross-cutting issues; changes surgical to the requested surface.

## Blockers
none

## Unverified items
none (all criteria executed via harness)