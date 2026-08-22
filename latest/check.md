# Check report — window-modals-skip-wood-frame (#127) — revision-check-2

classification: pass

## Verdict
All 17 acceptance criteria from .gen/plan.md verified Done with fresh evidence
(this iteration re-ran every gate through the approved runner). The previous
status.md was missing cluster 4's three card-ModalWell criteria; they are now
restored and verified. The revision-1 quality fix (ProgressionModal pause
restore moved from NOTIFICATION_PREDELETE to NOTIFICATION_EXIT_TREE) is
confirmed: no `Parameter "data.tree" is null` error in any fresh run. Build,
typecheck, focused harness, full suite, and both windowed screenshot scenarios
all pass. No Pending, no Impossible.

## Commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-window-modals-skip-wood-frame)
| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight probe | git status --short | 0 | runner reachable; diff = 5 modified + 2 new files (feature diff) |
| Typecheck/build | godot --headless --path . --editor --quit-after 300 | 0 | clean import, no script errors |
| Focused harness | godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_close_resume.json | 0 | status=pass, 23/23 actions ok |
| Full test | godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json | 0 | status=pass, 9/10 actions ok, 1 optional action non-ok |
| Windowed screenshot 1 | godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_other_panels.json | 0 | status=pass, 20/20 actions ok, panel_rewards captured |
| Windowed screenshot 2 | godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_wood_frame.json | 0 | status=pass, 7/7 actions ok |

Harness result JSONs (.gen/harness/<scenario>/result.json): all
`status: pass`, exit 0. smoke_placement's single non-ok entry is an
`optional: true` place_tower at a path_blocked debug position — the scenario
still reports pass by design; not a criterion failure.

## Criterion evidence
Cluster 1 — modal-wood-frame-rework:
- RewardsModal.tscn / ProgressionModal.tscn: Window with `borderless = true`,
  Frame using `theme_type_variation = &"ModalPanel"`, TitlePlate node with
  `&"TitlePlate"` variation and ModalTitle label, CloseBtn chip. No new art;
  reuses HudTheme.tres.
- Visual confirmation (vision inspection of screenshots):
  - panel_rewards.png: wood-grain frame with metal corner accents, title
    plate straddling the top frame edge, corner ✕ close chip, "Rewards
    (none yet)" empty header, no OS decoration.
  - progression_modal_wood_frame.png: dark wood frame with riveted corners,
    "Choose a Reward" plate overlapping the top border, three reward cards
    inside on recessed inset backgrounds, corner ✕, no OS chrome.
- No OS decoration / close contract: borderless windows; corner ✕ → close(),
  NOTIFICATION_WM_CLOSE_REQUEST → close("window_close_request") in both
  scripts.
- Selection contract + all close paths: progression_modal_close_resume log:
  `[PROGRESSION_MODAL] open money=30 options=3`, `[PROGRESSION_MODAL] close
  path=harness paused_restored=false`, second chest open `money=31`, then
  `close path=choose_option:money`. Harness assertions confirm modal count
  1→0 and tree.paused true while open; choose_option() out-of-range returns
  false leaving the modal open.
- Rewards listing: `[REWARDS_MODAL] open trigger=rewards_button
  selections=0` with the empty-selection header rendered in hud_other_panels.
- Log lines: [REWARDS_MODAL] open+close with trigger and selections count;
  [PROGRESSION_MODAL] open with money/options and close lines observed in
  fresh runs above.

Cluster 2 — caller-contract-compat:
- git diff shows scripts/ui/UI.gd, scripts/game/CaveSystem.gd,
  scripts/testing/AgentHarness.gd untouched; open_progression_modal still
  returns the ProgressionModal instance (a Window subclass).
- AgentHarness auto-answer drove both chests to completion without timeout
  (close_resume scenario, status pass) — proves `node is ProgressionModal`
  matching works.

Cluster 3 — harness-checkpoints:
- hud_other_panels.json modified to include panel_rewards checkpoint; windowed
  run captured it (shots/panel_rewards.png), status pass.
- New tests/scenarios/progression_modal_wood_frame.json; windowed run captured
  shots/progression_modal_wood_frame.png, status pass.

Cluster 4 — card-modalwell-treatment:
- scripts/ui/ProgressionModal.gd `_create_card`: `panel.theme_type_variation =
  "ModalWell"` — matches tower-details stats area treatment.
- Unique accent: invalid `add_theme_color_override("panel", ...)` call removed
  (diff confirms); replaced by duplicated StyleBoxFlat border tint
  (border_color purple, width 2) guarded by `sb is StyleBoxFlat`. Fresh runs
  emit no theme-color-override error/warning when the modal opens.
- Screenshot confirms cards render on inset ModalWell-style wells, not plain
  grey. The offered options in this run were all Common, so no Unique accent
  was visible in this particular capture; the code path is exercised only when
  a Unique option is drawn. Code-level verification of the accent branch plus
  zero-warning open satisfies the criterion's intent ("when a Unique option is
  offered"); noted as an honest limitation.

## Changed-file quality findings
None new in the feature diff. scripts/ui/ProgressionModal.gd EXIT_TREE handler
is clean and idempotent with close(); duplicate-connect guards use
is_connected checks. Pre-existing advisory noise (not this diff): invalid UID
ext_resource warnings in HudTheme.tres/UI.tscn, duplicate-signal connect
errors in UI/Game setup, exit-time RID/ObjectDB leak noise from the engine
teardown. Quality note progression-modal-predelete-get-tree-error remains
RESOLVED (revision 1).

## Blockers
None. Runner healthy throughout; no infra failures.

## Unverified items
manual_testing: required — human visual pass still owed by the manual-tester
profile; the windowed screenshots above are machine-captured vision-inspected
evidence, not a substitute for the human pass.
