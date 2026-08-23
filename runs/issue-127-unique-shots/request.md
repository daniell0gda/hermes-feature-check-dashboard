# Request: #127 visual closeout (continuation)

Implementation already exists on this worktree (uncommitted). Do not rebase.
Do not rewrite the Unique styling API unless a test fails.

https://github.com/daniell0gda/poke-defense-godot/issues/127

## Why the last run failed
Checker classified `fixable` because:
- no `.gen/manual-report.md` / windowed screenshots
- the windowed ProgressionModal scenario did not force a Unique option, so Unique gold border + Unique badge were unphotographed

## Required this run
1. Update `tests/scenarios/progression_modal_wood_frame.json` (and/or a sibling scenario) so at least one offered card is Unique. Seed/harness must force Unique, not hope the RNG draws it.
2. Run windowed (never --headless for visual):
   - `hud_other_panels` → panel_rewards
   - progression modal with Unique visible
   - RewardsModal listing at least one Unique selection if possible
3. Copy fresh PNGs to `.gen/screenshots/` AND `.gen/harness/.../shots/`.
4. Manual-tester required, windowed only. Judge `ui_feels_broken: no`.
5. Vision-check: full unclipped titles ("Rewards", "Choose a Reward"); description text inside a distinct ModalWell; Unique card has gold/bronze ≥3px border + readable "Unique" badge.

## Preserve
- Existing TitledPanel inset / plate-width fix
- Shared `scripts/ui/modal_unique_styling.gd`
- Runner: `godot-td` / `poke-defense-godot/issue-window-modals-skip-wood-frame`
- No commit/push/close

## Project path
/workspace/git-workspaces/poke-defense-godot/issue-window-modals-skip-wood-frame
