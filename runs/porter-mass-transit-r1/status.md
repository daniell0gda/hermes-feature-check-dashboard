## ✅ Done
- The progression catalog defines `porter_mass_transit` as a Unique entry for the porter tower only, with no levels (`maxLevels: 0`) so applying it is an idempotent single toggle.
- With the perk not owned, the progression API reports it unowned, reports level 0, and marks it chest-eligible once a Porter covers its compatibility requirement.
- After one `apply_progression` call for `porter_mass_transit`, the progression API reports it owned at level 1, repeat applications stay at level 1, and it drops out of the chest draw while owned.
- Without `porter_mass_transit` owned, a fully charged Porter teleports only its locked target; other nearby surface enemies are untouched (existing single-target behaviour preserved).
- With `porter_mass_transit` owned, when charge on the locked target completes, every OTHER surface enemy within a tight (~path-width) radius of the locked target's position begins the same teleport to underground as the locked target.
- Enemies that are dead, already underground, or beyond the tight sweep radius at charge-completion time are never swept by the mass transit teleport.
- Each candidate swept enemy gets its own underground-route validity check; candidates without a valid route are skipped and stay alive on the surface path while valid ones still teleport.
- Every additional swept enemy receives the same per-enemy feedback as the locked target (porter rings visual, teleport burst effect, dissolve animation); no swept enemy teleports without visible feedback.
- Debug-build [PORTER_MASS_TRANSIT] log line per mass-sweep event naming the locked target plus the count of additionally swept enemies.
- A focused harness scenario `tests/scenarios/porter_mass_transit.json` proves perk-off vs perk-on sweep behaviour on map_6 path geometry and passes headless with status=pass.

## ⬜ Pending

## ❌ Impossible
