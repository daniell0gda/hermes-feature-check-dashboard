# Cluster D — Verification and windowed smoke evidence

- `parallel: false`
- **Depends on:** Clusters A, B, and C.
- **Exclusive ownership:** `.gen` evidence/report files only; no production or test-source ownership.
- **Forbidden overlap:** Do not edit gameplay, UI, harness, or scenario files while verifying. Do not mutate GitHub.

## Verification steps

1. Confirm the starting revision and inspect `git status --short --branch`, `git diff --check`, and `git diff --stat` Hermes-side. Preserve unrelated master-worktree changes.
2. Run the Godot version and import/parse gate, then three fresh focused headless runs. Before/after each run verify the log grows and result is fresh; reject stale/malformed output.
3. Run three fresh windowed focused runs. Inspect every indicator/resume/finished/naptime PNG with vision; record actual readable text, state, layout, failure/recovery, and any variance. Do not call PNG creation visual proof.
4. Run existing smoke scenarios after locating the smoke reference. The requested `docs/tests/smoke-tests-reference.md` is absent at this revision; report that exact gap rather than silently treating another document as equivalent.
5. Report pass/fail/timeout separately for headless logic and windowed pixels, with result/log/shot paths and runner exit codes.

## Exact runner commands

```json
{"cmd":["godot","--version"],"project":"godot-td","workspace":"godot-td/issue-62"}
{"cmd":["godot","--headless","--path",".","--editor","--quit-after","300"],"project":"godot-td","workspace":"godot-td/issue-62"}
{"cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_autosave_resume.json"],"project":"godot-td","workspace":"godot-td/issue-62"}
{"cmd":["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_autosave_resume.json"],"project":"godot-td","workspace":"godot-td/issue-62"}
```

Git checks are explicitly Hermes-side, not runner commands: `git status --short --branch`, `git diff --check`, `git diff --stat`.

## Handoff

Final report must cite `.gen/plan.md`, all cluster files, starting revision, inspected paths, actual runner outputs, fresh result/log/PNG paths, and state that no production source was modified during planning.

