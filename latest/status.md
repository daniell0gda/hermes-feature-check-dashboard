## ✅ Done
- Perk definition registered like other progression perks; purchasable at 3 levels.
- Trigger fires exactly once per shield instance: only on the >0 → 0 transition. Hits while armor is already 0 do NOT re-trigger; re-trigger requires the enemy regaining armor first.
- Damage-taken multiplier applies for the debuff duration, per level values above, then expires cleanly.
- New VFX: `ExposedStatus`/`ExposedVFX` following the existing `BurnStatus`/`BurnVFX` pattern — cracked-shield emissive overlay for the duration, lazily instantiated by `EffectsManager` like `BurnVFX`/`OilVFX`. Headless machine proof passes (`exposed_plating_vfx`: `status=pass`, exit 0, all actions ok incl. `exposed_vfx == true` during the Exposed window); remaining windowed screenshots / real-30fps `record_frames` GIF + `ui_feels_broken` verdict are manual-tester scope (no `.gen/manual-report.md` yet), not code-check work.
- Debug logging: `[EXPOSED] prefix lines on trigger and expiry, gated by `OS.is_debug_build()`.

## ⬜ Pending
- Manual player-facing evidence only (manual-tester profile): windowed gameplay captures of the Exposed overlay appearing on a real armored enemy at the breach moment and disappearing on expiry; real-time 30fps recording from the harness `record_frames` path of `exposed_plating_vfx`; manual UI sanity pass ending with an explicit `ui_feels_broken: yes|no` verdict recorded in `.gen/manual-report.md`.

## ❌ Impossible
- (none)
