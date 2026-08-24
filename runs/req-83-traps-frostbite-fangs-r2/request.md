# Request: traps_frostbite_fangs (visual evidence redo)

- request_id: req-83-traps-frostbite-fangs-r2
project: godot-td
workspace: poke-defense-godot/issue-traps-frostbite-fangs
issue: https://github.com/daniell0gda/poke-defense-godot/issues/83
- branch: issue/traps-frostbite-fangs
- intent: continuation — keep the perk implementation; fix failed visual proof

## Feature

Traps with Unique perk `traps_frostbite_fangs` (L1-3) chill on hit via existing `EffectsManager.apply_frozen`. Level scales magnitude/duration. Reuse frost overlay VFX.

Implementation is already in the worktree (uncommitted). Do not revert it. Do not re-invent the perk.

## Why this run exists

req-83-traps-frostbite-fangs check/manual-tester claimed pass. Human review of the published shots FAILED:

- `frostbite_fangs_chilled_hit.png` is a wide underground view. The trap is a tiny brown cube on a distant stone pad. Any enemy is a speck. Frost tint is not readable.
- `frostbite_fangs_chilled_enemy_zoom.png` cropped empty floor. It does not show the trap or an enemy.
- That does not confirm "frost overlay renders when triggered from a trap."

Old `.gen/check.md`, `status.md`, `manual-report.md`, and `screenshots/` are historical. They are not fresh evidence.

## Required this run

1. Keep Unique `traps_frostbite_fangs` L1-3, `apply_frozen` on trap hit, per-level scale, existing VFX, ownership/stacking.
2. Fix the focused scenario so windowed shots prove the chill:
   - Top-down (not side), camera close on the placed trap + live enemy at hit time.
   - Enemy large enough to see body color. Frost/ice tint must be obvious vs a normal green Cactoro.
   - Explicit `screenshot` and 30fps `record_frames` GIF (not a still slideshow).
3. Checker must fail (`classification: fixable`) if shots do not clearly show a frost-tinted enemy next to the trap. Tiny distant specks fail. Wrong crop fails. Headless state tags alone do not pass the visual criterion.
4. `ui_feels_broken: yes` fails the manual test even if state assertions pass.
5. Re-run editor import gate + focused harness through the runner after any scenario/source change.

## Runner (mandatory)

Use only `run_project_cmd` with:
- project=`godot-td`
- workspace=`poke-defense-godot/issue-traps-frostbite-fangs`

Never `project=poke-defense-godot`. Never host-shell Godot.

## Manual testing

`manual_testing: required`. Windowed only. No `--headless`. New PNGs + 30fps GIF under `.gen/screenshots/`.

## Lifecycle

Do not commit, push, merge, or close the issue.
