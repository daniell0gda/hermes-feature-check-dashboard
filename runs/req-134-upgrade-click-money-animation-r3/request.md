# Request: upgrade-click-money-animation

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/134
- **Project:** poke-defense-godot
- **Git workspace:** /workspace/git-workspaces/poke-defense-godot/issue-upgrade-click-money-animation (branch issue/upgrade-click-money-animation)
- **Request ID:** req-134-upgrade-click-money-animation-r3

## Goal

Clicking "Upgrade" in the tower details panel must play the same floating "+N coins!" popup chest rewards use, and it must be VISIBLE on screen.

## Current state (from r1/r2 runs)

- Implementation exists: `UI.gd::_spawn_upgrade_money_popup` calls `ChestRewardSystem.create_reward_popup(...)` with parent `Surface`/`Underground` based on layer. Harness asserts on popup meta pass.
- BLOCKER found by visual verification: the Tower Details panel opens centered over the selected tower (panel ≈x770–1150, y155–685 at 1920x1080; tower at ≈(830,680)). The popup floats up from the tower position BEHIND the opaque panel, so no frame shows it. Pixel scans of 10 post-click frames found zero new yellow text pixels.

## Required fix

Make the upgrade popup render in a visible screen area:
- Unproject the tower position to screen coordinates, test overlap against the tower details panel rect, and if occluded offset the popup's world anchor (via camera projection) until its screen position lies outside the panel (e.g. left or below the panel).
- Keep style/timing identical to the chest popup (same create_reward_popup, yellow Label3D, 1.5s float/fade).
- Keep existing harness assertions working.

## Acceptance criteria

1. Clicking "Upgrade" triggers the same money-increase animation.
2. Animation is provably visible: fresh windowed run captures frames immediately after click where the yellow "+20 coins!" text is present OUTSIDE the panel region — verified by pixel scan (new yellow cluster) AND visual inspection, not by harness status alone.
3. Tower levels up and money is charged as before.

## Notes

- manual_testing: required (windowed screenshots mandatory).
- Previous attempts archived under `.gen-blocked-req134-attempt1/`; r2 evidence under `.gen/harness/`.
- Do not close or push unless Daniel asks.
