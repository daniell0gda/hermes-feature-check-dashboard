# Cluster: Issue #13 editor-log freshness and Godot collision

- **parallel:** `false`
- **Why:** The script and reference document share one behavioral contract; documentation must follow the final choice of per-run evidence vs freshness-gated shared evidence. Run sequentially in one worker; do not dispatch additional workers from the plan phase.
- **Depends on:** existing `Invoke-Godot` redirection in `.claude/skills/game-test/scripts/Run-Scenario.ps1`; no source/gameplay dependency.

## Exact file ownership

Implementation worker may modify only:

1. `.claude/skills/game-test/scripts/Run-Scenario.ps1`
   - Make `-Editor` inspect the current invocation's `.gen/harness/_logs/editor.out.log` and `.err.log` (preferred), or freshness-gate every shared-log read using pre/post length and mtime.
   - Preserve the existing error patterns and exit semantics.
   - Treat missing current-run output as an execution error, never a clean pass.
2. `.claude/skills/game-test/REFERENCE.md`
   - Document confirmed cross-worktree collision: Godot's single-instance-per-project lock and project-name-keyed app-data log are shared across sibling worktrees.
   - Document symptoms, safe process serialization/checks, and why current-run editor logs outrank a stale global log.

The plan worker may modify only:

- `.gen/plan.md`
- `.gen/clusters/issue-13-editor-log.md`

No gameplay/source files, commits, pushes, merges, GitHub mutations, or automatic process termination.

## Acceptance criteria

- A stale line in `%APPDATA%\Godot\app_userdata\TowerDefenseByAI-Godot\logs\godot.log` cannot false-FAIL a new `Run-Scenario.ps1 -Editor` run.
- The implementation uses `editor.out.log`/`editor.err.log` produced by `Invoke-Godot`, or proves shared-log freshness before trusting it.
- Existing patterns (`SCRIPT ERROR`, `Parse Error`, `Cannot infer the type`, `Compile Error`, `Invalid call`) and meaningful exit behavior remain covered.
- The reference documents that sibling worktrees can collide on the Godot lock and shared project-name log, with safe mitigation guidance.
- Runtime checks pass on supported Windows/PowerShell; if unavailable, the limitation is reported and static checks still run.

## Verification

```powershell
$run = '.claude/skills/game-test/scripts/Run-Scenario.ps1'
& $run -Editor
if ($LASTEXITCODE -ne 0) { throw "-Editor failed: $LASTEXITCODE" }
Get-Item '.gen/harness/_logs/editor.out.log', '.gen/harness/_logs/editor.err.log' |
  Select-Object FullName, Length, LastWriteTime

$text = Get-Content $run -Raw
if ($text -match "Join-Path \$env:APPDATA.*godot\.log" -and $text -notmatch 'OutLog|editor\.out\.log|LastWriteTime') {
  throw 'shared godot.log remains the sole editor verdict source'
}
$reference = Get-Content '.claude/skills/game-test/REFERENCE.md' -Raw
if ($reference -notmatch 'single-instance|worktree|editor\.out\.log') {
  throw 'required collision/freshness documentation is absent'
}
git diff --check
```

Before runtime verification, inspect `Get-Process | Where-Object ProcessName -like '*Godot*'`; wait for an existing project run and do not stop an unfamiliar process. Confirm the fresh per-run log timestamps/lengths and compare any shared-log mtime only as diagnostic evidence.

## Findings

The repository's `Run-Scenario.ps1` invokes `Invoke-Godot` with log names and redirects stdout/stderr to per-run files, but `-Editor` currently ignores those files and scans a global app-data `godot.log`. The project has multiple sibling worktrees, and project instructions plus the existing game-test guidance identify Godot's single-instance lock and log as project-name keyed. Cross-worktree collision is therefore confirmed as a real operational risk.

## Handoff

Implement Task 1 in the script, then Task 2 in the reference, then run the verification commands above. Keep the change surgical and leave all gameplay/source files untouched.

---
## Verification status for this planning worker

Artifacts were written and must be rechecked by the parent/implementation workflow. No source files were modified.

---

# End cluster
