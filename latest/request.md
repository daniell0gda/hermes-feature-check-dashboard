# Request: Progression perk `molten_shackles` (GitHub issue #93)

Repo: poke-defense-godot
Workspace: /workspace/git-workspaces/godot-td/issue-molten-shackles
Branch: issue/perk-molten-shackles (based on origin/master 726ee0c)
Issue slug: perk-molten-shackles

## Problem
Fire Tower's burn DoT and the armor mechanic are entirely separate systems today - burning an armored enemy does nothing to its shield.

## Why it matters
Fusion perk: it should do nothing on its own and only matter if the player has also taken a Fire-tower burn-duration/burn-damage perk (see `ProgressionManager.get_fire_burn_config()`). Makes a Fire-stacking build into passive armor-shred without touching Ballista or Sundering Bolts (#88).

## Done when
- New perk `molten_shackles`, type Common, 3 levels. Each burn tick additionally strips a flat armor amount, scaling with the player's current Fire burn perk level (not this perk's own level alone) - L1/L2/L3 sets the base amount (e.g. 1/2/3 armor per tick), scaled by the burn config's level multiplier.
- If no Fire burn perk is active, this perk has zero effect (burn does not exist to carry it) - expected, not a bug, covered by a `game-test` scenario asserting `armor` is unchanged by burn ticks when no burn perk is present.
- No new VFX required - reuses existing burn tick/`BurnVFX` visuals; armor drain visible via the armor bar.

Focus: headless harness verification; no manual-testing screenshots required beyond standard gates.
