# Request: Upgrade tower button disabled without money (issue #135)

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/135
- **Slug:** upgrade-button-disabled-without-money
- **Project:** poke-defense-godot
- **Workspace:** poke-defense-godot/issue-upgrade-button-disabled-without-money
- **Branch:** issue/upgrade-button-disabled-without-money

## Problem
The upgrade tower button stays clickable when the player has no money. It should be disabled whenever the player cannot afford the upgrade, and instantly re-enabled the moment the upgrade panel is open and the player has enough money.

## Acceptance criteria (from the issue)
1. Upgrade tower button is disabled while the player has less money than the upgrade cost.
2. When the upgrade panel is opened and the player has enough money, the button is enabled immediately (no need to reopen or click elsewhere).
3. Button state updates live as money changes while the panel is open (spend below cost → disables again).
4. Harness verification covers both states: no-money → disabled, affordable → enabled instantly on panel open.

## Notes
- This is visible UI work: per Daniel's standing expectation, visible chest/UI/gameplay behavior requires team-work manual-tester with windowed screenshots (never headless-only). Manual testing must be `required`.
- Use native Linux Godot via runner commands (`run_project_cmd`), explicit scene argument before harness user args.
- Checker classification line required: `classification: pass|fixable|design_failure|blocked`.
