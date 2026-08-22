## ✅ Done
- Each decrease of GameState egg HP triggers a scale-up-and-return "beat" animation on the HUD egg/heart icon.
- After every beat animation completes, the HUD heart icon is exactly back at its original base scale (no drift).
- Rapid consecutive egg HP decreases do not stack or break the animation: each hit restarts cleanly from the base scale and the icon still settles at the exact original scale.
- An egg_changed emission that is not a decrease (value equal or higher, e.g. map load/reset/restore) does not trigger a beat.
- Debug-build [HUD] log line per heart-beat trigger naming the old and new egg HP values
- The harness can read the HUD heart icon's current scale during a run so a headless scenario can assert the beat behaviour.
- A focused headless harness scenario applies two egg HP decreases, observes the icon scale rise above its base and return to it, and finishes with status pass and all expectations green.

## ⬜ Pending
- (none)

## ❌ Impossible
- (none)
