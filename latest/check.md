# Check report: issue-dead-options-modal-scene (revision-check-1, iteration 2)

classification: fixable

## Verdict

The deletion itself remains fully verified: dead `Options.tscn` / `Options.gd` /
`Options.gd.uid` are gone, a fresh project-wide grep finds zero references to
`Options.tscn` / `OptionsModal` / `scripts/ui/Options.gd`, the editor/import gate is
clean, `smoke_placement` passes fresh, and both live Options paths pass fresh
(pause-menu scenario: exit 0, all expectations green; main-menu script: exit 0,
OptionsScreen instantiated visible=true).

However, the plan's **full test command** (`smoke_tower_roster`) still fails with exit 1.
The revision added two `_set_egg(20)` calls to the scenario timeline, but they do not
cure it: the egg still reaches 0 hp during wave 2 and the game enters `gameover`
BEFORE the between-waves revive fires. Snapshots from the fresh run prove it:
`start egg_hp=20 paused` → `after_waves wave=2 egg_hp=0 gameover` → `after_upgrade
egg_hp=20 gameover`. Once `_set_game_state("gameover")` fires (Game.gd:526), waves stop
advancing (`current_wave` stays 2 < 3) and balista/bazooka/cannon never engage (damage 0).
Topping the egg up after gameover does not resume the simulation clock, so the revive
call is placed too late to matter. The failure signature is otherwise identical to the
pre-existing baseline failure (reproduced on pristine HEAD in iteration 1).

Per the build/test gate, no criterion may remain Done while the full suite is red, so
all criteria remain Pending. This is fixable: either place periodic/earlier egg top-ups
(e.g. `_set_egg(20)` immediately after each trigger_wave, or before the first snapshot
plus after every wait_for_condition) so gameover never occurs, or restore the scenario's
original expectations and file the map_10 pacing issue separately — but the criterion as
written requires `smoke_tower_roster` to pass.

## Verification commands (fresh this iteration, all via run_project_cmd,
project=poke-defense-godot, workspace=poke-defense-godot/issue-dead-options-modal-scene)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 (runner healthy) |
| `godot --headless --path . --editor --quit-after 300` | 0 | Clean; no parse or missing-resource errors |
| `--harness=res://tests/scenarios/smoke_tower_roster.json` | 1 | `status: fail`; snapshots show egg dead + gameover at wave 2 before mid-scenario revive; failing expectations: `current_wave >= 3`, balista/bazooka/cannon damage > 0 |
| `--harness=res://tests/scenarios/smoke_placement.json` | 0 | `[Harness] status=pass exit=0`; fresh `.gen/harness/smoke_placement/result.json` |
| `--harness=res://tests/scenarios/issue_dead_options_live_pause_menu.json` | 0 | `status=pass`; game_state==paused and 1 live OptionsScreen instance |
| `--script res://tests/ui/issue_dead_options_main_menu.gd` | 0 | OptionsButton wired to `_on_options_button_pressed`, pressed; live OptionsScreen instantiated visible=true |

Reference search (checker-run, project-wide grep over *.gd/*.tscn/project.godot/docs,
excluding .gen/.git/.godot): zero hits for `Options.tscn`, `OptionsModal`,
`scripts/ui/Options.gd`.

## Why the revision did not fix the red suite

- `_set_egg(20)` only clamps `egg_hp` and emits `egg_changed` (scripts/core/GameState.gd:47);
  it does not clear `game_state`.
- Game.gd:521-526 sets `game_state = "gameover"` when egg hp reaches 0; SimulationClock then
  stops, so `current_wave` freezes at 2 and later waves never spawn.
- The scenario's revive call sits AFTER `wait_for_condition(current_wave >= 3)` and the
  `after_waves` snapshot — i.e. strictly after gameover has already occurred. Too late by
  construction.

## Per-criterion status

1. No references to dead Options surfaces — verified fresh (grep above).
2. Dead files removed incl. `.uid` sidecar — verified (git status: 3 deletions).
3. Editor/import gate exit 0, no errors — verified fresh.
4. smoke_placement pass + fresh result.json — verified fresh.
5. smoke_tower_roster pass — FAILED (exit 1); revision's egg top-ups ineffective (see
   analysis above). Remains Pending.
6. Pause menu opens live Options modal — verified fresh via new scenario (pass).
7. Main menu loads OptionsScreen.tscn — verified fresh via new SceneTree script (exit 0).

## Test overlap check

New scenario `issue_dead_options_live_pause_menu.json` asserts OptionsScreen instantiation
via the public pause path; existing `hud_other_panels.json` only asserts the paused state,
so no duplication. New main-menu script has no existing equivalent. No overlap violations.

## Changed-file quality

Deletion-only feature diff plus focused test artifacts; new GDScript follows project style.
No quality violations in changed code. One environment observation recorded in
quality-notes.md: ~109 model `.glb` files show binary diffs against HEAD that are outside
the cluster scope (likely LFS-pointer smudging during workspace bootstrap, not coder edits);
advisory only.

## Blockers

None infra-related. Single blocker: `smoke_tower_roster` still red because the revision's
revive placement cannot prevent the wave-2 gameover. Next revision should top up the egg
immediately after each `trigger_wave` (or use repeated small top-ups around waits) so
`gameover` never occurs, then rerun the full command.
