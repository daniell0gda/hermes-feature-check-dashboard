## ✅ Done
- `porter_overcharged_rings` is a Common Porter perk that starts at level 0 and eligible, appears in a full chest draw while eligible, reaches levels 1, 2, and 3 with descriptions that include 10%, 20%, and 30% charge-time cuts, and is absent from a full chest draw after level 3.
- While unowned, Porter charge time required to teleport is the unshortened charge window; after levels 1, 2, and 3 that required time is 90%, 80%, and 70% of the unowned window; applying level 3 again leaves the required time at 70%.
- `porter_overcharged_rings`, `porter_wide_gate`, and `porter_boss_runner` keep independent state: taking any one does not own or change the others' levels or effects.
- An owned `porter_overcharged_rings` level and its shortened charge time remain after progression save and reload, and `reset_for_new_game` returns the perk to unowned and the charge time to the unshortened window.
- A Porter already placed before `porter_overcharged_rings` is taken uses the shortened charge time without being replaced.
- When `porter_overcharged_rings` is unowned, a required focused-scenario wait covering less than the unowned Porter charge window observes the in-range surface target still on the surface.
- When `porter_overcharged_rings` is at level 3, a required focused-scenario wait covering less than the unowned Porter charge window and more than 70% of that window observes that in-range surface target teleported underground.
- Debug-build [PorterProgression] log line per overcharged-rings apply
- Debug-build [PORTER] log line per charge-complete
- The existing `porter_wide_gate` progression contract still passes after `porter_overcharged_rings` is added.
- The existing chest-pool draw contract still passes after `porter_overcharged_rings` is added to the eligible Common pool.

## ⬜ Pending

## ❌ Impossible
