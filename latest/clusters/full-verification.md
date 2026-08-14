# Cluster full-verification — final evidence, visual inspection, and diagnostics

- **parallel:** false
- **depends on:** all implementation, harness, scenario, and smoke-doc clusters.
- **exclusive ownership:** `.gen` evidence/report files only, including fresh harness result roots and a final verification report. Do not edit production source, tests, docs, or scenarios.
- **forbidden overlap:** no code fixes, no scenario weakening, no GitHub/dashboard/Git mutations.

## Verification sequence

1. Hermes-side preflight: inspect `git status --short --branch`, current diff, and single-instance state. Confirm changed paths match ownership and preserve all pre-existing issue-62 changes.
2. Through `run_project_cmd`, run version and editor/import/parse gate. Scan output for `Parse Error`, `Failed loading resource`, `Failed to load script`, missing-node/duplicate-signal diagnostics, renderer/audio fallback, and shutdown ObjectDB/RID leaks. Classify changed-path diagnostics separately from pre-existing noise.
3. Run the seed→Continue pair three fresh sequential times in headless mode. Each seed must write a unique save fixture/result suffix; each Continue must start a new MainMenu process and invoke the real Continue path. Read each fresh result and compare exact active enemy/spawner snapshots.
4. Run the focused headless scenario three fresh sequential times. Assert all phases, exact-once clear behavior, failure/recovery, finished canonical state, and real naptime.
5. Run the focused visual scenario three fresh sequential times without `--headless`. Inspect every fresh PNG with vision: Saving, Save failed, recovered Save safe, active-wave/resume, naptime, post-wave/before-next, and victory/finished. Record dimensions, readable labels, clipping, stale overlays, and diagnostics. A PNG file alone is not proof.
6. Run all documented smoke scenarios fresh. Read `result.json` and logs; do not use smoke results to claim exact active-wave restoration.
7. Run Hermes-side `git diff --check`, `git diff --stat`, and `git diff --name-only`; verify only the intended implementation/test/doc paths plus `.gen` artifacts changed.

## Exact command forms

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_full_headless.json","--harness-run=headless-1"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_full_visual.json","--harness-run=windowed-1"]}
```

Repeat with `headless-2`, `headless-3`, `windowed-2`, `windowed-3` and the corresponding seed/continue suffixes. Use the approved runner for every Godot/Python project command; use Hermes-side tools for Git checks and image inspection.

## Acceptance and bounded revision

Final verification passes only when all issue criteria and the smoke reference are satisfied, all six focused runs are fresh and valid, and every required PNG was inspected. One bounded revision may rerun the smallest failing case; a second may rerun the full 3×3 matrix. Stop after that and report exact failing evidence; never pass with stale results, a fake phase label, aggregate enemy counts, or the known finished-state inconsistency.

Release the worker after the final runner command with `release_project_worker(..., remove=true)`. No commit, push, merge, issue closure, GitHub mutation, or dashboard mutation.