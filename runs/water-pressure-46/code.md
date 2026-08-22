# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `tests/scenarios/progression_pick.json` — modified (full-suite regression fix)
- `autoload/ProgressionManager.gd` — modified (prior iteration, unchanged this round)
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — modified (prior iteration)
- `scripts/progression/managers/WaterTowerProgressionManager.gd` — modified (prior iteration)
- `scripts/progression/water_tower.json` — modified (prior iteration)
- `scripts/ui/UI.gd` — modified (prior iteration)
- `tests/scenarios/water_pressure_progression.json` — new (prior iteration)

## Criteria
All nine plan criteria — Done (implementation from prior iteration retained; this round fixed the full-suite gate that had them demoted to Pending).

## Commands and results
- Focused: exit 0; `.gen/harness/water_pressure_progression/result.json` status=pass; `[WATER-PRESSURE] water_pressure L1 -> bonus=0.20 / L2 -> 0.35 / L3 -> 0.50` in stdout.
- Full: exit 0; `.gen/harness/progression_pick/result.json` status=pass; all four expectations pass (`venom_miasma_bloom=1`, `get_venom_miasma_config.enabled=true`, `paused=false`, `current_layer=underground`). Previously failing reproducibly at exit 1.
- Typecheck/build: exit 0; scripts parse cleanly (only pre-existing HudTheme UID/texture warnings).

## Notes
- Root cause of the full-suite regression: `water_tower.json` gained a new chest-eligible Common perk (`water_pressure`), which enlarged the chest draw pool in `progression_pick`. That scenario exhausts every other eligible perk to leave a deterministic 2-card pool `{venom_neurotoxin, venom_miasma_bloom}`; with the pool enlarged, the seeded pick missed venom_miasma_bloom and fallback index 0 grabbed the money card. Fix: added three `apply_progression(water_tower.json, water_pressure)` exhaustion calls to `progression_pick.json` right after the existing `water_deep_soak` block (mirrors how every other perk is exhausted).
- Gotcha for testers/future coders: any change adding or removing a chest-eligible progression entry must update the exhaustion list in `tests/scenarios/progression_pick.json`, otherwise its seeded draw breaks deterministically.
- Minor: the patch tool re-indented the inserted JSON block deeper than surrounding entries; JSON validity confirmed (`json.load` OK) and Godot parses it fine — cosmetic only.
\n