# Request: #127 follow-up (reopen)

Rebase is already done on this worktree (`f744f23` on `origin/master` `bc1fdf4`).
Do not redo the rebase. Implement the three remaining visual follow-ups.

https://github.com/daniell0gda/poke-defense-godot/issues/127

## Required (Daniel)

1. **Panel title must not be trimmed.**
   Both `RewardsModal` and `ProgressionModal` use `TitledPanel` + `TitlePlate`.
   The plate sits at `offset_top = -23` so it hangs out of the Window and gets clipped
   (titles like "Choose a Reward" look cut off). Keep the plate fully inside the
   window: inset the Frame from the top (or add a top spacer) so the straddling plate
   is fully visible, and size the plate from the title's combined minimum size
   (`TitledPanel._place_plate`) with enough width that no ellipsis/clip happens.
   See team-work `references/titledpanel-anchor-plate-pitfall.md`.
   TitlePlate stays center-anchored (`anchors_preset = 5`).
   Vision-check screenshots: full readable title, no cut letters.

2. **Add a panel around each reward perk description.**
   In `RewardsModal._create_card` the description is a bare RichTextLabel.
   Wrap the description in its own `PanelContainer` with `theme_type_variation = "ModalWell"`
   (same inset well as tower-details Stats). Title/sub stay above that well.
   ProgressionModal reward-card descriptions should also sit inside a ModalWell
   (the card itself is already ModalWell — nest a description well or keep card
   ModalWell and put description in a second inner well so the body text has a
   distinct panel).

3. **Professional look for Unique perks.**
   When a card's type is Unique (ProgressionModal offer cards and RewardsModal
   listed selections):
   - gold/bronze metallic border (duplicated StyleBoxFlat, width ≥ 3) — not the
     old invalid `add_theme_color_override("panel")`
   - Unique rarity chip/badge using existing `CostBadge` (or TitlePlate-style
     small plate) with text "Unique"
   - slightly stronger header contrast (ModalTitle on the name)
   - no new art assets
   Force at least one Unique card in the windowed screenshot scenario so this
   is actually visible (do not leave Unique styling unphotographed).

## Preserve
- Wood ModalPanel + TitlePlate + corner ✕ / close_requested
- CaveSystem Window-typed return; `node is ProgressionModal` auto-answer
- ModalWell card bodies for common rewards
- Runner: project `godot-td`, workspace `poke-defense-godot/issue-window-modals-skip-wood-frame`
- All Godot via run_project_cmd. manual_testing required, windowed screenshots,
  never --headless for manual-tester. ui_feels_broken: no.
- Do not commit, push, merge, or close.

## Project path
/workspace/git-workspaces/poke-defense-godot/issue-window-modals-skip-wood-frame
