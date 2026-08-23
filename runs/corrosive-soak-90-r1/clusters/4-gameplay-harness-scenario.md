# Cluster 4: gameplay-harness-scenario

- files: `tests/scenarios/floodgate_corrosive_soak.json`
- dependencies: 2
- parallel: false

## Acceptance criteria

- The headless gameplay harness proves end-to-end: with the perk applied at each level, a Floodgate discharge hit followed by another tower's armor-damage hit yields the level's amplified armor loss on the same enemy setup, and a matching unowned-perk control run yields no amplification.
- The headless gameplay harness proves isolation on the same enemy setup: after the Floodgate discharge hit, a second Floodgate-sourced hit's armor effect matches the unowned-perk control run.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/floodgate_corrosive_soak.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/tower/test_tower_armor_damage.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Notes: use the exact scene argument before user args; inspect raw Godot stdout for Parse Error / Failed loading resource, not just harness status=pass.
