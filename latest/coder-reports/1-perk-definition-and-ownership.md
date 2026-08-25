# Coder report: 1-perk-definition-and-ownership

## Changed files
- `scripts/progression/porter_tower.json` — new `porter_mass_transit` entry: type Unique, maxLevels 0, compatibility towers [porter], forceVisibility false.
- `scripts/progression/managers/PorterTowerProgressionManager.gd` — MASS_TRANSIT_NAME const, `_mass_transit_owned` state, `can_handle`, idempotent `_apply_mass_transit()`, `is_mass_transit_owned()` accessor, reset().
- `autoload/ProgressionManager.gd` — passthrough `is_porter_mass_transit_owned()`.

## Criteria
- Unique entry for porter only, maxLevels 0 (idempotent single toggle) — Done
- Unowned: API reports unowned / level 0 / chest-eligible once a Porter is placed — Done (covered by focused scenario arm 1)
- One apply -> owned level 1; repeat stays level 1; drops from chest draw while owned — Done (scenario applies twice, asserts owned + level==1; maxLevels 0 makes it ineligible at cur>0 per ProgressionManager.is_eligible line 111)

## Commands and results
- `godot --headless --path . --import` — exit 0
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/porter_mass_transit.json` — exit 0; `[Harness] status=pass`; stdout shows `[PORTER_MASS_TRANSIT] owned`
- Regression slice `python3 tests/run_all_shard.py 0 1 progression` — 15 PASS / 5 FAIL; all 5 failures reproduced identically with changes stashed (pre-existing, see cluster 2 report).

## Notes
- Chest eligibility of the perk itself while unowned follows existing `_is_chest_compatible` (Unique + towers[porter] requires a placed porter); scenario asserts pool behavior indirectly via eligibility APIs.
