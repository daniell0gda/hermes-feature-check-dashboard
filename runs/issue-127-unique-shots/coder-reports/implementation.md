# Coder report: implementation (revision 2)

Task: revision-code-1, cluster "implementation" — redo the failed criteria from the
revision-1 check (forced-Unique windowed scenario fixture + visual closeout prep).

## Changed files
- `tests/scenarios/progression_modal_wood_frame.json` — modified

## Criteria
- Forced-Unique scenario fixture — Done (automated evidence below)
- Windowed screenshots / `.gen/manual-report.md` — NOT DONE BY THIS WORKER: the
  runner executes headless (`HarnessScreenshot.is_available()` is false), so both
  screenshot checkpoints correctly report `skipped`. request.md itself assigns
  windowed capture and manual judging to the windowed/manual-tester worker; the
  code-side deliverable was making the scenario *force* a Unique offer so those
  shots actually contain one. That precondition now holds and is proven.

## Commands and results (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-window-modals-skip-wood-frame)
- `["git","status","--short"]` — exit 0. Runner reachable.
- `["godot","--headless","--path",".","res://tests/ui/test_titled_panel_close_corner.tscn"]`
  — exit 0, "58 ok, 0 failed", "every test ran to completion (17 of 17)".
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--",
  "--harness=res://tests/scenarios/progression_modal_wood_frame.json"]`
  — exit 0, `[Harness] status=pass exit=0`. Log shows the forced draw:
  `[Progression] draw_choices_for_chest: force_mode=true, flagged=1, normal=45,
  chosen=1` then `[PROGRESSION_MODAL] open money=50 options=2` with **no**
  invalid-theme-override errors. RewardsModal opened over 1 selection
  (`[REWARDS_MODAL] open trigger=rewards_button selections=1`, scifi_overclock).
  Both screenshots report outcome=skipped (headless renderer — expected).
- `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  — exit 0. Import/typecheck gate green.

## Notes for tester/manual-tester
- The forcing mechanism: map_10 puts Sci-Fi on its build roster, which makes the
  only forceVisibility perk (`scifi_overclock`) chest-compatible; while any
  forceVisibility perk is chest-eligible, `draw_choices_for_chest` is
  flagged-exclusive, so the modal's upgrade cards are exactly that Unique.
- Two inline `wait_for_condition` probes assert this BEFORE the chest opens:
  a 100-draw contains probe on `scifi_overclock` and a 2-draw !contains probe on
  `"type": "Common"`. An unmet precondition aborts the timeline instead of
  photographing a Common-only modal (the revision-1 failure mode).
- After the modal shot the timeline closes the modal via
  `progression_modal verb=close` (restores pre-open pause state), grants
  scifi_overclock directly, and opens RewardsModal via `_on_rewards_pressed`,
  producing checkpoint `panel_rewards_unique` — the listed-selections shot with a
  stored-type Unique card wearing the shared gold border/badge treatment.
- Run WINDOWED (never --headless) to get real pixels:
  `.gen/harness/progression_modal_wood_frame/shots/progression_modal_wood_frame.png`
  and `.../shots/panel_rewards_unique.png`; copy fresh PNGs to `.gen/screenshots/`.
- Pre-existing noise, not from this diff: invalid-UID ext_resource warnings in
  HudTheme.tres/UI.tscn, dummy-renderer RID-leak errors at exit, unimported-GLB
  load failures headless.
