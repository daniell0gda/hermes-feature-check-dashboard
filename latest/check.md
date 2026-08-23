# Check report: issue-dead-options-modal-scene (iteration 1)

classification: fixable

## Verdict

The deletion itself is correct and fully verified: dead `Options.tscn` / `Options.gd` /
`.uid` are gone, nothing references them, the editor/import gate is clean, both live
Options paths are proven intact by two new non-overlapping automated checks, and the
focused harness passes fresh. However, the plan's **full test command**
(`smoke_tower_roster`) fails with exit 1. Per the build/test gate, no criterion may
remain Done while the full suite is red, so all criteria are moved to Pending. The
failure was independently reproduced on pristine HEAD with identical failing
expectations — it is a pre-existing map_10 gameplay-pacing failure, not caused by this
change, but it still leaves the suite red.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-dead-options-modal-scene)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 (runner healthy) |
| `godot --headless --path . --editor --quit-after 300` | 0 | Clean; no parse or missing-resource errors |
| `--harness=res://tests/scenarios/smoke_placement.json` | 0 | `[Harness] status=pass exit=0`; fresh `.gen/harness/smoke_placement/result.json` |
| `--harness=res://tests/scenarios/smoke_tower_roster.json` | 1 | `status: fail`. Failing expectations: `current_wave 2 >= 3`, `damage_by_type.balista/bazooka/cannon 0 > 0` |
| `--harness=res://tests/scenarios/issue_dead_options_live_pause_menu.json` | 0 | `status=pass`; game paused + 1 live OptionsScreen instance found |
| `--script res://tests/ui/issue_dead_options_main_menu.gd` | 0 | OptionsButton wired, pressed; live OptionsScreen instantiated visible=true |

## Baseline control (checker-run)

Stashed the entire feature diff (`git stash push -u`), reran smoke_tower_roster on
pristine HEAD: exit 1, result.json `status: fail` with byte-identical failing
expectations (wave 2 vs 3; balista/bazooka/cannon damage 0). Stash popped; worktree
restored to feature state. Confirmed: pre-existing failure, unrelated to the deletion.

## Per-criterion status

1. No references to `Options.tscn` / `OptionsModal` / `scripts/ui/Options.gd` — verified
   (project-wide grep over scenes/scripts/autoload/project.godot/docs returns zero hits).
2. Dead files removed incl. `.uid` sidecar — verified via git status (3 deletions).
3. Editor/import gate exit 0, no errors — verified fresh.
4. smoke_placement pass + fresh result.json — verified fresh.
5. smoke_tower_roster pass — FAILED (exit 1); identical failure reproduced on pristine
   HEAD. Moved to Pending.
6. Pause menu opens live Options modal — verified fresh via new scenario (pass).
7. Main menu loads OptionsScreen.tscn — verified fresh via new SceneTree script (exit 0).

All criteria moved to Pending solely because of gate rule #5 (full-suite red).

## Test overlap check

New scenario `issue_dead_options_live_pause_menu` asserts OptionsScreen instantiation;
existing `hud_other_panels.json` calls `_on_pause_options` but only asserts
`game_state == paused` — no duplication. New main-menu script has no existing
equivalent. No overlap violations.

## Changed-file quality

Deletion-only diff plus two focused test artifacts. New GDScript follows project style
(typed vars, guard clauses, single responsibility). No quality violations in changed
code.

## Blockers

None infra-related. The single blocker is the pre-existing `smoke_tower_roster`
failure on map_10 (egg dies during wave 2 under seeded roster), which must be fixed
separately (or the plan's full-test criterion rebaselined) before this branch can be
green.
