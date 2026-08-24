# Coder report: dead-options-optionsmenu-cleanup

Both dead Options clones are deleted. Live `OptionsScreen` is unchanged.

## Files
Deleted (stay deleted):
- `scenes/ui/Options.tscn`
- `scripts/ui/Options.gd`
- `scripts/ui/Options.gd.uid`
- `scenes/ui/OptionsMenu.tscn`
- `scripts/ui/OptionsMenu.gd`
- `scripts/ui/OptionsMenu.gd.uid`

Kept:
- `scenes/ui/OptionsScreen.tscn`
- `scripts/ui/OptionsScreen.gd`

Added earlier (r1, keep):
- `tests/scenarios/issue_dead_options_live_pause_menu.json`
- `tests/ui/issue_dead_options_main_menu.gd`

## Non-goals honored
- `tests/scenarios/smoke_tower_roster.json` must match origin/master (no egg-top-up timeline).
- No `models/**`, harness, or `project.godot` edits.

## Note
A previous r2 code worker went off-scope and pretty-printed / retimed `smoke_tower_roster.json`. That file was reverted. Stale r1 `check.md` still says `classification: fixable` because it gated on roster — ignore it.
