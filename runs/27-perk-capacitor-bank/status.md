## ✅ Done
- At a fresh run start, `scifi_capacitor_bank` is eligible at level 0.
- A 100-choice chest draw includes `scifi_capacitor_bank` while it is eligible.
- Applying `scifi_capacitor_bank` three times reaches Common levels 1, 2, then 3; after level 3 the perk is no longer eligible and a 100-choice chest draw does not include it.
- While unowned, the Sci-Fi re-engage value the tower reads is the unperked baseline; levels 1, 2, and 3 each apply a strictly larger tunable cut from that perk's level data; applying the perk again at level 3 does not compound past level 3.
- After `save_now` plus `_load_state_and_apply`, `scifi_capacitor_bank` remains level 3 with the same level-3 cut; after `reset_for_new_game` it is level 0 and the unperked baseline is restored.
- Debug-build [CAPACITOR_BANK] log line per perk-level apply.
- After `scifi_capacitor_bank` is added to the progression catalog, `progression_chest_pool` still passes (remeasure seeded draw pins if the pool order changes).
- Without owning `scifi_capacitor_bank`, after a Sci-Fi tower switches aim to a target outside the unowned yaw gate, the beam stays off until yaw is aligned and does not resume in the short window that level 3 uses.
- With `scifi_capacitor_bank` at level 3, after the same kind of aim switch on a Sci-Fi tower already on the map, the beam resumes on the new target in a shorter window than the unowned yaw wait.
- A Sci-Fi tower with `scifi_capacitor_bank` owned still deals scifi damage through the existing beam on a live wave.
- Debug-build [CAPACITOR_BANK] log line per beam re-engage after a yaw wait.

## ⬜ Pending

## ❌ Impossible
