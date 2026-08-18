## ✅ Done
- `porter_one_way_ticket` is a Unique Porter perk that starts at level 0 and eligible, appears in a full chest draw while eligible, becomes level 1 after it is applied, and is absent from a full chest draw after that single level is taken.
- `porter_one_way_ticket` and `porter_wide_gate` keep independent state: taking either one does not own or change the other's level or effect.
- An owned `porter_one_way_ticket` remains owned after progression save and reload, and `reset_for_new_game` returns it to unowned.
- The existing `porter_wide_gate` progression contract still passes after `porter_one_way_ticket` is added.
- When `porter_one_way_ticket` is unowned, a successful Porter teleport moves a non-boss enemy underground without applying Stun or Wet.
- When `porter_one_way_ticket` is owned, a successful Porter teleport leaves a non-boss enemy underground with Stun remaining time greater than 0.8s and at most 1.0s and Wet remaining time greater than 2.8s and at most 3.0s immediately after underground arrival.
- After that owned teleport, Stun is gone by 1.1s while Wet remains, and Wet is gone by 3.1s.
- Owning `porter_one_way_ticket` does not apply Stun or Wet to an enemy that is underground without a Porter teleport.
- While Stun and Wet remaining times are both positive, the existing enemy health-bar status row shows the Stun icon and the Wet icon.
- Debug-build [PORTER] log line per one-way-ticket apply

## ⬜ Pending

## ❌ Impossible
