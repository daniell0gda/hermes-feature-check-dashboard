## ✅ Done
- A full chest draw with no Fire tower placed and no Fire-tower unlock or perk available does not include fire_flashover.
- A full chest draw after a Fire tower is placed includes fire_flashover while that reward remains otherwise eligible.
- A full chest draw with no Fire tower placed still includes fire_flashover when a currently available perk or unlock can add a Fire tower.
- Gold and Common rewards stay in chest generation under this compatibility filter, including fire_burn with no Fire tower placed.
- After fire_burn is owned, fire_wildfire_spread is absent from a full chest draw with no Fire tower and present after a Fire tower is placed.
- Unique rewards that declare no tower, element, or mechanic compatibility stay eligible regardless of placed towers.
- When compatibility filtering removes every non-Gold/Common candidate, chest generation still offers a non-empty valid choice set that includes Gold and/or Common rewards.
- Debug-build [PROGRESSION] log line per incompatible chest reward skip
- A full chest draw remains non-empty, keeps tower_dmg while it is eligible, and omits a progression after that progression has been taken.
- fire_flashover still starts unowned, applies to levels 1–3 with the existing flashover config values, and does not change fire_burn state.

## ⬜ Pending

## ❌ Impossible
