# Cluster 4: elemental-progression-tuning

- owned_files: autoload/ProgressionManager.gd` or perk config data, `tests/scenarios/scifi_overclock.json`, `tests/scenarios/scifi_overclock_progression.json`, `tests/scenarios/scifi_capacitor_bank.json`, `tests/scenarios/scifi_piercing_beam_progression.json`, `tests/scenarios/water_deep_soak_progression.json`, `tests/scenarios/floodgate_cryobrine_progression.json
- depends_on: none
- parallel: true

## Acceptance criteria

A fresh run of `scifi_overclock` reports its overclock effect within the scenario timeout and exits with status pass, code 0.
The `scifi_overclock_progression` scenario's observed progression value matches the scenario's expected value exactly (currently 1.5 vs expected 1.4); whichever side is stale, game code or scenario notes[] record which value is correct and why.
Fresh runs of `scifi_capacitor_bank`, `scifi_piercing_beam_progression`, `water_deep_soak_progression`, and `floodgate_cryobrine_progression` each report status pass with exit code 0.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/<primary-scenario>.json"]
- Full test: ["bash", ".gen/run_full_suite.sh"]
- Typecheck/build: ["godot", "--headless", "--editor", "--path", ".", "--quit-after", "3"]
