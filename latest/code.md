# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/progression/scifi_tower.json` — new
- `scripts/progression/managers/ScifiTowerProgressionManager.gd` — new
- `autoload/ProgressionManager.gd` — modified
- `scripts/game/actors/towers/ScifiTower.gd` — modified
- `scripts/game/TowerManager.gd` — modified
- `tests/scenarios/scifi_capacitor_bank_progression.json` — new
- `tests/scenarios/scifi_capacitor_bank.json` — new
- `tests/scenarios/progression_chest_pool.json` — modified (remeasured pins)

## Criteria
- At a fresh run start, `scifi_capacitor_bank` is eligible at level 0. — Done
- A 100-choice chest draw includes `scifi_capacitor_bank` while it is eligible. — Done
- Applying `scifi_capacitor_bank` three times reaches Common levels 1, 2, then 3; after level 3 the perk is no longer eligible and a 100-choice chest draw does not include it. — Done
- While unowned, the Sci-Fi re-engage value the tower reads is the unperked baseline; levels 1, 2, and 3 each apply a strictly larger tunable cut from that perk's level data; applying the perk again at level 3 does not compound past level 3. — Done
- After `save_now` plus `_load_state_and_apply`, `scifi_capacitor_bank` remains level 3 with the same level-3 cut; after `reset_for_new_game` it is level 0 and the unperked baseline is restored. — Done
- Debug-build [CAPACITOR_BANK] log line per perk-level apply. — Done (print in apply_level; stdout shows L1/L2/L3; out.log not asserted)
- After `scifi_capacitor_bank` is added to the progression catalog, `progression_chest_pool` still passes (remeasure seeded draw pins if the pool order changes). — Done
- Without owning `scifi_capacitor_bank`, after a Sci-Fi tower switches aim to a target outside the unowned yaw gate, the beam stays off until yaw is aligned and does not resume in the short window that level 3 uses. — Done
- With `scifi_capacitor_bank` at level 3, after the same kind of aim switch on a Sci-Fi tower already on the map, the beam resumes on the new target in a shorter window than the unowned yaw wait. — Done
- A Sci-Fi tower with `scifi_capacitor_bank` owned still deals scifi damage through the existing beam on a live wave. — Done
- Debug-build [CAPACITOR_BANK] log line per beam re-engage after a yaw wait. — Done (print on re-engage; stdout shows it)

## Commands and results
- `["godot", "--version"]` — exit code 0; 4.4.1.stable.official
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; registered ScifiTowerProgressionManager
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_capacitor_bank_progression.json"]` — exit code 0; `.gen/harness/scifi_capacitor_bank_progression/result.json` status pass
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_chest_pool.json"]` — exit code 0 after pin remeasure; `.gen/harness/progression_chest_pool/result.json` status pass
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_capacitor_bank.json"]` — exit code 0; `.gen/harness/scifi_capacitor_bank/result.json` status pass

## Notes
- Cuts are absolute 0.25/0.50/0.75. `get_scifi_yaw_tolerance(base)` lerps base deg toward 180. L0=8, L1=51, L2=94, L3=137.
- `TowerManager.offset_yaw_half_turn_at` is the one-shot harness snap; `wait_for_condition` cannot snap because it would re-fire every frame.
- Native Godot did not populate harness out.log for `[CAPACITOR_BANK]`; prints are in engine stdout. Porter overcharged rings documented the same trap.
- Chest-pool pins after adding the perk: trailing draw `curse_blood_money`; opened chest awarded `tower_dmg` (was `fire_flashover` / `chest_duplication`).
- Full suite `smoke_tower_roster` not run here; checker owns that.
\n