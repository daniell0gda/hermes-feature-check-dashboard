# Cluster: manual-visual-verification

parallel: false
depends_on: nature-count-scaling

## Owned files

- Evidence only under `.gen/manual/` — no production source changes.

## Acceptance criteria

- In a windowed (non-headless) run with PNG screenshots captured, `custom_map` (50x50) visibly shows trees and bushes spread across the whole board, not only near the paths or one corner; overall UI sanity verdict recorded as `ui_feels_broken: yes|no`.

## Verification commands

- Focused: `["godot", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/nature_decoration_scaling.json"]`
- Full: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/level_walkthrough_lean.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`

## Notes

- Windowed run required (no `--headless`); use `--rendering-method
  gl_compatibility --audio-driver Dummy` if Vulkan fails on llvmpipe. PNGs are
  mandatory evidence and must be stored under `.gen/manual/`.
- The manual-testing gate must state the overall UI-sanity criterion as
  `ui_feels_broken: yes|no`.
