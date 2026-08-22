# Request: upgrade-click-money-animation

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/134
- **Project:** poke-defense-godot
- **Git workspace:** /workspace/git-workspaces/poke-defense-godot/issue-upgrade-click-money-animation (branch issue/upgrade-click-money-animation, base origin/master @726ee0c)
- **Request ID:** req-134-upgrade-click-money-animation-r2

## Goal

Play the existing floating money-increase animation when the player clicks "Upgrade" in the tower details panel. Upgrading currently spends money with no visual feedback.

## Acceptance criteria (from issue)

1. Clicking "Upgrade" in tower details triggers the same animation used for money increase.
2. Animation visually matches the existing money-increase effect (position/style consistent).
3. Verified in-game via windowed screenshot.

## Notes

- Visible player-facing effect → manual_testing: required.
- Previous attempt (req-134-upgrade-click-money-animation) was blocked: planner and coder both hit their iteration limits without writing artifacts; its partial unverified changes were discarded and archived under .gen-blocked-req134-attempt1/. Start fresh.
- Do not close or push unless Daniel asks.
