## ✅ Done
- The new Unique perk `water_riptide` is defined in the Water tower progression file as a single-level Unique compatible with the water tower, and is grantable through the normal progression flow (`apply_progression` raises its level from 0 to 1, and further grants are refused once owned).
- After `reset_for_new_game`, `water_riptide` is unowned again and has no gameplay effect until re-granted.
- With `water_riptide` owned, a Water tower projectile hit on an enemy applies a Slow of 20% magnitude lasting 1.5 seconds, observable as reduced enemy movement speed for that window while the Wet status continues as before.
- Without `water_riptide` owned, Water hits apply no slow; enemy movement speed and existing Wet behaviour are unchanged from before this feature.
- While an enemy's slow is owned by another tower instance (e.g. Ice), a Water hit does not overwrite or steal the active slow; when Water itself owns the active slow, subsequent Water hits refresh it to 20% / 1.5s rather than stacking.
- Debug-build `[RIPTIDE]` log line per water-triggered slow application, naming the enemy id, slow magnitude, duration, and owning tower instance id.
- When a Water hit triggers the Riptide slow, the enemy shows the existing Chilled visual cue (IceSlowFX snowflake particles plus ice-tint overlay) driven by the existing status-controller visuals, with no new VFX asset added; the cue clears when the slow expires.
- The existing shared hit-path scenario (`water_electric_hit_path`) still passes: Water and Electric hits continue to land damage and apply their existing effects alongside the new optional slow.

## ⬜ Pending

## ❌ Impossible
