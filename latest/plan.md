# Issue #13 — Fresh Godot editor verdicts and worktree collision plan

**Goal:** Make `Run-Scenario.ps1 -Editor` fail or pass from the current invocation's output instead of false-reading the shared/stale Windows `godot.log`, and document the confirmed cross-worktree collision risk.

**Scope constraint:** Planning only. Do not modify gameplay/source files, commit, push, merge, or close the issue.

## Investigated context and findings

- `.claude/skills/game-test/scripts/Run-Scenario.ps1:82-91` already creates per-run `.gen/harness/_logs/<LogName>.out.log` and `.err.log` through `Invoke-Godot`; the editor run uses the stable basename `editor` at lines 152-155.
- The editor verdict currently reads `%APPDATA%\Godot\app_userdata\TowerDefenseByAI-Godot\logs\godot.log` at line 155 and scans it for `SCRIPT ERROR`, `Parse Error`, `Cannot infer the type`, `Compile Error`, and `Invalid call`. That path is outside the worktree and is not tied to this invocation.
- The project game-test guidance explicitly records that the shared log is global by Windows user/project name, and that stale content can remain when a launch is blocked. The project has multiple sibling worktrees (`issue-5`, `issue-50`, `issue-62`, `issue-63`, `disable-debug-perk`, etc.), so cross-worktree collision is a real risk, not merely theoretical.
- The single-instance conclusion is supported by the project instructions and existing skill guidance: Godot's per-project lock and project-name-keyed log are shared across sibling worktrees. A concurrent or stuck Godot process can therefore make a later invocation no-op or consume another run's shared log. The implementation should not attempt to namespace or delete the global lock from this issue; it should avoid trusting the shared log and report process/concurrency diagnostics.
- `gh` is not installed in this Linux worker, so the issue body could not be fetched independently; the issue title and acceptance criteria supplied in the task context are treated as authoritative.

## Exact acceptance criteria

1. `Run-Scenario.ps1 -Editor` must not false-FAIL because of an error line left in a shared/stale `%APPDATA%...\godot.log` by another or earlier run.
2. The editor check must either prove the shared log is fresh for this invocation before trusting it, **or** use the per-run `editor.out.log`/`editor.err.log` produced by `Invoke-Godot`. Preferred implementation: use the per-run logs as the verdict source and retain the shared log only as optional diagnostic context.
3. Preserve the existing error pattern set and exit-code behavior: return non-zero when current-run output contains a matching error; return zero for a clean editor run, subject to the existing Godot process exit result.
4. Verify that cross-worktree single-instance collision is real; document the finding and operational mitigation in `.claude/skills/game-test/REFERENCE.md` (including that the lock/log are keyed by project name, not checkout path, and that concurrent runs must be serialized/checked).
5. Add no gameplay/source changes. Keep generated artifacts under `.gen` only for this plan worker.

## Proposed implementation sequence

### Task 1 — Make the editor verdict invocation-local

**Owner:** one implementation worker; `parallel=false` with Task 2 because the docs must describe the final behavior and both tasks share the same acceptance decision.

**Files owned:**
- Modify: `.claude/skills/game-test/scripts/Run-Scenario.ps1`
- Do not modify: gameplay scripts, scenes, project settings, or generated harness results.

**Steps:**
1. Before changing behavior, preserve the current editor invocation contract and error patterns.
2. After `Invoke-Godot ... 'editor'` returns, scan `$run.OutLog` and `$run.ErrLog` (or a clearly documented combined current-run stream) rather than the global `godot.log`. Do not treat the presence/absence of a shared-log match as this invocation's verdict.
3. Keep the existing human-readable failure output, but identify the per-run files in diagnostics. If the implementation retains a shared-log fallback, it must record pre-run existence/length/mtime and accept shared matches only when the file changed during this invocation; otherwise report the shared file as stale/untrusted and use current-run logs.
4. Ensure the script handles missing per-run logs as an execution error, not as a clean pass. Keep `Set-StrictMode` safe for empty `Select-String` results.

### Task 2 — Document the confirmed collision and verification procedure

**Owner:** same implementation/review owner after Task 1; `parallel=false` because documentation must match the chosen log-source behavior.

