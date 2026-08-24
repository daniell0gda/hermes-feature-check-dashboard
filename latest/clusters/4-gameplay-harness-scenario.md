# Cluster 4: gameplay-harness-scenario

- files: `tests/scenarios/floodgate_corrosive_soak.json`, `scripts/testing/HarnessValues.gd`
- dependencies: 2
- parallel: false

## Acceptance criteria

- The headless gameplay harness stages a live armored enemy deterministically — the enemy is confirmed alive and at its staged armor value immediately before each hit action, using the max-armor-per-id report field rather than index-0 lookup — so no hit or observation ever targets an absent or unarmored spawn.
- The headless gameplay harness proves end-to-end: with the perk applied at each level, a Floodgate discharge hit followed by another tower's armor-damage hit yields the level's amplified armor loss on the same enemy setup, and a matching unowned-perk control run yields no amplification.
- The headless gameplay harness proves isolation on the same enemy setup: after the Floodgate discharge hit, a second Floodgate-sourced hit's armor effect matches the unowned-perk control run.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/floodgate_corrosive_soak.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/tower/test_tower_armor_damage.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Notes (r1/r2 failure history): r1 timed out on `enemies.Alien.armor == 950.0` seeing 0.0 (observed an unarmored index-0 spawn); fresh r2 run times out earlier at `enemies.corroded_count == 1.0` because `set_armor`/`floodgate_hit` report "no live enemy at index 0". Re-run the focused harness fresh after any scenario fix and inspect raw stdout for Parse Error / Failed loading resource, not just harness status.
