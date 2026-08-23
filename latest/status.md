## ✅ Done
- Perk definition registered like other progression perks; purchasable at 3 levels.
- Trigger fires exactly once per shield instance: only on the >0 → 0 transition. Hits while armor is already 0 do NOT re-trigger; re-trigger requires the enemy regaining armor first.
- Damage-taken multiplier applies for the debuff duration, per level values above, then expires cleanly.
- Debug logging: `[EXPOSED]` prefix lines on trigger and expiry, gated by `OS.is_debug_build()`.

## ⬜ Pending
- New VFX: `ExposedStatus`/`ExposedVFX` in `scripts/game/actors/effects/` following the existing `BurnStatus`/`BurnVFX` pattern — cracked-shield emissive overlay swapped onto the enemy's material for the duration, lazily instantiated by `EffectsManager` like `BurnVFX`/`OilVFX`. — implementation exists (`ExposedStatus.gd`, `ExposedVFX.gd`, lazy `show_exposed()`/`hide_exposed()` in EffectsManager); revision iteration added the observable `exposed_vfx` harness field and the `exposed_plating_vfx` visual-bed scenario, which passes headless with the full wash lifecycle asserted (exposed_vfx 0 → 1 on breach → 0 after expiry). Remaining: the player-facing visual itself requires the mandated windowed/manual verification (screenshots or real-30fps `record_frames` GIF via that scenario run windowed, ending with a `ui_feels_broken` UI-sanity pass); owned by the manual-tester profile, not yet produced.

## ❌ Impossible
- (none)
