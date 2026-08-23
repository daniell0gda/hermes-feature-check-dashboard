# Request

- request_id: req-84-traps-venom-barbs
- issue: https://github.com/daniell0gda/poke-defense-godot/issues/84
- project runner key: godot-td
- git workspace: poke-defense-godot/issue-traps-venom-barbs
- branch: issue/traps-venom-barbs
- intent: implementation (explicit pickup of #84)

## Feature

Traps get Unique `traps_venom_barbs` (levels 1–3). Trap hits apply lingering poison via the existing `EffectsManager.apply_poison(total_damage, duration, tick_interval, tower_type_id, tower_instance_id)` path. Poison magnitude scales by level; duration and tick interval match Venom tower baseline. Reuse existing poison tick/cloud VFX. Verify poison VFX and lingering damage work when the source is a trap, not Venom.

## Acceptance

- Add Unique `traps_venom_barbs` at levels 1–3.
- Trap hits apply poison through existing `apply_poison`.
- Scale poison magnitude by level; duration/tick interval match Venom baseline.
- Reuse existing poison VFX from `apply_poison`.
- Verify poison VFX and lingering damage trigger correctly from a trap source.

## Redo notes

- Runner: project=`godot-td`, workspace=`poke-defense-godot/issue-traps-venom-barbs` only.
- Visible player-facing VFX: `manual_testing: required`, windowed screenshots/GIF, no `--headless` for manual tester.
- Do not commit/push/merge/close unless asked.

## Related

Companion perk to #39, #40, #41, #42 (trap perk family).
