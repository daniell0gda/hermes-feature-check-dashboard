# Cluster C — Focused harness and scenario coverage

- `parallel: false`
- **Depends on:** Cluster A's checkpoint/lifecycle API; Cluster B for indicator node/value names and windowed checkpoints.
- **Blocks:** final verification.
- **Exclusive ownership:** `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessActions.gd`, `scripts/testing/AgentHarness.gd` only if necessary, and new `tests/scenarios/issue_62_autosave_resume.json` (plus a narrowly scoped visual companion if needed).
- **Forbidden overlap:** Do not implement gameplay/save behavior in the harness. Do not edit UI/core files owned by A/B.

## Scenario requirements

Create one deterministic, hermetic scenario with a fixed seed and bounded budget. The timeline must exercise and assert:

1. main/new-game save-safe checkpoint;
2. active-wave save and continue with same wave/phase/towers/underground/spawner state;
3. post-wave checkpoint with `wave_completed=true` and no automatic duplicate advance;
4. before-next-wave transition under auto-next and both clear routes, exactly one generation/reason and one wave increment;
5. paused save/resume preserving paused state, speed and layer;
6. interrupted/menu/quit checkpoint and continuation;
7. finished/victory/defeat and naptime/idle checkpoints;
8. deterministic save failure followed by successful recovery, with lifecycle sequence and last-good generation asserted;
9. indicator values: Save safe, Saving, Save failed, and recovered safe; screenshot actions at active save, failed/recovered save, resume, and finished/naptime.

Use explicit expectations rather than logs alone. If the existing schema cannot resolve a required value, add the smallest typed resolver/action and return that gap to Cluster A/B; never use aggregate wave counts to imply active-enemy restoration.

## Acceptance/evidence

- Fresh headless result has `status: pass`, all expectations pass, and includes checkpoint sequence/generation/reasons.
- No stale `.gen/harness/issue_62_autosave_resume/result.json` is accepted; isolate/rotate output between runs.
- Windowed result records screenshot metadata and fresh PNGs for visual inspection; headless screenshot checkpoints are correctly treated as skipped.
- Existing `visual_checkpoints.json` format and user-save isolation remain intact.

## Exact commands

Through `run_project_cmd` (`godot-td`, `godot-td/issue-62`):

```json
{"cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_autosave_resume.json"],"project":"godot-td","workspace":"godot-td/issue-62"}
{"cmd":["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_autosave_resume.json"],"project":"godot-td","workspace":"godot-td/issue-62"}
```

Run headless three fresh sequential times and windowed three fresh sequential times; inspect `.gen/harness/issue_62_autosave_resume/result.json`, `_logs/*.out.log`, and `shots/*.png` after each mode.

## Handoff

Provide scenario seed, timeline, expectations, action records, lifecycle sequence, and screenshot names/paths to Cluster D. No GitHub mutations or source edits outside ownership.