**Files owned:**
- Modify: `.claude/skills/game-test/REFERENCE.md`

**Required documentation:**
- State that Godot's single-instance-per-project lock and `user://`/app-data log are keyed by project identity/name, so sibling worktrees can collide.
- Explain observable symptoms: fast no-op, hang, unchanged shared-log mtime/content, or output belonging to another scenario/worktree.
- Give safe mitigation: inspect running Godot processes before launch, serialize runs for this project, wait for the existing process, and do not kill an unfamiliar process without confirmation. Mention stale recovery-mode lock handling only as existing project guidance, not as an automatic destructive step.
- State that `editor.out.log` and `editor.err.log` are the invocation-local evidence for `-Editor`; a shared log may be diagnostic only unless freshness is proven.

## Dependencies and ownership

- **Prerequisite:** none beyond the existing `Invoke-Godot` behavior in `Run-Scenario.ps1` and the project instructions already read.
- **Dependency order:** Task 1 → Task 2 → verification. Documentation must not be finalized before the implementation chooses primary evidence.
- **Parallel decision:** `parallel=false`; one worker should make the script change and then update the reference so behavior and guidance cannot drift. No worker dispatch from this plan worker.
- **File ownership boundary:** implementation worker owns only the two listed files; this plan worker owns only `.gen/plan.md` and `.gen/clusters/issue-13-editor-log.md`.

## Verification commands

Run from the repository root on the supported Windows/PowerShell environment:

```powershell
$run = '.claude/skills/game-test/scripts/Run-Scenario.ps1'
& $run -Editor
$editorExit = $LASTEXITCODE
Get-Item '.gen/harness/_logs/editor.out.log', '.gen/harness/_logs/editor.err.log' |
  Select-Object FullName, Length, LastWriteTime
Get-Content '.gen/harness/_logs/editor.out.log'
Get-Content '.gen/harness/_logs/editor.err.log'
if ($editorExit -ne 0) { throw "Editor verification failed with exit $editorExit" }
```

Regression/freshness checks:

```powershell
$shared = Join-Path $env:APPDATA 'Godot\app_userdata\TowerDefenseByAI-Godot\logs\godot.log'
$before = if (Test-Path $shared) { Get-Item $shared | Select-Object Length, LastWriteTimeUtc } else { $null }
& $run -Editor
$exit = $LASTEXITCODE
$after = if (Test-Path $shared) { Get-Item $shared | Select-Object Length, LastWriteTimeUtc } else { $null }
# The verdict must agree with editor.out/err, not an injected stale shared-log error.
$exit
$before
$after
```

Static/shape checks (PowerShell):

```powershell
$text = Get-Content $run -Raw
if ($text -match "Join-Path \$env:APPDATA.*godot\.log" -and $text -notmatch 'LastWriteTime|OutLog|editor\.out\.log') {
  throw 'Editor verdict still relies solely on the shared log'
}
$text = Get-Content '.claude/skills/game-test/REFERENCE.md' -Raw
if ($text -notmatch 'single-instance|worktree|editor\.out\.log') { throw 'Collision/freshness guidance missing' }
```

If a Godot executable or Windows PowerShell is unavailable in the verification environment, report the blocker honestly; still verify artifact paths, Markdown presence, and script/reference diffs without claiming a runtime pass.

## Risks and non-goals

- A per-run redirected log proves what the launched process emitted, but cannot by itself prove Godot acquired the project lock; the script/docs should still surface a no-op or old process as a concurrency diagnostic.
- Do not delete the shared log or recovery lock automatically, and do not stop another agent's Godot process.
- Do not broaden this issue into a general runner redesign or alter gameplay tests.

## Done when

- The two owned source/doc files implement and document invocation-local editor evidence and the confirmed collision.
- The listed PowerShell checks pass on Windows, with a fresh `editor.out.log`/`editor.err.log` visible and no stale shared-log false failure.
- `git diff --check` is clean for the implementation files and no gameplay/source files changed.
- This plan and its cluster artifact exist under flat `.gen` paths.

> This artifact is a plan for a later implementation worker; it intentionally contains no source patch.

---
