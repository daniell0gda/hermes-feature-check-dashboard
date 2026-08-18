# Coder report — Issue #13 editor log freshness

## Outcome

Implemented the invocation-local editor verdict and documented the confirmed cross-worktree Godot collision guidance. No gameplay/source files were changed.

## Changed files

- `.claude/skills/game-test/scripts/Run-Scenario.ps1`
  - `-Editor` now scans the per-invocation `.gen/harness/_logs/editor.out.log` and `editor.err.log` returned by `Invoke-Godot`.
  - Missing invocation logs fail as an execution error.
  - Existing patterns remain: `SCRIPT ERROR`, `Parse Error`, `Cannot infer the type`, `Compile Error`, `Invalid call`.
  - Matching current-run output exits 1; a clean run preserves the Godot process exit code (non-zero exits non-zero); clean exit 0 exits 0.
  - The shared app-data `godot.log` is no longer used as the verdict source.
- `.claude/skills/game-test/REFERENCE.md`
  - Documents the confirmed project-name-keyed single-instance lock and app-data log collision across sibling worktrees.
  - Records symptoms, serialization/process checks, safe recovery-lock guidance, and the precedence of invocation-local editor logs.

## Exact verification commands and results

All project commands below were run through `run_project_cmd` with project `godot-td` and workspace `godot-td/issue-13`.

1. `godot --version`
   - Exit code: `0`
   - Output: `4.4.1.stable.official.49a5bc7b6`

2. `pwsh -NoLogo -NoProfile -Command '$run = ''.claude/skills/game-test/scripts/Run-Scenario.ps1''; $text = Get-Content $run -Raw; if ($text -match "Join-Path \\$env:APPDATA.*godot\\.log" -and $text -notmatch ''OutLog|editor\\.out\\.log|LastWriteTime'') { throw ''Editor verdict still relies solely on the shared log'' }; $reference = Get-Content ''.claude/skills/game-test/REFERENCE.md'' -Raw; if ($reference -notmatch ''single-instance.*worktree|worktree.*single-instance'') { throw ''Collision guidance missing'' }; if ($reference -notmatch ''editor\\.out\\.log'') { throw ''Fresh editor log guidance missing'' }; ''static checks passed'''
   - Exit code: `0`
   - Output: `static checks passed`

3. `pwsh -NoLogo -NoProfile -Command '$tokens = $null; $errors = $null; [System.Management.Automation.Language.Parser]::ParseFile(''.claude/skills/game-test/scripts/Run-Scenario.ps1'', [ref]$tokens, [ref]$errors) | Out-Null; if ($errors.Count -gt 0) { $errors | ForEach-Object { $_.ToString() }; exit 1 }; ''PowerShell parse passed'''
   - Exit code: `0`
   - Output: `PowerShell parse passed`

4. `godot --headless --path . --editor --quit-after 300`
   - Exit code: `0`; timed out: `false`; duration: `58445 ms`.
   - Complete runner output was inspected from `/tmp/hermes-results/call_03105U5BLiApDeBrVcqLEhc9.txt`.
   - The editor/import gate exposed one pre-existing/unrelated diagnostic: `SCRIPT ERROR: Parse Error: Function "get_process_frame()" not found in base self.` (one `SCRIPT ERROR`, one `Parse Error`). It is not in either changed file. There were also 189 generic `ERROR: Parameter "t" is null.` lines; no `Cannot infer the type`, `Compile Error`, or `Invalid call` matches.
   - Therefore the runner process exit was successful, but the project-wide editor gate is not diagnostically clean; this report does not claim a clean project parse gate.

5. Hermes-side source verification (not a project execution command): `git status --short && git diff --check && git diff -- .claude/skills/game-test/scripts/Run-Scenario.ps1 .claude/skills/game-test/REFERENCE.md`
   - Exit code: `0`.
   - Status contains exactly the two intended modified source/doc files.
   - `git diff --check` is clean.
   - No gameplay/source files were modified.

## Runtime limitation

The planned Windows `Run-Scenario.ps1 -Editor` invocation could not be executed in this Linux worker because the script's configured Godot binary is Windows-specific and the worker does not provide the supported Windows PowerShell/Godot environment. Static PowerShell parsing and shape checks passed; the native Godot editor gate was run through the approved runner and its unrelated existing diagnostic is recorded above.

No commit, push, merge, issue mutation, or process termination was performed.
