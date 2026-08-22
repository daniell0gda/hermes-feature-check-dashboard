# Request: window-modals-skip-wood-frame (#127)

Give RewardsModal and ProgressionModal the same wood frame and corner close as the other HUD modals.

## Issue
https://github.com/daniell0gda/poke-defense-godot/issues/127

## Problem
RewardsModal and ProgressionModal are raw Window nodes with no theme. They look like a grey Godot default window. Every other modal uses ModalPanel + TitlePlate from themes/hud/HudTheme.tres and TitledPanel.gd, and dismisses via CloseChip / close_requested.

## Done when
- Both modals use the same wood frame and name plate as PauseMenu, Options, Manage Towers, and tower details.
- They dismiss through the same corner ✕ / close_requested contract, not a window decoration.
- UI.open_progression_modal and UI._on_rewards_pressed still return something callers can use. CaveSystem types the return as Window. AgentHarness still matches `node is ProgressionModal` for auto-answer.
- tests/scenarios/hud_other_panels.json panel_rewards checkpoint shows the wood frame.
- The reward-pick modal (ProgressionModal) gets a screenshot checkpoint of its own.

Visual: reuse existing ModalPanel / TitlePlate / ChipButton theme variations. No new art.

## Follow-up (Daniel, not satisfied)
Each reward card inside ProgressionModal must use the same background treatment as
the tower-details panel's stats area (`Root/UpgPanel/Frame/VBox/Stats`, theme type
variation `ModalWell` from themes/hud/HudTheme.tres) — not the current plain grey
default PanelContainer style. The Unique rarity accent must not rely on the invalid
`add_theme_color_override("panel", ...)` call (PanelContainer has no such color
item); tint via a duplicated StyleBoxFlat border instead if kept.
Verify visually with a windowed screenshot of the opened modal showing ModalWell-
styled cards.

## Constraints
- Project runner key: godot-td
- Workspace: poke-defense-godot/issue-window-modals-skip-wood-frame
- All Godot/project commands via run_project_cmd. No host godot.
- Visible UI work: manual_testing required, windowed screenshots, never --headless for manual-tester.
- Do not commit, push, merge, or close the issue.
- Follow /opt/data/coding_rules.md and project CLAUDE.md.

## Project path
/workspace/git-workspaces/poke-defense-godot/issue-window-modals-skip-wood-frame
