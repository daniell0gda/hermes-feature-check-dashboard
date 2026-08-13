# Issue #62 checker status

## Classification

design_failure

## Verdict

The focused autosave lifecycle implementation is runnable and its represented headless assertions pass across three fresh runs. The windowed run also passes structurally and proves readable Save safe/Save failed/recovery pixels. The issue is **not acceptable as pass** because required acceptance behavior is unverified or not exercised: process-boundary Continue, exact live-enemy restoration, both real clear producers together, a real finished/naptime visual checkpoint, and visual Saving coverage. The requested `docs/tests/smoke-tests-reference.md` is absent.

## Fresh evidence

- Editor/import gate: exit 0.
- Focused headless scenario: three fresh exit-0 runs; each reported `status=pass`.
- Focused windowed scenario: one fresh exit-0 run; reported `status=pass`; five PNGs.
- Structured result: `.gen/harness/issue_62_autosave_resume/result.json`; seed `62062`; all 10 final expectations pass.
- PNG directory: `.gen/harness/issue_62_autosave_resume/shots/`.
- Final Git checks: `git diff --check` exit 0; expected issue-62 source/test scope remains.

## Criteria state

- **Indicator lifecycle:** headless transitions and failure/recovery pass; safe/failed/recovered visuals pass; Saving and finished/naptime visuals are incomplete.
- **Checkpoint phase/reason/generation:** represented phases and reasons are recorded in ordered actions; generations reach 14 monotonically.
- **Last-good preservation:** deterministic failure/recovery seam passes, but file preservation is not independently checked immediately after the failed write.
- **Post/before-next semantics:** checkpoint records exist, but the scenario directly invokes saves rather than driving and asserting a real next-wave transition/increment.
- **Continue:** no second process/menu Continue path; unverified.
- **Live enemies:** active-wave screenshot has an enemy, but no exact identity/position/health/spawner restoration after restart; unverified.
- **Both clear producers:** production handlers converge on one guard, but no fresh scenario fires both producers; unverified.
- **Smoke reference:** `docs/tests/smoke-tests-reference.md` is missing.
- **Finished/naptime:** final PNG is visibly still Wave 1/4 active gameplay, so the requested visual checkpoint is not proven.

## Diagnostics

No Parse Error, Failed loading resource, or Failed to load script appeared in fresh focused output. Recurring diagnostics were classified separately in `.gen/check.md`: missing Tower1/IconBoss nodes, duplicate `layer_changed` connection, renderer/audio fallback warnings, and shutdown leak diagnostics. Exit 0 and harness pass do not erase these diagnostics.

## Blockers and next action

This is a design/evidence failure rather than a runner outage. The owner should add the missing deterministic harness seams/transition driving, then rerun the complete fresh matrix (including three windowed runs if required), inspect every PNG, and resolve or explicitly scope the recurring changed-path duplicate-signal diagnostic before requesting another checker.

No production source or scenario file was edited by the checker. No commit, push, merge, issue closure, or GitHub mutation was performed.