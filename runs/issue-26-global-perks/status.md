## ✅ Done
- With neither Common owned, the global damage multiplier and the global attack-speed multiplier are both 1.0.
- Applying `tower_dmg` once, twice, then three times sets the global damage multiplier (and the generic and bazooka tower damage multipliers) to 1.05, then 1.10, then 1.20, each step strictly greater than the last, and that level's description states the matching +5% / +10% / +20% bonus.
- Applying `tower_atk_speed` once, twice, then three times sets the global attack-speed multiplier to 1.05, then 1.10, then 1.20, each step strictly greater than the last, and that level's description states the matching +5% / +10% / +20% bonus.
- After `tower_dmg` and `tower_atk_speed` are each at level 2, a progression save and reload leaves both multipliers at 1.10.
- `reset_for_new_game` returns both global multipliers to 1.0.
- On an unupgraded Generic tower whose stored damage is 3.0, one tower upgrade raises stored damage to 4.8; `tower_dmg` at level 3 multiplies outgoing damage by 1.20, which is less than that upgrade's 1.60.
- Debug-build [PROGRESSION] log line per global aggregate apply
- Applying `tower_dmg` does not change trap hit damage.
- After `tower_atk_speed` level 1 is owned, the progression attack-speed aggregate stays 1.05 while Overheat's burst fire-rate multiplies on top of that aggregate.
- After three `tower_dmg` picks and one `tower_atk_speed` pick, a defeat Try Again returns both global multipliers to 1.0 and makes `tower_dmg` eligible again.

## ⬜ Pending

## ❌ Impossible
