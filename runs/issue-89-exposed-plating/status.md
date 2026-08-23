## ✅ Done
- Perk definition registered like other progression perks; purchasable at 3 levels.
- Trigger fires exactly once per shield instance: only on the >0 → 0 transition. Hits while armor is already 0 do NOT re-trigger; re-trigger requires the enemy regaining armor first.
- Damage-taken multiplier applies for the debuff duration, per level values above, then expires cleanly.
- Debug logging: `[EXPOSED]` prefix lines on trigger and expiry, gated by `OS.is_debug_build()`.

## ⬜ Pending
- New VFX: `ExposedStatus`/`ExposedVFX` in `scripts/game/actors/effects/` following the existing `BurnStatus`/`BurnVFX` pattern — cracked-shield emissive overlay swapped onto the enemy's material for the duration, lazily instantiated by `EffectsManager` like `BurnVFX`/`OilVFX`. — implementation exists (`ExposedStatus.gd`, `ExposedVFX.gd`, lazy `show_exposed()`/`hide_exposed()` in EffectsManager) and the harness proves the status window wiring, but the player-facing visual itself requires the mandated windowed/manual verification (screenshots or 30fps `record_frames` GIF on a real enemy, ending with a `ui_feels_broken` UI-sanity pass); that evidence is owned by the manual-tester profile and was not yet produced.

## ❌ Impossible
- (none)
