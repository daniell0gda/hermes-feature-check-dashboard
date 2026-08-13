# Cluster B — UI lifecycle and indicator

- `parallel: false`
- **Depends on:** Cluster A's checkpoint schema and save lifecycle signals.
- **Blocks:** visual evidence in Cluster D; harness UI assertions in Cluster C.
- **Exclusive ownership:** `scripts/ui/UI.gd`, `scripts/ui/PauseMenu.gd`, `scripts/MainMenu.gd`, and the specific Main/UI scene resources required to place the indicator.
- **Forbidden overlap:** Do not edit save core, harness, scenario, or unrelated visual assets.

## Implementation steps

1. Add one stable, always-readable indicator to the existing game UI and map save lifecycle events to exactly three user-facing states: Save safe, Saving, Save failed. Keep it processable while paused.
2. Display failure context without destroying the prior good save; on the next success display recovery/save-safe rather than leaving a stale error.
3. Wire pause, menu/quit/interrupted, victory/defeat/finished, and naptime/idle flows through the core checkpoint owner. Avoid duplicate signal connections on scene reload.
4. Fix Continue's saved-map lookup to match the actual save schema and show a visible load error/recovery message instead of console-only output.
5. Ensure normal-scale windowed layout/readability and that the indicator remains visible during transitions and on the finished/naptime screen as required by the issue.

## Acceptance/evidence

- Windowed screenshots visibly show Save safe, Saving, Save failed, recovery, and finished/resume states at normal scale.
- Harness-visible UI values or call targets confirm lifecycle state and no duplicate handlers.
- Continue loads the saved map and phase; invalid/missing save shows visible error without crashing.

## Commands

Use the focused scenario windowed through the runner (no `--headless`):

```json
{"cmd":["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_62_autosave_resume.json"],"project":"godot-td","workspace":"godot-td/issue-62"}
```

Inspect fresh PNGs under `.gen/harness/issue_62_autosave_resume/shots/`; file existence alone is not visual proof.

## Handoff

Give Cluster C the final node/call/value names for indicator state and the screenshot checkpoint names. Give Cluster D the fresh PNG paths and observed text/layout.

