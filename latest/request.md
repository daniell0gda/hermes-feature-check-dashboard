# Request: Water Tower: Riptide — light Slow alongside Wet (issue #47)

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/47
Project: poke-defense-godot
Workspace: poke-defense-godot/issue-water-tower-riptide-light-slow-alongside
Branch: issue/water-tower-riptide-light-slow-alongside (cut fresh from origin/master @ b5d75ae)
Request ID: r47-1

## Summary
Add new Unique perk `water_riptide`: Water tower hits also apply a 20% Slow for 1.5s,
via the existing EffectsManager / apply_slow-style API.

## Done when
- New Unique `water_riptide` exists and is grantable through the normal progression flow.
- Water hits apply a 20% Slow for 1.5s on enemies.
- Must respect the existing slow-owner-refresh convention in
  `EnemyStatusController.apply_slow` so it does not fight Ice's own slow application.
- Visual: reuse the existing IceSlowFX snowflake particles + ice-tint overlay driven from
  `scripts/game/actors/enemy/parts/EnemyStatusController.gd` — no new VFX asset; confirm the
  cue fires when Water is the trigger instead of Ice.

## Runner notes (redo pins)
- Runner key + workspace MUST be exactly `godot-td` / `poke-defense-godot/issue-water-tower-riptide-light-slow-alongside`.
- Use explicit scene argument before user args in gameplay harness commands.
- Visible player-facing work → manual_testing: required; windowed screenshots/GIF only (no --headless for the manual test).
