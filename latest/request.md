# Request: traps-pit-of-spikes-first-hit-stuns-turn

- **Project:** poke-defense-godot
- **Git workspace:** poke-defense-godot/issue-traps-pit-of-spikes-first-hit-stuns-turn
- **Branch:** issue/traps-pit-of-spikes-first-hit-stuns-turn (cut from origin/master @ 0ba60e1)
- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/42 (claimed 2026-08-25T08:28:26Z)
- **Request ID:** traps-pit-of-spikes-42-r1

## Feature

New Unique perk `traps_pit_of_spikes` ("Pit of Spikes"): the first trap hit against a given enemy
also applies a brief Stun (~0.4s) via the existing `EffectsManager.apply_stun` path. The stun icon
already renders via `scripts/ui/EnemyHealthBar.gd` (`icon_stun`, driven off `stun_time_left`) — no
new VFX needed; just confirm the icon triggers when the source is a trap.

## Done when

- First trap hit on an enemy applies ~0.4s stun through `apply_stun`.
- Subsequent hits on the same enemy do NOT re-stun (only the first hit per enemy).
- Stun status icon shows on the enemy health bar after a trap hit.
- Focused headless harness proves the behavior; windowed screenshot evidence for the stun icon.

## Runner notes

- Runner key: `godot-td`; workspace: `poke-defense-godot/issue-traps-pit-of-spikes-first-hit-stuns-turn`.
- Use exact workspace names; invented ones give HTTP 422 chdir.
- Windowed evidence: add `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails.
- Manual testing: required (visible player-facing stun icon) with UI-sanity pass criterion.
