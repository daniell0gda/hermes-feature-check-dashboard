# Cluster full-harness-scenarios — process boundary and machine evidence

- **parallel:** false
- **depends on:** `full-save-schema`, `full-wave-runtime`, and `full-ui-menu` APIs/node names.
- **exclusive ownership:** `scripts/testing/AgentHarness.gd`, `scripts/testing/HarnessActions.gd`, `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessScreenshot.gd` only when needed for immutable output routing, and new/updated `tests/scenarios/issue_62_full_*.json` files.
- **forbidden overlap:** do not edit gameplay/save/UI production files, smoke documentation, or `.gen` reports.

## Implementation

1. Keep ordinary harness user-file isolation, but add an explicit named seed/continue mode that preserves only the controlled save fixture across two separate Godot invocations and cleans it afterward. Fail clearly if the MainMenu or Game scene is unavailable.
2. Add values for checkpoint schema/generation/reason, lifecycle event sequence, raw save fingerprint, active enemy canonical snapshot, spawner queue/timer/RNG snapshot, transition token, producer list, naptime state, finished game state, and UI indicator.
3. Add actions that call real product APIs: seed active wave, request pause/menu/quit, invoke actual MainMenu Continue, trigger real idle transition, emit both real clear producers, advance manual/auto next wave, arm deterministic save failure, and capture before/after fingerprints. No action may merely assign `checkpoint_phase=finished` or `naptime`.
4. Create separate bounded scenarios: `issue_62_full_seed.json`, `issue_62_full_continue.json`, `issue_62_full_headless.json`, and `issue_62_full_visual.json`. Include explicit seeds, 45–90 second budgets, fresh suffixes, phase/reason/generation assertions, exact active-state comparison, duplicate-clear proof, failure/recovery proof, real naptime, finished-state consistency, and visual checkpoints for Saving/failed/recovered/victory/naptime/Continue.
5. Record fresh result timestamps and reject stale/overwritten artifacts. Headless screenshot actions must remain `skipped/headless`; only inspected windowed PNGs count for pixels.

## Acceptance and handoff

- Seed process writes a known active-wave snapshot; a second MainMenu process invokes the real Continue path and compares canonical state.
- All required phase transitions and exact-once event counts pass in fresh JSON.
- Every required visual checkpoint has a fresh PNG and an inspection target.
- Scenarios fail on inconsistent finished `game_state`, missing naptime transition, or aggregate-only active-wave evidence.

## Exact runner commands

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_full_seed.json","--harness-run=headless-1"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/MainMenu.tscn","--","--harness=res://tests/scenarios/issue_62_full_continue.json","--harness-run=headless-1"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_full_headless.json","--harness-run=headless-1"]}
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_full_visual.json","--harness-run=windowed-1"]}
```

Repeat each required focused run with suffixes `2` and `3`, sequentially.

## Bounded revision

Revision 1 may fix only harness routing/observability or a scenario assertion proven wrong by runtime evidence. Revision 2 may fix only remaining evidence wiring; it may not weaken exact-state or process-boundary requirements.

No GitHub mutations, commits, pushes, merges, or issue closure.