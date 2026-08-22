# Cluster 3: static-breach-vfx

- Files: `scripts/game/actors/effects/EffectsManager.gd`, new effect script following `scripts/game/actors/effects/OverheatGlowVFX.gd` pattern, `scripts/utils/HighlightShaderUtils.gd`
- Dependencies: 2 (static-breach-charge-runtime)
- Parallel: false

## Acceptance criteria
- While an enemy carries at least one stacked charge, it shows a visible stacking-charge highlight built through the existing HighlightShaderUtils preset-highlight factory pattern, and the visual clears when the charge resets.
- The triggering breach hit produces a distinct shatter flash on the enemy, distinct from the persistent charge highlight, reusing the shield-crack asset where one exists.

## Verification commands
- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/static_breach_vfx.json"]` (windowed for pixels)
- Full test: see plan.md Full test
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
