# Request: Warlord's Doctrine progression perk

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/87
- **Project:** poke-defense-godot
- **Git workspace:** /workspace/git-workspaces/poke-defense-godot/issue-warlords-doctrine
- **Branch:** issue/warlords-doctrine (cut from origin/master @ 97ed515)
- **Request ID:** warlords-doctrine-r1

## Feature (plain English)
New Common global progression perk `warlords_doctrine`: towers deal more damage, but every enemy spawns with bonus armor as a share of its max HP. Give/take perk.

## Acceptance criteria (from issue #87, exact)
1. New perk `warlords_doctrine`, type Common, global (`scripts/progression/global.json`), 3 levels:
   - L1: +5% tower damage, enemies spawn with bonus armor = 8% of max HP
   - L2: +9% tower damage, bonus armor = 12% of max HP
   - L3: +14% tower damage, bonus armor = 15% of max HP
2. Bonus armor is additive on top of innate `armor` from `enemies.xml`, applied at the same spawn site as `Enemy.gd:107-109` — not a replacement.
3. Damage bonus goes through the existing tower-damage modifier chain (`scripts/progression/handlers/global/TowerDamage.gd`), stacking correctly alongside the existing `tower_dmg` perk rather than overriding it.
4. Existing armor bar (`EnemyHealthBar.gd` ArmorRow/ArmorBar, `_update_armor_bar()` ~line 330) correctly shows/animates newly granted armor on previously-unarmored enemies.
5. A `game-test` scenario spawns a normally-unarmored enemy with the perk active and asserts `enemy.armor > 0` at spawn.

## Runner notes (redo pins)
- Runner key: `godot-td`; workspace: `poke-defense-godot/issue-warlords-doctrine`.
- Use native Godot via run_project_cmd; harness scene args before user args.
- Manual testing: visible player-facing perk — set `manual_testing: required` with windowed screenshots (no --headless for manual tester). UI-sanity criterion required.

## Historical context
Fresh claim; no prior worktree or diff.
