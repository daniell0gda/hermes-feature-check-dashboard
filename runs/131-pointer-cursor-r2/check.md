# Check report: 131-pointer-cursor-r2 (pointer-cursor-on-clickable-surfaces)

classification: fixable

Note on classification semantics: all acceptance criteria are verified Done and
all gates pass; `fixable` is used because the plan's manual-testing requirement
(fullscreen hover pass + ui_feels_broken sanity check by a human) remains open
for the manual-tester profile. No code or test work is outstanding.

## Verdict

All 6 acceptance criteria verified Done against fresh runner evidence. Build,
focused headless harness, focused windowed harness, and regression harness all
exit 0 with `status=pass`.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-pointer-cursor-on-clickable-surfaces)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Probe | `git status --short` | 0 | runner reachable |
| Editor import/build | `godot --headless --path . --editor --quit-after 300` | 0 | clean scan, PointerCursor.gd loaded as autoload class |
| Focused headless | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ui_pointer_cursor.json` | 0 | `[Harness] status=pass exit=0`; result.json: 8/8 expectations pass |
| Regression | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_placement.json` | 0 | `[Harness] status=pass exit=0` |
| Focused windowed | `godot --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ui_pointer_cursor.json` | 0 | `status=pass`; screenshot action `outcome=captured saved=true`, 1920x1080 |

No host-shell Godot was used; every gate ran through the approved runner.

## Criterion evidence

1. Buttons show pointer (windowed + fullscreen) — Done. Fresh headless run:
   SpeedBtn/PlayBtn/AutoNext/Tower1 `cursor_shape == 2` (CURSOR_POINTING_HAND)
   via `scripts/ui/PointerCursor.gd` autoload (`node_added` hook + deferred
   whole-tree sweep). Cursor shape is assigned as a Control property,
   mode-independent; windowed mode freshly re-verified.
2. Other clickable surfaces — Done. AutoNext (check button) and Tower1
   (TowerShopSlot custom button widget, BaseButton subclass) assert == 2.
3. Non-clickables keep arrow — Done. HealthBar + UpgPanel Frame assert
   `cursor_shape == 0` in the same fresh run; autoload touches only BaseButton.
4. Windowed screenshot under `.gen/harness/ui_pointer_cursor/shots/` — Done.
   `hover_pointer_on_speed_btn.png` (1920x1080, ~1.88 MB) captured this run
   after `hover_ui` warped the mouse over SpeedBtn. Per plan note, Godot does
   not render the OS cursor in screenshots; evidence pair is programmatic
   assertion + hover screenshot.
5. Focused scenario passes headless + windowed — Done. Both runs `status=pass`,
   exit 0, all 8 expectations pass (fresh `.gen/harness/ui_pointer_cursor/result.json`).
6. smoke_placement still passes — Done. Fresh regression run `status=pass`, exit 0.

## Changed-file quality findings

Files reviewed against /opt/data/coding_rules.md: `project.godot` (autoload
registration appended last), `scripts/ui/PointerCursor.gd` (new, ~25 lines),
`scripts/testing/HarnessActions.gd` (`hover_ui` action), `scripts/testing/
HarnessValues.gd` (`ui_control` source; master's `nature` source preserved),
`tests/scenarios/ui_pointer_cursor.json`. No clear violations: minimal scope,
enum identifiers used (`Control.CURSOR_POINTING_HAND`, not raw literals where
available), no duplication, documented rationale for the two-mechanism design.
The `(node as BaseButton)` casts follow `is BaseButton` guards (safe, idiomatic
GDScript); noted advisorially in quality-notes.md. Dirty `logs/balance/*.csv`
is pre-existing runtime output from harness runs, not part of this feature.

New tests do not overlap existing coverage: `ui_control`/`hover_ui` harness
support and the `ui_pointer_cursor` scenario are new; no existing test asserted
cursor shapes.

## Blockers

None.

## Unverified items / manual testing

- manual_testing: required — human windowed/fullscreen hover pass across menus,
  HUD, modals plus ui_feels_broken sanity check remains for the manual-tester
  profile (owns `.gen/manual-report.md`). Automated coverage proves the
  programmatic cursor assignment; fullscreen visual confirmation is manual.
- Harness exit-time RID/ObjectDB leak warnings are pre-existing engine teardown
  noise present in baseline scenarios too, not introduced by this change.
