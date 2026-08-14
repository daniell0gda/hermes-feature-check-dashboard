# Full smoke docs report

## Outcome

Created the required smoke-test source of truth at `docs/tests/smoke-tests-reference.md`.

## Documented

- Godot 4.4.1 and the `godot-td` / `godot-td/issue-62` runner boundary.
- Exact tokenized native runner commands for `smoke_placement`, `smoke_tower_roster`, and `smoke_underground_visible`.
- The purpose, assertions, evidence path, and explicit non-claims for each baseline smoke scenario.
- The separate windowed command and visual inspection requirement for underground visibility.
- Fresh unique suffix, result, log, timestamp/size, stale-artifact, malformed-result, and single-instance rules.
- The limit that headless screenshots are not visual evidence.
- Issue #62 focused seed, real second-process Continue, focused headless, and focused visual scenarios, without treating them as baseline smoke tests.

No smoke scenario JSON file needed a documentation-only correction: every referenced file exists and the documented harness paths match the existing scenario files. No production, harness, issue-scenario, or other documentation files were edited.

## Verification

- Parsed all JSON runner blocks in the new document. Found 9 runner blocks; all use project `godot-td` and workspace `godot-td/issue-62`.
- Checked every documented scenario path. All 7 referenced scenario files exist.
- `git diff --check -- docs/tests/smoke-tests-reference.md .gen/coder-reports/full-smoke-docs.md` passed with no output.
- Project-runner preflight: `run_project_cmd` with `{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--version"]}` returned exit 0 and `4.4.1.stable.official.49a5bc7b6`.
- Released the disposable project worker after the final project command.

The worktree contains pre-existing issue #62 source, harness, and focused-scenario changes. They were preserved; no GitHub mutations, commit, push, merge, or issue closure was performed.
