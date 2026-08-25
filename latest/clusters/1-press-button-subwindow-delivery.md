# Cluster 1: press-button-subwindow-delivery

- owned file scope: `scripts/testing/HarnessActions.gd`
- dependencies: none
- parallel: true

## Acceptance criteria

- When `press_button` targets a BaseButton inside an embedded `Window` (subwindow) that is enabled and visible in tree, the button's own `pressed` signal fires as a result of the delivered synthetic press (observable via connected handler side effects such as the modal closing).
- When `press_button` cannot actually deliver the press (target disabled, not visible in tree, or delivery fails), its returned detail reports `landed` as false together with a reason string naming why.
- `landed=true` is only reported after the synthetic press was confirmed delivered through the button's viewport GUI path, so the field cannot read as success without delivery.
- `press_button` on a Button inside an embedded subwindow whose handler disables or frees the button between mouse-down and mouse-up does not fire the `pressed` signal, matching real-click behaviour.
- Debug-build `[HARNESS-CLICK]` log line per press_button attempt names the target, whether the event was delivered to the button's viewport, and the landed outcome with reason when not delivered.
- `hud_controls_state` scenario still passes unchanged after the press_button delivery changes (root viewport presses unaffected).

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/reward_panels_visual.json"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "2"]`
