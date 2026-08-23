# Coder report: implementation\n\n# Coder report: implementation (revision 1)

## Changed files
- `scripts/testing/HarnessActions.gd` — modified
- `scripts/testing/AgentHarness.gd` — modified
- `tests/scenarios/hud_controls_state.json` — modified
- `.claude/skills/game-test/REFERENCE.md` — modified

## Criteria
- press_button delivers real GUI-path press, reports landed — Done
- unresolvable target → ok:false naming target — Done (code path present in `_press_button`; resolution failure returns the naming detail)
- disabled button → landed:false, handler not run — Done (`landed` only set when not `disabled` and visible; BaseButton swallows input while disabled)
- works headless via real GUI path — Done (verified headless by focused run)
- REFERENCE.md documents fields/detail/headless behaviour — Done
- Debug-build `[HARNESS-CLICK]` log per attempt — Done (`OS.is_debug_build()`-gated print carrying target, landed, disabled-at-press)
- hud_controls_state presses Upgrade after ≥0.5s wait past selection, asserts level increase — Done

## Commands and results
All via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-harness-cannot-inject-gui-input.

- `godot --version` — exit 0; 4.4.1.stable.official.49a5bc7b6
- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0; import/parse clean
- Focused test: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_controls_state.json` — exit 0; `[Harness] status=pass exit=0`; log contains `[HARNESS-CLICK] press_button target=UpgradeBtn landed=true disabled=false`, `[UPG-BTN] upgrade button unavailable ... money=0` then `upgrade button available ... money=200` exactly once each; tower level 1→2; money 200→180. Result at `.gen/harness/hud_controls_state/result.json`.
- Full suite (run as individual commands because the profile allowlist rejects `bash -lc`):
  - `hud_layer_roundtrip` — exit 0, status=pass
  - `hud_heart_beat_on_egg_damage` — exit 0, status=pass
  - `hud_other_panels` — exit 0, status=pass
  - `hud_wood_panels` — exit 0, status=pass

## Notes
- Root cause of the previous red gate was two-fold:
  1. `_press_button` called `Control._gui_input(event)` directly; Godot 4 rejects that from GDScript ("Nonexistent function '_gui_input'"), so no click ever reached BaseButton and level never changed despite `landed: true`. Rewritten to `Viewport.push_input(event, true)` (motion event + down/up), which routes through the viewport's real GUI picking and works headless. Verified with a throwaway SceneTree probe before wiring in.
  2. `AgentHarness.materialize_engine_out_log` matched a marker identical across runs of one scenario, so log expectations read a stale first-run slice (all `log` expectations failed with empty actual). The activation line and marker now carry `(run <stamp>)`.
- Scenario money expectation corrected 172 → 180: generic tower base cost is 20 and upgrade cost is round(20 * 1.4^(level-1)) = 20 at level 1.
- Pre-existing engine warnings (invalid UIDs in HudTheme.tres, missing GLB imports, duplicate signal connects) are unchanged from HEAD and out of scope.
\n\n# Coder report: revision-summary\n\n# Revision 1 summary — implementation

## What changed
- `scripts/testing/HarnessActions.gd` — `_press_button` now delivers its click through `Viewport.push_input(event, true)` (motion + down/up). The previous `Control._gui_input(event)` direct call is rejected by Godot 4 ("nonexistent function"), so no press ever landed despite `landed: true`.
- `scripts/testing/AgentHarness.gd` — activation log line and materialize marker carry a per-run stamp `(run <n>)`, so log expectations never reuse a stale `.out.log` slice from an earlier run of the same scenario (previously all `log` expectations read an empty/first-run slice).
- `tests/scenarios/hud_controls_state.json` — money expectations corrected 172 → 180 (level-1 upgrade cost is 20, not 28).
- `.claude/skills/game-test/REFERENCE.md` — headless/delivery documentation updated to match the working mechanism.

## Verification (all via run_project_cmd, project=poke-defense-godot)
- Typecheck/build (`godot --headless --path . --editor --quit-after 300`) — exit 0
- Focused test (`hud_controls_state`) — exit 0, `status=pass`; `[HARNESS-CLICK] press_button target=UpgradeBtn landed=true disabled=false`, tower level 1→2, money 200→180
- Full suite: `hud_layer_roundtrip`, `hud_heart_beat_on_egg_damage`, `hud_other_panels`, `hud_wood_panels` — each exit 0, status=pass

## Artifacts
- status.md: all 7 criteria moved to Done
- changes.md: revision-1 entry with gotchas appended
- quality-notes.md: rewritten (two entries resolved, one new reusable gotcha)
- coder-reports/implementation.md: full report with exact command results
\n