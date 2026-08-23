# check.md — revision-check-1 (harness-cannot-inject-gui-input, iteration 2)

## Verdict

classification: fixable

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-harness-cannot-inject-gui-input)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `["godot","--version"]` | 0 | Godot 4.4.1.stable |
| Typecheck/build | `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | clean import/parse |
| Focused test | `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/hud_controls_state.json"]` | 0 | `status=pass`, `.gen/harness/hud_controls_state/result.json`, 6/6 expectations passed |
| Full suite | hud_layer_roundtrip, hud_heart_beat_on_egg_damage, hud_other_panels, hud_wood_panels (one run_project_cmd call each) | 0 each | all `status=pass`, expectations all passed |

Key focused-run evidence: `[HARNESS-CLICK] press_button target=UpgradeBtn landed=true disabled=false`; result.json action 18 detail `{button: /root/Main/UI/Root/UpgPanel/Frame/VBox/UpgButtons/UpgradeBtn, landed: true, disabled_at_press: false}`; tower level 1 → 2 asserted (action 19); money 200 → 180 asserted (action 20).

## Criterion-by-criterion

1. press_button delivers a real press through Godot's input path and reports landing — **Pending**. Implementation (`HarnessActions.gd::_press_button` → `Viewport.push_input(event, true)` down/up pair) works and the focused scenario proves it end-to-end (level 1→2 via the click, exact cost deduction). Demoted for quality violations in the new code itself (see below).
2. Unresolvable target → `ok: false` naming the target — **Pending** (missing evidence). Code path exists (`HarnessActions.gd:160`) but no automated scenario exercises it; no passing test would fail if this branch broke.
3. Disabled Button → press does not land, handler does not run — **Pending** (missing evidence). Code path exists (`landed=false` when `disabled`), but the scenario only presses while enabled (it asserts disabled true→false, then waits and presses enabled). No automated test asserts the disabled-press behaviour.
4. Works headless by driving the input path directly — **Done**. All verification above ran under `--headless`; press landed and level assertion passed with the dummy display.
5. REFERENCE.md documents press_button (fields, return detail, headless behaviour) — **Done**. `.claude/skills/game-test/REFERENCE.md` gains a `press_button` table row plus a full section covering target resolution, return-detail table, failure modes, headless behaviour, and the log line. (Plan named `docs/REFERENCE.md`; the repo's game-test reference lives at `.claude/skills/game-test/REFERENCE.md` — treated as the same artifact.)
6. Debug `[HARNESS-CLICK]` log line per attempt — **Done**. Gated on `OS.is_debug_build()` per CLAUDE.md logging rule, and the scenario asserts the exact line via a `log` expectation (hud_controls_state.json), so removing it fails the suite.
7. hud_controls_state.json presses Upgrade after ≥0.5s post-selection and asserts level up — **Done**. Timeline: select (10–11) → wait 1.0s (17, five 0.2s poll ticks) → press_button (18) → tower.level==2 (19) → money==180 (20). Passed fresh.

## Changed-file quality findings

- scripts/testing/HarnessActions.gd: `_deliver_motion()` is added by this diff but never called — dead code from the change; remove it or wire it into the click sequence.
- scripts/testing/HarnessActions.gd: `_press_button` docstring says the action "drive[s] Control._gui_input directly", but the implementation uses `Viewport.push_input(event, true)` (the direct call is not callable in Godot 4). Same contradiction appears in `tests/scenarios/hud_controls_state.json` note 13 and in the REFERENCE.md press_button section's framing. Docs/comments must match the shipped mechanism.
- scripts/testing/AgentHarness.gd run-stamp change: reasonable fix for stale-log reuse; no violation found.

## Blockers

None. Runner healthy; all gates green.

## Unverified items

Criteria 2 and 3 (failure paths) are implemented but unproven by any automated test.
