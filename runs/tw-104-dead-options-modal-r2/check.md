# Check report: issue-dead-options-modal-scene (revision-check-2, iteration 3)

classification: fixable

## Verdict

The deletion itself remains fully verified with fresh evidence: dead `Options.tscn`
/ `Options.gd` / `Options.gd.uid` are gone from the worktree and the diff, a fresh
project-wide grep finds zero references to `Options.tscn` / `OptionsModal` /
`scripts/ui/Options.gd`, the editor/import gate is clean, `smoke_placement` passes
fresh with a new result.json, `smoke_tower_roster` now reaches wave 2 with 7 of its
10 tower types dealing damage (up from 4), and both live Options paths pass fresh
(pause-menu scenario exit 0 with all expectations green; main-menu script exit 0,
OptionsScreen instantiated visible=true).

However, the plan's **full test command** (`smoke_tower_roster`) still fails with
exit 1. The revision added five `_set_egg(20)` timeline calls but they still cannot
prevent the wave-2 gameover: snapshots prove the egg dies DURING wave 1 — the
first wait (`current_wave >= 2`, optional) times out at wave 1 with gameover already
set, so the wave-1 top-up placed immediately after `trigger_wave` fires long before
the leak lands. Once Game.gd flips to `gameover` the SimulationClock stops,
`current_wave` freezes at 2 (< 3), and balista/bazooka/cannon never engage (damage 0).
The failure signature is otherwise identical to the pre-existing baseline failure
(reproduced on pristine HEAD in iteration 1); it is a map_10 gameplay-pacing issue
under this synthetic 12-tower placement, not a resource/class-cache regression from
the deletion.

Per the build/test gate, no criterion may remain Done while the full suite is red,
so all criteria stay Pending. This remains fixable.

## Verification commands (all fresh this iteration via run_project_cmd,
project=poke-defense-godot, workspace=poke-defense-godot/issue-dead-options-modal-scene)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 (runner healthy) |
| `godot --headless --path . --editor --quit-after 300` | 0 | Clean; no parse or missing-resource errors |
| `--harness=res://tests/scenarios/smoke_tower_roster.json` | 1 | `status: fail`; snapshots: start egg=20 → after_waves wave=2 egg=0 gameover; first wait times out AT WAVE 1 (gameover during wave 1); failing expectations: `current_wave >= 3` (actual 2), balista/bazooka/cannon damage > 0 |
| `--harness=res://tests/scenarios/smoke_placement.json` | 0 | `[Harness] status=pass exit=0`; fresh `.gen/harness/smoke_placement/result.json` |
| `--harness=res://tests/scenarios/issue_dead_options_live_pause_menu.json` | 0 | `[Harness] status=pass exit=0`; expectations green: game_state==paused, ui_call size 1 >= 1 (live OptionsScreen instance) |
| `--script res://tests/ui/issue_dead_options_main_menu.gd` | 0 | `[issue-check] options control found: OptionsButton`; pressed; `[issue-check] live OptionsScreen instantiated: Control visible=true` |

Reference search (checker-run grep over *.gd/*.tscn/*.godot/*.md, excluding .gen/.git):
zero hits for `Options.tscn`, `OptionsModal`, `scripts/ui/Options.gd`. File checks:
all three dead files absent from disk.

## Why the second revision also did not fix the red suite

- The egg dies during **wave 1**, not between waves: the first
  `wait_for_condition(current_wave >= 2)` (optional) returned NOT OK with actual=1,
  and the `after_waves` snapshot shows `wave=2 egg_hp=0 game_state=gameover`. The
  top-up placed right after the first `trigger_wave` executes before wave-1 damage
  accumulates, so it cannot protect the egg through the whole wave.
- `_set_egg(20)` clamps `egg_hp` and emits `egg_changed` only; once
  `game_state = "gameover"` is set (Game.gd ~line 526) the simulation clock stops
  and no later call resumes waves.
- Fix direction for the next revision: repeated small egg top-ups INSIDE each wave
  (e.g. interleave short `wait_for_duration` steps with `_set_egg(20)` calls until
  `current_wave >= 3`), or rebaseline/rebalance the scenario expectations and file
  the map_10 pacing issue separately. The criterion as written requires
  `smoke_tower_roster` to pass.

## Per-criterion status

1. No references to dead Options surfaces — verified fresh (grep above).
2. Dead files removed incl. `.uid` sidecar — verified fresh (git status + ls).
3. Editor/import gate exit 0, no errors — verified fresh.
4. smoke_placement pass + fresh result.json — verified fresh.
5. smoke_tower_roster pass — FAILED (exit 1). Partial progress vs iteration 2:
   generic/fire/ice/water/electric/venom/scifi damage > 0 now (was 4 of 10);
   current_wave reached 2 (was frozen lower). Remains Pending.
6. Pause menu opens live Options modal — verified fresh via new scenario (pass).
7. Main menu loads OptionsScreen.tscn — verified fresh via SceneTree script (exit 0).

## Test overlap check

No new tests were added in this revision (only timeline entries inside the existing
scenario). No overlap violations.

## Changed-file quality

Feature diff is deletion-only plus scenario timeline edits; new GDScript test script
follows project style. No quality violations in changed code. Advisory (already in
quality-notes.md): ~109 model `.glb` files show binary diffs against HEAD outside
cluster scope — LFS-pointer smudging from workspace bootstrap, not coder edits;
also the two open quality-notes entries about the smoke_tower_roster pre-existing
failure remain open (no RESOLVED marker yet) since the scenario is still red.

## Blockers

None infra-related. Single blocker: `smoke_tower_roster` still red because the egg
cannot survive wave 1 under the current single-shot top-up pattern. Next revision:
interleave periodic egg top-ups with short waits throughout waves 1–2 so gameover
never occurs, then rerun the full command.
