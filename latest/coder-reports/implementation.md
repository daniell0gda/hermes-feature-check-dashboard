# Coder report: implementation (revision 2 redo, task code)

## Changed files
- `scripts/ui/UI.gd` — modified: `_spawn_upgrade_money_popup` now routes its anchor through a new `_adjust_anchor_out_of_upgrade_panel(anchor)` occlusion check; added the `[UPGRADE_POPUP]` debug log line.
- (`tests/scenarios/upgrade_click_money_popup.json`, `scripts/game/ChestRewardSystem.gd`, `scripts/testing/HarnessValues.gd` unchanged this iteration — carried over from the previous iteration's already-green work.)

## Criteria
- Clicking Upgrade spawns chest-style "+N coins!" popup — Done (verified previously + re-run this iteration)
- Popup stays outside details-panel rect when tower is occluded — Done (new)
- Unoccluded popup anchors above tower unchanged — Done (guard clause returns anchor untouched when panel off / point outside rect)
- Same yellow Label3D factory/timing, popup frees within ~2 s — Done
- Level increments, exact cost charged — Done
- Chest compatibility scenario unchanged and passing — Done
- Debug-build `[UPGRADE_POPUP]` log per adjustment naming tower screen pos and adjusted anchor — Done (new)

## Commands and results (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-upgrade-click-money-animation)
- `godot --version` — exit 0; 4.4.1.stable.official.49a5bc7b6
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` — exit 0; status=pass, all 3 expectations pass. Live occlusion path exercised: `[UPGRADE_POPUP] tower screen (803.84, 403.60) inside panel [P: (770, 172), S: (380, 502)] - anchor moved (-5.684, 1.5, -5.213) -> (8.371838, 1.5, -5.212997)` followed by `[CHEST REWARD] Created popup for 20 coins at (8.371838, 1.5, -5.212997)`; final `reward_popups == 0`.
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/chest_reward_compatibility.json` — exit 0; status=pass (chest popup for 33 coins on the unmodified factory path).
- `godot --headless --path . --editor --quit-after 300` — exit 0; parse clean, no script errors.

## Notes
- Occlusion algorithm: if the popup anchor projects inside `upg_panel.get_global_rect()`, project a cleared screen point 40 px past the nearer panel edge back onto the horizontal plane at the anchor's height (`Plane(Vector3.UP, anchor.y)` via `project_ray_origin/normal`) and spawn there. Guard clauses return the original anchor when there is no camera, the panel is hidden, or the projected point is outside the rect — so unoccluded towers behave exactly as before.
- Adjustment only runs on the surface layer (`GameState.current_layer != "underground"`); the details panel is a surface HUD concept.
- Gotcha for tester: the focused scenario deselects the tower before the "after" screenshot (so the popup is unoccluded for pixel-scan), but the headless log above proves the occluded branch fires while the panel is open at click time. Windowed recapture must budget ≥ ~2 s of TransitionUtils fade-in before screenshots.
