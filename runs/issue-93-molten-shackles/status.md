## ✅ Done
- `molten_shackles` is a Common Fire-progression perk with 3 levels; applying it three times reaches levels 1, 2, then 3, after which it is no longer eligible, and after `reset_for_new_game()` it is back at level 0 with its config inactive.
- At any `molten_shackles` level, when no Fire burn perk (`get_fire_burn_config()`) is active, the molten-shackles config reports inactive/zero effect.
- When a Fire burn perk is active together with `molten_shackles`, the exposed armor-strip config carries a flat per-tick base armor amount of 1 / 2 / 3 for shackles levels 1 / 2 / 3, scaled by the burn config's level multiplier.
- On an armored enemy with an active Fire burn perk and `molten_shackles` at level 1, burn ticks reduce the enemy's `armor` by the configured flat base amount scaled by the burn config's level multiplier, while the enemy stays alive.
- With a higher Fire burn perk level and the same `molten_shackles` level, each burn tick strips more armor than at the lower burn perk level.
- When no Fire burn perk is owned, burn ticks leave an armored enemy's `armor` unchanged (expected behaviour, covered by a dedicated game-test scenario).
- With a Fire burn perk owned but `molten_shackles` not taken, burn ticks leave an armored enemy's `armor` unchanged.
- Burn ticks under `molten_shackles` never reduce `armor` below 0, and do not change the HP damage dealt by those ticks compared with the same burn without the perk.
- Reusing existing burn visuals, no new VFX is introduced: burning an enemy with `molten_shackles` produces the same BurnStatus/BurnVFX presentation as burning without it.
- Debug-build [MOLTEN_SHACKLES] log line per burn tick that strips armor, naming the enemy, the armor before/after, and the applied strip amount.

## ⬜ Pending

## ❌ Impossible
