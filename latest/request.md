# Request: Warlord's Doctrine — readable windowed armor-bar evidence

- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/87
- **Project:** poke-defense-godot
- **Runner key:** `godot-td`
- **Git workspace:** `/workspace/git-workspaces/poke-defense-godot/issue-warlords-doctrine`
- **Workspace id:** `poke-defense-godot/issue-warlords-doctrine`
- **Branch:** `issue/warlords-doctrine` (uncommitted perk implementation on current `origin/master`)
- **Request ID:** `warlords-doctrine-r2`

## Feature (plain English)

Towers deal more damage, but every enemy spawns with extra armor. The perk itself is already implemented. This run only has to make the granted armor bar *visibly readable* in windowed shots.

## Historical (do not re-implement unless broken)

`warlords-doctrine-r1` already implemented and auto-verified:

- Perk `warlords_doctrine` Common global, 3 levels: L1 +5% dmg / 8% HP armor, L2 +9% / 12%, L3 +14% / 15%.
- Bonus armor additive on innate `enemies.xml` armor at `Enemy.setup()` after max_hp is final.
- Damage stacks additively with `tower_dmg` via `_warlords_damage_ratio` in `get_global_damage_multiplier()`.
- Headless `tests/scenarios/warlords_doctrine.json` pass (L1/L2/L3 multipliers, Mushnub granted 1.76, Orc boss 303.75, reset to 0).
- `enemy_armor_ballista` / `enemy_armor_trap` still pass.
- Files already changed (keep them): `autoload/ProgressionManager.gd`, `scripts/game/actors/Enemy.gd`, `scripts/progression/global.json`, `tests/scenarios/warlords_doctrine.json`, `tests/scenarios/enemy_armor_bar_visual.json`.

r1 manual-tester wrote `.gen/manual-report.md` PASSED, but the pixels do **not** prove the armor bar:

- Debug Panel covers the left third of the frame.
- Camera is a distant top-down of map_3; Mushnub is a tiny purple placeholder (GLB missing in this worktree).
- Named “close-ups” (`doctrine_armor_*.png`) are just wide crops of that same distant shot. Two bars are not readable. `ui_feels_broken: yes` for evidence purposes.

Archive of r1 dashboard: https://daniell0gda.github.io/hermes-feature-check-dashboard/runs/warlords-doctrine-r1/

## Remaining acceptance (this run)

1. Windowed (no `--headless`) `enemy_armor_bar_visual` (or a dedicated follow-up scenario) produces PNGs where a human can clearly see:
   - a previously-unarmored enemy (Mushnub / map_3 wave 1) with Warlord's Doctrine L1 active
   - **two** bars: HP row + granted armor row, filled at spawn
   - armor fill shrinks after a scripted armor hit
   - armor row hidden once armor is 0
2. Hide the Debug Panel before screenshots (`UI.debug_panel.visible = false` or equivalent harness call). Do not leave the gray debug overlay in evidence shots.
3. Camera must aim at the **live enemy position** (`camera_target` = enemy world pos, then `_update_camera_for_layer("surface")`). Hardcoded `[-8,0,-8]` is wrong if the spawn is elsewhere. Zoom close enough that the bars are more than a couple of pixels (raise camera / shorten `surface_distance` only for the shot if a public setter exists; do not permanently change default camera for the game).
4. If Mushnub GLB is missing, still make the bars readable (zoom, bigger bar scale for the shot, or a larger unarmored enemy type that still has config armor 0). Do not fake armor with a naturally armored enemy for the doctrine leg.
5. `ui_feels_broken: no` on each final screenshot. Misplaced/clipped HUD or a covering debug panel fails.
6. Fresh windowed PNGs copied to `.gen/screenshots/` (overwrite the unreadable r1 crops).
7. Re-run focused `warlords_doctrine.json` headless so perk behavior is still green after any scenario/camera change.
8. Do **not** reformat unrelated `global.json` entries (existing advisory: whitespace noise).

## Runner notes

- `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-warlords-doctrine`.
- Native Godot; harness scene before user args: `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/<name>.json`
- Windowed evidence: no `--headless`; add `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy` if Vulkan fails.
- `manual_testing: required`. Windowed PNGs required. A pass with unreadable bars is a fail.

## Lifecycle

Do not commit, push, merge, or close the issue.
