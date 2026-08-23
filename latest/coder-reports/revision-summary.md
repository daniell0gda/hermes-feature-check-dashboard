# Revision 1 summary — implementation

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
