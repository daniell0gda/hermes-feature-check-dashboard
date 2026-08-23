# Manual Test Report – req-136 padding-closable-panels-close-button

## Summary

- Result: PASSED
- Tested on: 2026-08-23 ~04:05 UTC, fresh windowed run via run_project_cmd (Godot 4.4.1, GL compatibility / llvmpipe, audio dummy)
- Scenario: .gen/ui_scenario.md (windowed run of tests/scenarios/hud_other_panels.json)
- Tester: Manual-tester profile

Overall: Ran the hud_other_panels scenario windowed through the project runner on map_1 with a
placed and selected tower. Inspected all four fresh screenshots (tower details, pause menu,
Manage Towers, Options). On every closable panel the content sits clear of the painted "x", the
"x" sits flush INSIDE the wooden frame's top-right corner (not floating outside it), and nothing
is clipped or overlapped. ui_feels_broken: no on all four shots.

## Scenario Walkthrough

### Step 1 – Tower details panel (tower placed and selected)

- Action: Loaded map_1, placed a generic tower at (-6, 0, 2), selected it, opened tower details.
- Expected: Panel content (name header, level badge, stats, buttons) entirely clear of the "x".
- Observed: "Tower Details" header is left of the x; name/Lv.1 badge/stat rows/UPGRADE/SELL/
  targeting controls are all below the header bar. No control touches or overlaps the x; the x is
  flush inset into the frame's top-right corner. Runner log shows
  `[TITLED_PANEL] UpgPanel reserves 90x103px of top-right padding for the close corner`.
- Status: PASS — ![tower details](screenshots/panel_tower_details.png)

### Step 2 – Pause menu overlay

- Action: Opened pause menu over the running game.
- Expected: Buttons clear of corner ornament; no visual regression.
- Observed: Paused title + Resume / Options / Quit to Menu / Quit Game buttons are centered,
  fully inside the ornate frame, clear of all corner accents. No clipping.
- Status: PASS — ![pause menu](screenshots/panel_pause_menu.png)

### Step 3 – Manage Towers overlay

- Action: Opened Manage Towers via the Manage button path (_on_update_towers_pressed).
- Expected: Group list and cost text not running under the "x".
- Observed: The x sits alone in the top-right corner inside the silver corner trim. The
  Generic Tower (1) entry, its stats/cost line ("Lvl 1 Dmg 3.0 Rate 1.10/s Cost 20.0"), the
  Raise-to-Highest button and the +1/Max buttons are all well below/left of the x. Log line:
  `[TITLED_PANEL] ManageTowersPanel reserves 90x103px ...`. No clipping or overlap.
- Status: PASS — ![manage towers](screenshots/panel_manage_towers.png)

### Step 4 – Options screen

- Action: Opened Options from the pause flow (_on_pause_options).
- Expected: Rows/toggles clear of the "x".
- Observed: The painted x glyph is visible in the recessed center of the top-right metal corner
  bracket, fully enclosed by the frame art. Graphics/Sound tabs, Fullscreen / Resolution /
  UI Scale rows and Apply / Restore Defaults / Close buttons never approach the corner. Log line:
  `[TITLED_PANEL] Panel reserves 90x103px ...`. No overlap.
- Status: PASS — ![options](screenshots/panel_options.png)

## Criteria

- Closable TitledPanel reserves horizontal padding so no content intersects the CloseChip rect at any size
  - Proven visually at live panel sizes for UpgPanel, ManageTowersPanel and Options:
    - ![tower details](screenshots/panel_tower_details.png)
    - ![manage towers](screenshots/panel_manage_towers.png)
    - ![options](screenshots/panel_options.png)
  - (Rect-level proof at multiple sizes is covered headless by tests/ui/test_titled_panel_close_corner.tscn — 33 ok / 0 failed per changes.md.)
- Padding applies only when closable; non-closable panels unchanged
  - Pause menu (non-closable) layout unchanged, buttons clear of corner ornaments:
    - ![pause menu](screenshots/panel_pause_menu.png)
- CloseChip sits flush INSIDE the frame's top-right corner
  - Visible on all three closable panels: x enclosed by full-size frame art in each shot above.
- UpgPanel (tower details) content fully outside CloseChip rect
  - ![tower details](screenshots/panel_tower_details.png)
- Manage Towers and Options: no content intersects the chip after layout
  - ![manage towers](screenshots/panel_manage_towers.png)
  - ![options](screenshots/panel_options.png)
- Debug [TITLED_PANEL] reservation log naming panel and inset
  - Observed in this run's stdout for UpgPanel, ManageTowersPanel and Panel (Options): "reserves 90x103px of top-right padding for the close corner".

## Issues and Observations

- Low (pre-existing, unrelated): invalid UID warnings for HudTheme textures and a handful of
  unimported GLB models (backdrop earth, portal arch, ruined house) in this worktree; cosmetic
  log noise only, does not affect the UI under test.
- Low: exit-time GLES3 leak warnings from Godot teardown; engine housekeeping, not gameplay.

## Recommendation

Ready. All four panels show clean separation between content and the close "x", the chip is
flush inside the frame corner, and the debug reservation logs fire as specified. No fixes needed.

ui_feels_broken per shot: tower_details no | pause_menu no | manage_towers no | options no
