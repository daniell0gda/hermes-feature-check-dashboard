# Check report: issue-dead-options-modal-scene (iteration 3, fresh r2 verification)

classification: pass

## Verdict

All seven acceptance criteria are Done with fresh evidence from this iteration.
Both dead pairs (`Options.tscn`/`Options.gd` + `.uid`, `OptionsMenu.tscn`/
`OptionsMenu.gd` + `.uid`) are deleted; the diff contains only the six deletion
paths plus the two untracked live-path tests. The r2 plan's full test command is
`smoke_placement.json` (not `smoke_tower_roster`, which the request explicitly
demoted to advisory — it fails identically on pristine master per quality-notes
and was not run or edited this round, per STOP instructions). All required gates
pass.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-dead-options-modal-scene)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 (runner healthy) |
| `godot --headless --path . --editor --quit-after 300` | 0 | Import gate clean: no parse errors, no missing-resource errors. Only pre-existing invalid-UID warnings in `themes/hud/HudTheme.tres` / `scenes/UI.tscn` (text-path fallback, pre-dates this issue). |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_dead_options_live_pause_menu.json` | 0 | `[Harness] status=pass exit=0`; expectations green: `game_state == paused`, `ui_call size >= 1` → actual 1 (live OptionsScreen instance). Fresh result at `.gen/harness/issue_dead_options_live_pause_menu/result.json`. |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json` | 0 | `[Harness] status=pass exit=0`; fresh `.gen/harness/smoke_placement/result.json` with `status: pass`. |
| `godot --headless --path . --script res://tests/ui/issue_dead_options_main_menu.gd` | 0 | `[issue-check] options control found: OptionsButton`; pressed; `[issue-check] live OptionsScreen instantiated: Control visible=true`. |

Exit-time RID/ObjectDB leak noise appears in every headless run including
baseline — engine shutdown noise, not scenario failures. GLB
"resources have been imported" errors are LFS-model environment noise (no git-lfs
on host) and do not implicate any criterion.

## Criterion evidence

1. Repo-wide reference search (checker grep over *.gd/*.tscn/*.godot/*.json,
   excluding `.gen`/`.git`/`.gen-blocked-*`): single benign hit — a prose
   description string "Options.tscn / Options.gd removed" in the new test
   scenario's own description field
   (`tests/scenarios/issue_dead_options_live_pause_menu.json:7`). No load path,
   class reference, preload, or scene ext_resource remains for any dead file or
   `OptionsModal`/`OptionsMenu`.
2. File existence check: all six dead-file paths absent from disk;
   `git diff HEAD --stat` shows exactly the six deletions (642 lines removed),
   nothing re-added.
3. Editor/import gate exit 0 (see table).
4. Pause-menu scenario exit 0, all expectations pass (see table).
5. Main-menu script exit 0, live OptionsScreen visible=true (see table).
6. smoke_placement exit 0, `status: pass` in fresh result.json (see table).
7. Diff scope confirmed by `git diff HEAD --stat`: only the six deleted files.
   No changes to `models/**`, `project.godot`, harness, or
   `tests/scenarios/smoke_tower_roster.json`. Untracked additions are the two
   live-path test files required by criteria 4–5.

## Quality findings

- New test files (`tests/scenarios/issue_dead_options_live_pause_menu.json`,
  `tests/ui/issue_dead_options_main_menu.gd`) assert through public paths
  (pause-menu Options button press → OptionsScreen instance count ≥ 1; main-menu
  OptionsButton press → visible OptionsScreen instance). No overlap with
  existing suite coverage of these paths found; both would fail if a dead or
  missing Options resource were wired back in.
- Cross-cutting: none new. Pre-existing advisory entries in quality-notes.md
  (smoke_tower_roster map_10 pacing failure on pristine master) remain open and
  are out of scope per plan non-goals; no resolution appended because the
  violation itself is unchanged and explicitly non-gating here.

## Blockers

None.

## Unverified items

None. Manual testing marked optional in the plan; windowed sanity not performed
(headless evidence covers both live Options paths programmatically).
