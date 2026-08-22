# Cluster 1: hud-heart-beat-animation

cluster_id: hud-heart-beat-animation
owned file scope: `scripts/ui/UI.gd`, `scenes/UI.tscn`
dependencies: none
parallel: false

## Acceptance criteria

- Each decrease of GameState egg HP triggers a scale-up-and-return "beat" animation on the HUD egg/heart icon.
- After every beat animation completes, the HUD heart icon is exactly back at its original base scale (no drift).
- Rapid consecutive egg HP decreases do not stack or break the animation: each hit restarts cleanly from the base scale and the icon still settles at the exact original scale.
- An egg_changed emission that is not a decrease (value equal or higher, e.g. map load/reset/restore) does not trigger a beat.
- Debug-build [HUD] log line per heart-beat trigger naming the old and new egg HP values

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-heart-hud-beat-on-egg-damage` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/hud_heart_beat_on_egg_damage.json"]
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-heart-hud-beat-on-egg-damage` cmd=["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-heart-hud-beat-on-egg-damage` cmd=["godot", "--headless", "--editor", "--quit-after", "300", "--path", "."]
