# Check report: 131-pointer-cursor-r2 (revision-check-1, pointer-cursor-on-clickable-surfaces)

classification: pass

## Verdict

All 6 acceptance criteria verified Done against fresh runner evidence gathered
this iteration. Editor/build gate, focused headless harness, focused windowed
harness (with screenshot), and regression harness all exit 0 with
`status=pass`. No quality violations in changed code; no test-overlap issues;
no blockers.

## Verification commands (all via run_project_cmd,
project=godot-td, workspace=poke-defense-godot/issue-pointer-cursor-on-clickable-surfaces)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Probe | `git status --short` | 0 | runner reachable |
| Typecheck/build | `godot --headless --path . --editor --quit-after 300` | 0 | clean scan/import, no parse errors |
| Focused headless | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ui_pointer_cursor.json` | 0 | `[Harness] status=pass exit=0`; result.json: 8/8 expectations pass |
| Focused windowed | `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ui_pointer_cursor.json` | 0 | `status=pass`; screenshot re-captured this run (1920x1080) |
| Regression | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json` | 0 | `[Harness] status=pass exit=0` |

No host-shell Godot was used; every gate ran through the approved runner.
Fresh result at `.gen/harness/ui_pointer_cursor/result.json`: status=pass with
4x clickable cursor_shape==2 (SpeedBtn, AutoNext, Tower1, PlayBtn), 2x
non-clickable cursor_shape==0 (HealthBar, UpgPanel Frame), exists and gamestate
checks pass.

## Criterion evidence

1. Buttons show pointer in windowed + fullscreen — Done. Fresh headless +
   windowed runs assert SpeedBtn/PlayBtn/AutoNext/Tower1
   `mouse_default_cursor_shape == CURSOR_POINTING_HAND (2)` via the
   `scripts/ui/PointerCursor.gd` autoload (`node_added` hook + deferred
   whole-tree sweep). The shape is a Control property independent of display
   mode; windowed freshly verified.
2. Other clickable surfaces — Done. AutoNext (CheckButton) and Tower1
   (TowerShopSlot custom BaseButton widget) assert == 2 in the same run.
3. Non-clickables keep arrow — Done. HealthBar and UpgPanel Frame assert == 0;
   the autoload touches only BaseButton instances.
4. Windowed screenshot under `.gen/harness/ui_pointer_cursor/shots/` — Done.
   `hover_pointer_on_speed_btn.png` (1920x1080, ~1.88 MB) captured by this
   run's windowed harness after `hover_ui` warped over SpeedBtn. Per plan note,
   Godot does not render the OS cursor in screenshots; evidence pair is the
   programmatic assertion + hover screenshot.
5. Focused scenario passes headless + windowed — Done. Both fresh runs:
   status=pass, exit 0, all 8 expectations pass.
6. smoke_placement still passes — Done. Fresh regression run status=pass,
   exit 0 (`[Harness] status=pass exit=0`).

## Changed-file quality findings

Reviewed against /opt/data/coding_rules.md and worktree CLAUDE.md:
`project.godot` (autoload appended last, order otherwise untouched),
`scripts/ui/PointerCursor.gd` (new, ~25 lines, documented two-mechanism
rationale, typed params, enum identifiers `Control.CURSOR_POINTING_HAND`),
`scripts/testing/HarnessActions.gd` (`hover_ui` action — small focused
function, typed locals, guard clauses),
`scripts/testing/HarnessValues.gd` (`ui_control` source; master's `nature`
source preserved after rebase as required),
`tests/scenarios/ui_pointer_cursor.json`. No clear violations. The
`(node as BaseButton)` casts follow `is BaseButton` guards — safe and
idiomatic GDScript; already recorded advisorially in quality-notes.md
(iteration 2), still satisfied, no new entry needed.

New tests do not overlap existing coverage: no existing scenario or test
asserted cursor shapes; `ui_control`/`hover_ui` harness support and
`ui_pointer_cursor.json` are new capability, verified against the full suite
layout.

Dirty `logs/balance/*.csv` is pre-existing runtime output from harness runs,
not part of this feature diff.

## Blockers

None.

## Unverified items / manual testing

- manual_testing: required per plan — human windowed/fullscreen hover pass
  across menus/HUD/modals plus ui_feels_broken sanity check remains for the
  manual-tester profile (owns `.gen/manual-report.md`). This is a plan-defined
  human step, not an unmet acceptance criterion; automated coverage proves the
  programmatic cursor assignment and the windowed evidence pair.
- Exit-time RID/ObjectDB leak warnings are pre-existing engine teardown noise
  present in baseline scenarios too, not introduced by this change.
