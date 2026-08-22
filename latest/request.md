# Request: Heart HUD beat animation when egg takes damage

- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/139
- Slug: heart-hud-beat-on-egg-damage
- Project: poke-defense-godot
- Runner key: godot-td
- Workspace: poke-defense-godot/issue-heart-hud-beat-on-egg-damage
- Branch: issue/heart-hud-beat-on-egg-damage

## Feature
When the "egg" (base) HP decreases, the heart icon in the game HUD should play a small "heart-beat" animation: scale up slightly, then animate back to its original size.

## Acceptance criteria
- Each egg HP decrease triggers the HUD heart icon scaling up slightly and returning to its exact original scale.
- Rapid consecutive hits must not stack or break the animation (no drift from the base scale).
- Icon ends exactly at its original scale after each animation.

## Notes for workers
- Use runner key `godot-td` and workspace `poke-defense-godot/issue-heart-hud-beat-on-egg-damage` exactly.
- This is visible player-facing HUD work → manual_testing: required with windowed screenshots/GIF.
