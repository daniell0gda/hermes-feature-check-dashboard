## ✅ Done
- After the last remaining enemy in a discovered cave with has_boss true is killed, that cave has an unopened chest, has_chest is true, and single_perk_chest_count is at least 1.
- Opening that boss-clear chest offers exactly one perk and no money option; a seeded Unique branch grants a Unique perk and a seeded Common branch grants a Common perk.
- After the last remaining enemy in a discovered non-boss enemy cave is killed, that cave has no chest and single_perk_chest_count is 0.
- Debug-build [CAVE] log line per boss-clear chest grant with cave id
- After a discovered cave spawner completes its wave_lifetime waves, that cave has has_spawner false, has_chest true, and spawner_lifetime_expired true, and single_perk_chest_count is at least 1.
- Opening that lifetime-converted chest offers exactly one perk and no money option, and a seeded run grants a perk whose rarity matches that seed's 40% Unique roll.
- Per-cave harness observations report has_chest and spawner_lifetime_expired.
- The existing spawner_lifetime_and_discovery_confirmation scenario still passes.

## ⬜ Pending

## ❌ Impossible
