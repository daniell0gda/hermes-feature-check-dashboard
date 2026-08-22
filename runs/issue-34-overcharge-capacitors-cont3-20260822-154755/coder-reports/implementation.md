# Coder report: implementation

## Changed files
- `autoload/ProgressionManager.gd` — modified (overcharge bonus in damage pipeline)
- `scripts/progression/global.json` — modified (perk definition)
- `tests/scenarios/overcharge_capacitors_progression.json` — new (harness scenario)

## Criteria (cluster 1: overcharge-perk-registration-and-damage-bonus; cluster 2: overcharge-harness-scenario)
- `overcharge_capacitors` registered as Common perk, eligible on fresh run and drawn via `draw_choices_for_chest` — Done
- Fewer than 3 same-type towers → no bonus for that type — Done
- Exactly 3 same-type towers → tier-1 bonus (+0.10 at L1) applied to that type — Done
- 6+ same-type towers → tier-2 bonus (+0.20 at L1), one tier per complete group of 3 — Done
- Per-type isolation (3 generic + 3 cannon each get their own tier) — Done
- Composes with existing pipeline: `get_tower_damage_multiplier_for(kind)` = global + overcharge for kind — Done
- Bonus stops when same-type count drops below threshold via sell path — Done
- `reset_for_new_game()` clears selection and bonus — Done
- `[OVERCHARGE]` debug log behind `OS.is_debug_build()` — Done (code inspection; official headless build is release so the print cannot be observed here)
- Harness scenario covers <3 / ==3 / 6+ boundary cases with all expectations passing — Done

## Commands and results
- Preflight `godot --version` — exit 0; Godot 4.4.1.stable.official.49a5bc7b6.
- Focused test `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/overcharge_capacitors_progression.json` — exit 0; harness `status=pass`. Log shows `[OVERCHARGE] type=generic towers=3 tier=1 multiplier=1.1`, `towers=6 tier=2 multiplier=1.2`, `type=cannon towers=3 tier=1 multiplier=1.1`, then reset clearing bonuses.
- Full test `--harness=res://tests/scenarios/display_damage_surface_parity.json` — exit 0; harness `status=pass`.
- Typecheck/build `godot --headless --path . --editor --quit-after 300` — exit 0 (~9s). No parse errors from the modified autoload; only pre-existing asset/UID import warnings.

## Notes
- Iteration-2 blocker was the scenario's count-drop sell step selecting an unplaced position (-6.0, 0.0, 2.0); fixed to select the placed tower at (-6.0, 0.0, 0.0) before `_on_sell_pressed`, so one generic tower is removed and the tier-1 bonus falls back to 0.0. Quality-note entry `overcharge-scenario-sell-target-mismatch` resolved this iteration.
- Implementation detail: `get_overcharge_bonus_for_kind(kind)` returns level_value × floor(count/3), 0 when not selected or count < 3; wired into both branches of `get_tower_damage_multiplier_for` (generic branch adds overcharge; bazooka branch includes it alongside rocket modifiers).
- Tower counting walks `Game/Towers.get_towers()` defensively with null/validity guards so it degrades to 0 outside a running game scene.
