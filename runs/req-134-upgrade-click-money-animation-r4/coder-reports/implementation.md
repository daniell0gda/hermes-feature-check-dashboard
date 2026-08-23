# Coder report: implementation (task revision-code-1)

## Changed files
- `scripts/testing/HarnessScreenshot.gd` — modified: optional `downscale` on capture_checkpoint; burst frames resized before save so PNG encode cost no longer blows the ~0.2s interval.
- `scripts/testing/AgentHarness.gd` — modified: `_burst_capture` reads a `downscale` field and threads it into each checkpoint action.
- `tests/scenarios/upgrade_click_money_popup_gif.json` — modified: burst now `"interval_sec": 0.2, "span_sec": 2.0, "time_scale": 0.2, "downscale": 0.5`.
- Carried over from the previous iteration (already in tree, verified this run): `scripts/ui/UI.gd` debug-gated `[UPGRADE_POPUP]` prints + projected-anchor log + occlusion getters; `tests/scenarios/upgrade_click_money_popup.json`, `tests/scenarios/upgrade_click_money_popup_unoccluded.json`.

## Criteria
- Windowed burst starts immediately after click, ~0.2s interval, ≥1.5s span, no pre-waits — Done (mechanism): fresh gif run logs `[UPGRADE_POPUP_BURST] 11 frames over 1.985s (first at 0.000s, engine time_scale 0.20)`, wall gaps 0.197–0.208s; scenario fires `_on_upgrade_pressed` immediately before the burst (no wait between). Final windowed pixel confirmation is the manual tester's step (runner is headless).
- Run output logs projected screen coords of popup anchor — Done: `[UPGRADE_POPUP] popup anchor (8.371838, 1.5, -5.212997) projected screen (1190.0, 403.5959)` in fresh focused-run log.
- Debug-build `[UPGRADE_POPUP]` log per occlusion adjustment gated on OS.is_debug_build(); release prints nothing — Done: both print sites wrapped in `if OS.is_debug_build():`; occlusion line fired in headless debug runs.
- Non-occluded branch exercised by test with unchanged anchor asserted — Done: upgrade_click_money_popup_unoccluded.json status=pass; expectations include `was_last_upgrade_popup_occluded == false` and `get_last_upgrade_popup_screen_pos != (0,0)`; log shows anchor untouched (`popup anchor (-9.4, 1.5, -5.213) projected screen (701.7532, 403.5959)`, no "inside panel" line).
- Headless harness upgrade_click_money_popup.json keeps passing — Done: status=pass, 3/3 expectations.
- Chest compatibility unchanged — Done: chest_reward_compatibility.json status=pass.
- Windowed yellow-cluster-at-anchor pixel evidence — Pending for tester: mechanism ready (burst cadence fixed, projected coords logged); needs windowed recapture + crop inspection.

## Commands and results (all via run_project_cmd, project=poke-defense-godot)
- `godot --version` — exit 0; 4.4.1.stable.official.49a5bc7b6
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` — exit 0; status=pass; both gated [UPGRADE_POPUP] lines present.
- Full: same command with chest_reward_compatibility.json — exit 0; status=pass.
- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0; parse clean (HarnessScreenshot/HarnessValues docs regenerated, no script errors).
- Extra: upgrade_click_money_popup_unoccluded.json — exit 0; status=pass, 4/4 expectations.
- Extra: upgrade_click_money_popup_gif.json — exit 0; status=pass; burst detail above.

## Notes
- Root cause of the old slow cadence: full-HD viewport grab+PNG save costs >1s wall per frame under software rendering, dwarfing interval_sec 0.2. Half-resolution frames cut encode cost enough for the loop to keep pace in headless timing; windowed confirmation still required.
- Gotcha: `downscale` applies only to burst frames; regular screenshot checkpoints stay full-res.
