## ✅ Done

## ⬜ Pending
- A case-sensitive search for `_find_suitable_cave_position` under `scripts/` returns zero matches after the change.
- The only cave-placement lookup used at runtime remains the shared helper (`CaveUtils.find_suitable_cave_position`); no second same-named placement routine exists anywhere under `scripts/`.
- The `cave_discovery_chance` scenario passes a fresh headless harness run (status pass, exit code 0, all expectations met).
- The `cave_discovery_long_carve` scenario passes a fresh headless harness run (status pass, exit code 0, all expectations met).
- The `cave_discovery_pending_placement` scenario passes a fresh headless harness run (status pass, exit code 0, all expectations met).
- The headless editor parse gate completes without script parse or class-cache errors after the removal.

## ❌ Impossible
