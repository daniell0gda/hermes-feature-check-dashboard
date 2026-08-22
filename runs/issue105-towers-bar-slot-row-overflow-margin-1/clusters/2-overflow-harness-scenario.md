# Cluster 2: overflow-harness-scenario

- Owned file scope: `tests/scenarios/towers_bar_slot_overflow.json`
- Dependencies: 1
- parallel: false (asserts the degradation behavior from cluster 1)

## Acceptance criteria

- A `game-test` scenario loads a map with the full roster unlocked, captures a windowed screenshot checkpoint of the towers bar, and its headless assertions report pass with no slot clipped (or the slot row's width measured within the bar's inner width).
- The same scenario leaves the underground trap row (UndergroundTraps with Trap1/Trap2/Trap3/Trap5) laid out inside its panel with no regression versus current behavior.

## Notes

- Headless screenshot checkpoints report skipped/headless; windowed run required for the manual pass. Do not weaken the no-clip assertion to a slot count.
- Scenario must expose harness-readable layout state for the towers bar (slot rects / row width vs panel inner rect).

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/towers_bar_slot_overflow.json"]`
- Full test: `["bash", "-lc", "for s in smoke_placement hud_controls_state hud_wood_panels hud_other_panels hud_layer_roundtrip removed_tower_kinds_no_crash; do godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/$s.json || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

manual_testing: required
