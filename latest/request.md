# Request: health-bar-never-auto-hides

- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/112
- Project key (runner): `godot-td`
- Git workspace: `poke-defense-godot/issue-health-bar-never-auto-hides`
- Branch: `issue/health-bar-never-auto-hides` (cut from origin/master @ d241462)
- Claimed: 2026-08-23, by autostart cron pickup.

## Summary

`EnemyHealthBar.setup()` ends with `show_health_bar()` which sets `hide_timer = 0.0`, but the
auto-hide branch in `_process` requires `hide_timer > 0`, so a freshly spawned undamaged enemy's
full-health bar never fades. The fade-out plumbing (`FADE_OUT_DELAY`, `FADE_OUT_SPEED`,
`hide_timer`, `is_showing = false`) is unreachable on spawn.

## Decision requested (record it in code)

Prefer keeping the fade: `setup()` should arm `hide_timer` so a spawned bar fades after
`FADE_OUT_DELAY` and reappears on first damage. If instead fade-out is removed, delete the dead
plumbing entirely — do not leave half-states.

## Done when (from issue)

1. A decision recorded in `scripts/ui/EnemyHealthBar.gd`: either remove fade plumbing, or arm
   `hide_timer` in `setup()` so a spawned bar fades after `FADE_OUT_DELAY`.
2. If fade kept: verified on a windowed run that a spawned enemy's bar fades out and reappears on
   first hit; assert the spawn case in `tests/ui/test_enemy_armor_bar.gd`.
3. Re-examine `scripts/game/actors/Enemy.gd:339-344` `is_menu_backdrop` skip — if bars self-hide,
   the menu special case may be removable (remove only if verified safe).

## Redo notes for resumed runs

- Runner names: project `godot-td`, workspace `poke-defense-godot/issue-health-bar-never-auto-hides`. Do NOT invent other workspace names.
- Godot commands need explicit scene arg before user args; use `--rendering-method gl_compatibility --audio-driver Dummy` for windowed runs.
