# check.md — revision-check-2 (harness-cannot-inject-gui-input, iteration 3)

## Verdict

classification: pass

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-harness-cannot-inject-gui-input)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `["godot","--version"]` | 0 | Godot 4.4.1.stable.official.49a5bc7b6 |
| Typecheck/build | `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | clean import/parse, no script errors |
| Focused test | `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/hud_controls_state.json"]` | 0 | `status=pass`, `.gen/harness/hud_controls_state/result.json` fresh, all expectations passed |
| Full suite | hud_layer_roundtrip, hud_heart_beat_on_egg_damage, hud_other_panels, hud_wood_panels (one run_project_cmd call each) | 0 each | all `status=pass`, result.json written fresh per scenario |

Focused-run evidence:
- `[HARNESS-CLICK] press_button target=UpgradeBtn landed=false disabled=true` (action 17) followed by `harness.last_action.landed == false`, `disabled_at_press == true`, `tower.level == 1.0` (actions 18–20) → disabled press did not land and handler did not run.
- Action 15 `press_button target 'NoSuchButtonAnywhere' did not resolve to a live Button node` with `ok:false`, asserted by `harness.last_action.ok == false` (action 16) → unresolvable-target failure path proven.
- `[HARNESS-CLICK] press_button target=UpgradeBtn landed=true disabled=false` (action 24), tower level 1→2 (action 25), money ==180 after upgrade (action 26); wait of 1.0s (action 23) spans five 0.2s selection-poll ticks.
- Log expectation asserts the exact `[HARNESS-CLICK] ... landed=true disabled=false` line from this run's out.log slice.

## Criterion-by-criterion

1. press_button delivers a real press through Godot's input path (`Viewport.push_input(event, true)` down/up pair, not `pressed.emit()`) and detail reports landing on an enabled button — **Done**. Fresh focused run proves end-to-end: level 1→2 via the click, exact cost deduction, detail `{landed: true, disabled_at_press: false}`.
2. Unresolvable target → `ok:false` naming the target — **Done**. Scenario action 15 presses `NoSuchButtonAnywhere`; result detail names the target; action 16 asserts `harness.last_action.ok == false`. Revision 2 added the `harness.last_action.*` value path (HarnessValues.gd) making this assertable.
3. Disabled Button → press does not land, connected handler does not run — **Done**. Scenario actions 17–20: press while button disabled (money below upgrade cost, `[UPG-BTN] upgrade button unavailable` in log), `landed=false`, `disabled_at_press=true`, tower stays level 1. Later enabled press levels the tower, proving the earlier non-press was the disabled gate.
4. Works headless by driving the input path directly — **Done**. All verification ran under `--headless` (dummy display); press landed and level assertion passed. Docs correctly explain that direct `Control._gui_input` is not callable from GDScript in Godot 4 and push_input is the working headless mechanism.
5. REFERENCE.md documents press_button (fields, return detail, headless behaviour) — **Done**. `.claude/skills/game-test/REFERENCE.md` diff adds a press_button section matching the shipped mechanism.
6. Debug `[HARNESS-CLICK]` log line per attempt carrying target / landed / disabled — **Done**. Gated on `OS.is_debug_build()`; both lines observed in the fresh run; the scenario asserts the enabled-press line via a `log` expectation, so removing it fails the suite.
7. hud_controls_state.json presses Upgrade after ≥0.5s past selection across poll ticks and asserts level up — **Done**. Timeline: select (10–11) → wait 1.0s (23, ≥0.5s, five 0.2s ticks) → press_button (24) → level==2 (25) → money==180 (26). Passed fresh.

## Changed-file quality findings

Revision-1 findings resolved:
- Dead `_deliver_motion()` removed from HarnessActions.gd — no longer present in the diff.
- Docstring/comment/scenario/REFERENCE.md wording now consistently names `Viewport.push_input(event, true)` instead of the incorrect "drive Control._gui_input directly".

New code checked against /opt/data/coding_rules.md and CLAUDE.md: typed variables used throughout new GDScript; functions small and single-purpose; debug logging follows the `[TAG]` + `OS.is_debug_build()` convention; changes surgical (5 files, all trace to the issue). No open violations.

## Blockers

None.

## Unverified items

None.
