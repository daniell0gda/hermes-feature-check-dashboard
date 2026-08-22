# Coder report: 1-attunement-perk-and-effectiveness

## Changed files
- `scripts/progression/global.json` — new `elemental_attunement` Unique (maxLevels 3, forceVisibility false, one absolute `element` per level: fire/water/electric). Eligible for chest/cave draws through the existing Uniques pool path; no new eligibility code needed.
- `scripts/progression/managers/AttunementProgressionManager.gd` — new manager. Each `apply_level` sets `_element` to the level's absolute `element`; `reset()` clears it. Debug-build prints `[ELEMENTAL_ATTUNEMENT] apply L<n> -> element=<el>`.
- `autoload/ProgressionManager.gd` — registers the attunement handler (`ATTUNEMENT_PM_SCRIPT`, delegation in `_apply_to_handler`, reset in `reset_for_new_game`) and exposes public `get_attuned_element() -> String` ("" while unowned).
- `scripts/config/Balance.gd` — new const `attunement_coverage` ({fire:{Water,Electric}=2.0, water:{Fire,Electric}=2.0, electric:{Fire,Water}=2.0}) and static `get_attunement_multiplier(attacker_type, defender_type, owned_element)` returning 1.0 when unowned/unchosen/uncovered. Base `type_effectiveness` untouched.
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — `_compute_effectiveness` now applies the attunement multiplier only when it grants coverage (>1.0), never over a defender resistance and never on a same-element pair, so every self-resistance (0.5x fire/fire, 0.5x water/water, 0.3x electric/electric) is preserved.
- `scripts/testing/HarnessActions.gd` — added deterministic `water_hit` seam mirroring the existing `fire_hit`/`electric_hit`.

## Criteria
All 8 cluster-1 criteria — Done (verified by focused harness run, see cluster 2 report for command results).

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit code 0; project imports/parses cleanly.
- Focused harness — see cluster 2 report; status `pass`, all expectations green including `[ELEMENTAL_ATTUNEMENT]` log assertion.

## Notes
- KEY FIX this iteration: the previous implementation **overwrote** the base multiplier with `get_attunement_multiplier(...)` whenever attacker != defender type. That helper returns 1.0 when the perk is unowned, which silently clobbered base-matrix entries like fire→Water 0.5 (observed as a "10 damage" baseline instead of 5 in the first failing run). Fix: apply the attunement multiplier only when it is >1.0 (i.e., it grants extended coverage); otherwise keep the base/resistance value.
