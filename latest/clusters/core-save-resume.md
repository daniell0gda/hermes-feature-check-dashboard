# Cluster A — Checkpoint/save/resume core

- `parallel: false`
- **Depends on:** none.
- **Blocks:** UI and harness clusters.
- **Exclusive ownership:** `scripts/systems/AutoSaveManager.gd`, `autoload/SaveManager.gd`, `autoload/LoadManager.gd`, `scripts/utils/GameSaveLoader.gd`, `scripts/game/Game.gd`, `scripts/core/GameState.gd`.
- **Forbidden overlap:** Do not edit UI, scene, harness, scenario, or comparator files. Do not change unrelated wave/combat behavior.

## Implementation steps

1. Define a typed, versioned checkpoint payload with explicit phase, map, wave, completion flag, game state, layer, speed, auto-next, and monotonic generation/reason. Keep one save owner and one exact-once boundary-transition guard.
2. Replace wall-clock-second throttling with a monotonic/deferred mechanism that coalesces reasons but never loses the newest checkpoint. Emit lifecycle signals/results for started, succeeded, failed, and recovered saves; preserve the prior good file on failure.
3. Route all required checkpoint sites through that owner: main/new game, active wave, post-wave, before-next-wave, pause, interruption/menu/quit, finished/defeat/victory, and naptime/idle.
4. Make both spawner-clear paths converge on one idempotent completion transition. Persist post-wave before auto-next, and consume a before-next token once so loading cannot replay or skip it.
5. Restore the saved phase without unconditional pause. If exact live enemy restoration is not feasible, expose and document the smallest deterministic spawner/active-wave seam that proves the phase and no duplicate spawn; do not claim `current_wave` alone proves active-wave restoration.
6. Add a test-only deterministic FileAccess failure seam that cannot be enabled by normal gameplay; failed writes return false and leave last-known-good data intact.

## Acceptance/evidence

- Unit/in-process checks prove monotonically increasing generations, one save result per request, no duplicate clear transition, and last-good preservation on failure.
- Focused harness can observe checkpoint phase/reason/generation and failure/recovery.
- Headless issue-62 scenario proves active-wave, post-wave, before-next exactly once, paused, interrupted, finished, and naptime resumes.
- Existing wave scenarios remain unaffected.

## Commands

Through `run_project_cmd` (`godot-td`, `godot-td/issue-62`):

```json
{"cmd":["godot","--headless","--path",".","--editor","--quit-after","300"],"project":"godot-td","workspace":"godot-td/issue-62"}
{"cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_autosave_resume.json"],"project":"godot-td","workspace":"godot-td/issue-62"}
```

Do not use runner for Git; parent performs `git diff --check` Hermes-side.

## Handoff

Report payload schema, signal names, exact-once guard semantics, failure-injection method, and fresh result path to Cluster B/C. No source changes outside owned files.

