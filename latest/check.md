# Check Report: harness-cannot-inject-gui-input (issue #126)

classification: fixable

## Verdict

Implementation exists as uncommitted working-tree changes on
`issue/harness-cannot-inject-gui-input` (HEAD d241462):
`scripts/testing/HarnessActions.gd`, `tests/scenarios/hud_controls_state.json`,
`.claude/skills/game-test/REFERENCE.md`. Build/import gate passes, but the
focused scenario fails with `status: timeout`, so no criterion can be accepted
as Done: the new `press_button` action was never reached or executed in any
observed run (no `[HARNESS-CLICK]` line appears in any log).

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-harness-cannot-inject-gui-input)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `godot --version` | 0 | 4.4.1.stable |
| Typecheck/build | `godot --headless --path . --editor --quit-after 300` | 0 | import/parse clean |
| Focused test | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_controls_state.json` | **1** | `status: timeout` at action index 4 |
| Baseline probe (changes stashed, HEAD) | same focused command | 1 | also `timeout` at action index 4 |
| Sanity | `--harness=res://tests/scenarios/hud_layer_roundtrip.json` | 0 | `status=pass exit=0` |

Fresh result: `.gen/harness/hud_controls_state/result.json`
(2026-08-23T00:07:57, `status: timeout`). Full-suite loop not run to completion:
the first scenario in the list fails, so the gate is red regardless.

## Root cause of the focused failure

The scenario's `wait_for_condition` steps resolve
`{"source":"ui_call","method":"get_armed_mode_buttons"}` (and
`money_readout_settled`, `get_money_readout`, `is_money_rolling`). None of these
methods exist anywhere in the tree — verified by grep across all worktrees and
`git log --all -S` across every branch (only hit: the scenario JSON itself,
introduced in c7a6104). Result: `"UI has no method 'get_armed_mode_buttons'"`,
action index 4 times out, timeline aborts before reaching the press_button step.
This is pre-existing (baseline HEAD run fails identically), but the coder
modified this exact scenario and its expectations without making it green, so
the acceptance plan's own focused test cannot pass.

## Acceptance criteria

1. press_button delivers real GUI-path press, reports landed — **Pending** (implemented in HarnessActions.gd::_press_button but never executed; zero runtime evidence)
2. unresolvable target → ok:false naming target — **Pending** (code path present, never tested)
3. disabled button → landed:false, handler not run — **Pending** (code path present, never tested)
4. works headless via Control._gui_input — **Pending** (approach plausible, never demonstrated headless)
5. REFERENCE.md documents fields/detail/headless behaviour — implemented, in `.claude/skills/game-test/REFERENCE.md` (the harness reference; repo has no `docs/REFERENCE.md`) — held Pending because the whole cluster's test gate is red
6. Debug-build `[HARNESS-CLICK]` log per attempt — **Pending** (print exists but unconditional, not debug-gated; never observed in a run)
7. hud_controls_state presses Upgrade after ≥0.5s wait, asserts level increase — **Pending** (steps added to scenario; scenario times out before reaching them)

## Changed-file quality findings

- `scripts/testing/HarnessActions.gd`: `[HARNESS-CLICK]` print runs in all builds; criterion says debug-build. Minor.
- `scripts/testing/HarnessActions.gd`: stray double blank line after `_resolve_button`; docstring claims "a named UI button such as ... 'upg_btn'" while resolution requires a leading `@` (`@upg_btn`). Documentation inconsistency inside the code.
- `tests/scenarios/hud_controls_state.json`: expectation money changed 158→110 and sale/kill arithmetic left stale in notes; scenario still depends on four UI methods that exist nowhere, so it cannot pass even before this issue's changes.
- No test overlap issues found (no existing test asserts press_button).

## Blockers

None infrastructural. Runner healthy; all commands executed through
run_project_cmd successfully. The fix is code work: make the focused scenario
green (either add the missing UI helper methods or rewrite its conditions using
existing sources such as `is_carving`), then re-run focused + full suite so the
press_button step actually executes and produces `[HARNESS-CLICK]` +
level-assertion evidence.

## Unverified items

All seven criteria remain unverified pending a passing focused run.
