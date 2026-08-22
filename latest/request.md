# Issue 124: cave-carved-path-torches

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/124
Project: godot-td
Workspace: poke-defense-godot/issue-cave-carved-path-torches

## Goal
Fix cave torch placement so every carved cave-path corridor is visibly lit end to end, including newly carved corridors. Keep pending/declined dangerous caves completely dark.

## Required verification
- Fresh plan, code, and check from the beginning; do not trust prior reports.
- Use native Linux Godot through run_project_cmd.
- Headless scenario must sample the full length of all four arms of a 2-by-18 plus 18-by-2 cross at roughly 2-unit intervals, not only the cave room.
- Verify new connected corridor coverage.
- Verify zero torches in pending and declined cave interiors, including overlap with carved paths.
- Scan raw output for parse/resource errors separately from harness status.
- Manual testing is required: windowed, top-down orthographic screenshots before/after the cross carve and after decline. Inspect actual PNG pixels. A stale screenshot or headless result is not evidence.

## Known previous failures to investigate
Previous implementation passed weak headless checks but Daniel rejected the screenshots: only portions of the cross were visibly lit. Previous r2/r3 failed because code/check used a rejected model. Previous r4/r5 had stale evidence and manual-tester/provider failures. Do not mark visual pass unless fresh PNG timestamps and inspected pixels prove the full carved cross is lit end to end.

Do not commit, push, merge, or close the issue.
