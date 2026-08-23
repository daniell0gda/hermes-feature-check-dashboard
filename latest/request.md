# Request: traps_frostbite_fangs

- request_id: req-83-traps-frostbite-fangs
- issue: https://github.com/daniell0gda/poke-defense-godot/issues/83
- project runner key: godot-td
- git workspace: poke-defense-godot/issue-traps-frostbite-fangs
- branch: issue/traps-frostbite-fangs
- intent: implementation

## Feature

Add Unique perk `traps_frostbite_fangs` (L1-3). Trap hits apply chill/slow via existing `EffectsManager.apply_frozen(magnitude, duration, owner_instance_id)`. Duration or magnitude scales per level. Reuse existing frost overlay VFX; no new asset. Confirm frost overlay renders when triggered from a trap.

## Acceptance

- Add Unique `traps_frostbite_fangs` (L1-3).
- Trap hits apply chill/slow via `EffectsManager.apply_frozen`.
- Duration or magnitude scales per level.
- Reuse existing frost overlay VFX from `apply_frozen`.
- Confirm frost overlay renders when the effect is triggered from a trap.
- Preserve existing frozen-effect ownership and stacking semantics.
- Focused coverage for level scaling and trap-triggered visual/effect behavior.
- Verify the relevant trap gameplay path, not only generic parsing.

## Runner

Use only `run_project_cmd` with project=`godot-td` and workspace=`poke-defense-godot/issue-traps-frostbite-fangs`.

## Manual testing

Visible frost overlay on trap hit is player-facing: `manual_testing: required`. Windowed screenshots/GIFs, no `--headless` for manual tester.

## Lifecycle

Do not commit, push, merge, or close the issue.
