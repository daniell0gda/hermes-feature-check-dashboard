# Request: Exposed Plating perk (issue #89)

- **Repo:** daniell0gda/poke-defense-godot
- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/89
- **Workspace:** poke-defense-godot/issue-exposed-plating
- **Branch:** issue/exposed-plating (cut from origin/master @ 97ed515)
- **Runner key:** `godot-td` — workers MUST use runner key `godot-td`, workspace `poke-defense-godot/issue-exposed-plating`. Never invent workspace names.

## Feature

New progression perk `exposed_plating` (Common, global, 3 levels). When an enemy's armor transitions from >0 to 0 (same site as `_consume_armor()`, `EnemyHealthController.gd:337-344`), it gains an "Exposed" status:

- L1: +15% damage taken, 0.5s
- L2: +25% damage taken, 1s
- L3: +35% damage taken, 1.5s

## Acceptance criteria

1. Perk definition registered like other progression perks; purchasable at 3 levels.
2. Trigger fires exactly once per shield instance: only on the >0 → 0 transition. Hits while armor is already 0 do NOT re-trigger; re-trigger requires the enemy regaining armor first.
3. Damage-taken multiplier applies for the debuff duration, per level values above, then expires cleanly.
4. New VFX: `ExposedStatus`/`ExposedVFX` in `scripts/game/actors/effects/` following the existing `BurnStatus`/`BurnVFX` pattern — cracked-shield emissive overlay swapped onto the enemy's material for the duration, lazily instantiated by `EffectsManager` like `BurnVFX`/`OilVFX`.
5. Debug logging: `[EXPOSED]` prefix lines on trigger and expiry, gated by `OS.is_debug_build()`.

## Verification requirements (redo notes for workers)

- Focused headless harness scenario proving the once-per-shield-instance transition semantics (pre/post checkpoints, exact damage deltas).
- Manual testing: REQUIRED (player-facing VFX). Windowed screenshots/GIF of the Exposed VFX on a real enemy; no headless-only verification. GIFs must be real 30fps from engine frames via harness `record_frames`.
- End every manual test with overall UI-sanity pass (`ui_feels_broken: yes|no`).
- Use native Godot through run_project_cmd; explicit scene argument before user args; windowed runs may need `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy`.
