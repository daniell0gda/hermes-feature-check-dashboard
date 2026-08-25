# Cluster 2: reward-scenario-uses-press-button

- owned file scope: `tests/scenarios/reward_panels_visual.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- The `reward_panels_visual` scenario drives its Unique-card pick with a `press_button` action targeting the second card's Accept button instead of `progression_modal verb=choose_option`, and passes only when the `[PROGRESSION_MODAL] close path=accept:upgrade` log line appears.
- The `reward_panels_visual` scenario asserts the RewardsModal corner-close press (`UI/Root/RewardsModal/Center/Panel/CloseChip`) produces the `[REWARDS_MODAL] close trigger=corner_close` log line.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/reward_panels_visual.json"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`
