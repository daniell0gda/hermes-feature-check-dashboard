# Cluster C — harness autosave/resume report

## Changed files

Owned test-only changes:

- `scripts/testing/HarnessValues.gd`
  - Added `save` resolver for `SaveManager.last_save_result`.
  - Added `save_file` resolver for the current `user://game_progress.json` schema, including `version` and `statistics.map_id`.
- `scripts/testing/HarnessActions.gd`
  - Added `save_checkpoint` action that invokes the real `Game._request_checkpoint` path and records ordered `save_started`, terminal lifecycle, and recovery events plus the UI indicator state.
  - Added debug-only `fail_next_save` action using `SaveManager.test_fail_next_save()`.
  - Signal connections are guarded with `is_connected`.
- `tests/scenarios/issue_62_autosave_resume.json`
  - New deterministic seed `62062`, bounded 45-second timeline, explicit action records, waits, expectations, and screenshots.

No production save/UI/game files were edited by Cluster C. Existing Cluster A/B modifications remain in the worktree.

## Scenario timeline and evidence

The timeline is: map load/new-game; explicit `main/new_game`; trigger wave 1 and explicit `active_wave/active_wave`; explicit `post_wave/wave_complete`; `between_wave/awaiting_next_wave`; `before_next_wave/before_next_wave`; `paused/pause`; `interrupted/main_menu`; `finished/victory`; `naptime/idle`; arm `test_fail_next_save`; explicit failed `paused/forced_failure`; successful `paused/recovery`.

The fresh structured result records the lifecycle sequence per save action. In the final run, the deterministic failure was generation 13 (`save_started`, `save_failed`, `deterministic test failure`), followed by generation 14 (`save_started`, `save_succeeded`, `save_recovered`). The failed action reports `indicator: Save failed`; recovery reports `indicator: Save safe`. Final expectations pass for checkpoint phase/reason, generation 14, last-good generation 14, final save result, UI `Save safe`, schema version 2, and `statistics.map_id == map_1`.

Active-wave assertions deliberately cover the explicit checkpoint phase/reason and save schema only; they do not claim exact live-enemy restoration from aggregate counts. The scenario action timeline is the evidence for intermediate phases because final expectations execute after the timeline.

## Commands and results

All project commands were run through `run_project_cmd` with project `godot-td` and workspace `godot-td/issue-62`:

1. `godot --version` — exit 0; `4.4.1.stable.official.49a5bc7b6`.
2. `godot --headless --path . --editor --quit-after 300` — exit 0; editor/import and global-class scan completed.
3. `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_autosave_resume.json` — exit 0; fresh `status: pass`.
4. Same headless command, second fresh run — exit 0; `status: pass`.
5. Same headless command, third fresh run — exit 0; `status: pass`.
6. `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_62_autosave_resume.json` — exit 0; fresh `status: pass`, five 1920x1080 PNGs written.

Authoritative fresh result path (last run; the legacy harness rotates/overwrites one result file):

- `.gen/harness/issue_62_autosave_resume/result.json`

Windowed screenshot paths:

- `.gen/harness/issue_62_autosave_resume/shots/indicator_save_safe.png`
- `.gen/harness/issue_62_autosave_resume/shots/resume_active_wave.png`
- `.gen/harness/issue_62_autosave_resume/shots/indicator_save_failed.png`
- `.gen/harness/issue_62_autosave_resume/shots/indicator_recovered.png`
- `.gen/harness/issue_62_autosave_resume/shots/indicator_finished_naptime.png`

Headless screenshot actions correctly report `outcome: skipped`, `reason: headless`; they are not visual evidence.

## Diagnostics

The harness result has all expectations passing and status `pass`. Existing runtime diagnostics remain in the engine output: missing `Root/ButtonsContainer/TowerButtons/Tower1`, missing `EnemyHealthBar/SubViewport/Root/BarRow/IconBoss` during enemy spawn, repeated `layer_changed` connection on map reload, renderer fallback/Vulkan and dummy audio warnings in windowed mode, and renderer/ObjectDB/RID/resource leak diagnostics at shutdown. These were not introduced by Cluster C and are recorded for the checker rather than treated as assertion failures.

Hermes-side `git diff --check` exited 0. Existing Cluster A/B source modifications are visible in `git status`; Cluster C added only the two harness scripts and scenario listed above, plus this `.gen` report.

## Gaps and checker handoff

- The declarative harness cannot restart a second Godot process and perform a true Continue/menu reload within one scenario; it validates the current v2 producer payload (`version == 2`, `statistics.map_id`) and the lifecycle seam instead. A true process-boundary Continue check remains a checker follow-up.
- Exact live enemy/spawner identity restoration is not asserted, per Cluster A's documented boundary; the scenario asserts active-wave phase/reason and records the action timeline.
- The scenario exercises the before-next-wave checkpoint seam directly, but does not force both real clear signal producers to fire in one run. The production exact-once guard remains a Cluster A responsibility; a signal-level duplicate-clear probe is not reachable through the declarative schema without an additional narrow harness seam.
- Screenshots were produced fresh in windowed mode but were not pixel-inspected by this coder; checker should inspect all five PNGs before making visual claims.

No GitHub mutations, commit, push, merge, issue closure, or worker release was performed by Cluster C; the parent owns final lifecycle cleanup.
