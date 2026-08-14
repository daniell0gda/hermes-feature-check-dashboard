# Issue #62 revision 1

## Checker classification

design_failure

## Failed acceptance criteria

- Process-boundary Continue/reload is not exercised.
- Exact live-enemy/spawner restoration is not proven after restart.
- Both real clear signal producers are not fired together; duplicate transition/increment is unverified.
- Finished/naptime screenshot is still active Wave 1/4 gameplay.
- No visual Saving checkpoint was captured.
- `docs/tests/smoke-tests-reference.md` is absent, so the required smoke matrix cannot be selected from source-of-truth documentation.
- Last-known-good file preservation is reviewed but not independently checked immediately after a failed write.
- Windowed repetition is one run, not the three sequential runs required by the plan.

## Scope for revision

Preserve existing production/UI/harness work. Add only the smallest deterministic harness/runtime seams needed to exercise process-boundary save/load, both clear producers, genuine finished/naptime transitions, visual Saving capture, and immediate failed-write file preservation. Do not weaken criteria or claim exact enemy restoration from aggregate wave counts. If exact enemy reconstruction cannot be implemented and verified within this bounded revision, classify it explicitly as blocked/design failure rather than silently narrowing the issue.

## Required evidence

- Fresh process-boundary Continue/reload result with map, phase, wave, and save schema evidence.
- Fresh duplicate-clear probe invoking both real producers and proving one completion transition/increment.
- Fresh failed-write checkpoint showing prior file bytes/generation remain unchanged before recovery.
- Fresh windowed Saving, finished, and naptime screenshots inspected by checker.
- Three sequential fresh headless and three sequential fresh windowed focused runs.
- Smoke-reference path located or absence recorded as an explicit repository blocker.

## Stop condition

Do not spend another speculative code cycle if the runner cannot start a second process or if the game cannot reach finished/naptime deterministically. Preserve evidence and return `blocked` with the exact infrastructure/design gap.

## Dashboard

Continuation dashboard run: `issue-62-cb495eb68a65`. Checker phase was published before this revision dispatch. Revision phase must use a unique dashboard task ID and finish on the same persistent dashboard instance.

No commit, push, merge, or issue closure is authorized by this revision handoff.

## Next action

Focused re-plan, then one bounded revision-code cluster followed by a fresh checker.
