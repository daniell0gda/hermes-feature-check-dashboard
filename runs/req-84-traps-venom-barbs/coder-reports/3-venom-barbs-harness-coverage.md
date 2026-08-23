# Coder report: 3-venom-barbs-harness-coverage

## Changed files
- `tests/scenarios/traps_venom_barbs_progression.json` — new: headless scenario asserting the perk definition (Unique, 3 levels), unowned → `get_trap_poison_total()==0.0`, L1/L2/L3 → 8/16/24 strictly increasing, duration 10.0 / tick 0.25 constant across levels, save/load replay lands back on L3, serrated-edges ownership does not enable poison
- `tests/scenarios/traps_venom_barbs_trap_poison.json` — new: live runtime scenario on map_7 wave 6 (single armored Orc Enemy King, hp 1625, armor 60): scripted `Trap.perform_hit` (apply_effect "trap_hit") proves unowned → exact direct damage, no PoisonStatus, no extra stats accrual over a wait window; owned L1 → same direct damage, `poisoned` becomes true, first visible HP drop composes direct+first poison point, HP keeps dropping across ticks with no further trap hits, trap-attributed poison totals ≥4 through stats, and a regex assertion on the `[VENOM-BARBS]` log line (enemy id, trap id, level, total, duration, tick); Undermining arm re-proves armor strip 60→52 alongside poison (perks compose)
- `scripts/testing/HarnessValues.gd` — mod (harness seam): enemy condition source gained a `poisoned` field (`PoisonStatus != null`) in `_live_enemy_field` and `_enemy_report`

## Criteria
- Progression/scaling scenario — Done (status=pass)
- Live trap-hit poison scenario — Done (status=pass)

## Commands and results
- Both focused scenarios run via runner: exit code 0 each; result.json status=pass for `traps_venom_barbs_progression` and `traps_venom_barbs_trap_poison`

## Gotchas
- `scripts/testing/HarnessValues.gd` was extended to expose the `poisoned` enemy field — if another issue touches HarnessValues, expect this addition.
- Full-suite note: `progression_pick` fails on `venom_miasma_bloom` expectations, but it fails identically with all venom-barbs changes stashed — pre-existing on this branch, unrelated to trap files.

## Measured
- Focused scenarios elapsed ~1.4s each headless.
