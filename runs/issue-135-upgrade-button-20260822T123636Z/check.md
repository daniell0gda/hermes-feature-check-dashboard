# Check report: upgrade-button-disabled-without-money (issue #135)

classification: pass

## Verdict
All 7 acceptance criteria verified Done through fresh runner-based verification. Build/typecheck gate passed; focused harness passed; full regression scenario passed. No quality violations found in the changed code.

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-upgrade-button-disabled-without-money)
1. Preflight `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
2. Typecheck/build `["godot","--headless","--path",".","--editor","--quit"]` — exit 0, no script errors (only pre-existing invalid-UID theme warnings).
3. Focused `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_135_upgrade_button_disabled.json"]` — exit 0, `[Harness] status=pass exit=0`. Fresh result at `.gen/harness/issue_135_upgrade_button_disabled/result.json`: all 15 actions ok, all 5 expectations pass.
4. Full suite `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/tower_details_panel.json"]` — exit 0, `[Harness] status=pass exit=0`, result at `.gen/harness/tower_details_panel/result.json`.

## Criteria evidence
1. Disabled without further input when broke — PASS. Scenario sets money 0, selects tower, opens panel; `wait_for_condition ui_call is_upgrade_button_disabled == true` ok; engine log `[UPG-BTN] upgrade button unavailable kind=generic level=1 cost=20 money=0`.
2. Funding enables within one frame while panel open — PASS. `_set_money(9999)` then `is_upgrade_button_disabled == false` within 0.5s window; log shows one "available" transition.
3. Spending below cost disables again within one frame — PASS. `_set_money(5)`, condition true again; second "unavailable" transition.
4. Panel opened while broke shows disabled from first frame — PASS. First transition logged is unavailable on `on_tower_selected` with money already 0; ordering regex `unavailable.*available.*unavailable` passes.
5. Debug-only `[UPG-BTN]` trace with kind, level, cost, money — PASS. Log lines carry all four fields; print guarded by `OS.is_debug_build()` in `scripts/ui/UI.gd::_apply_upgrade_availability`.
6. Harness drives all three states end-to-end via `ui_call` — PASS (result.json expectations all pass, including both `contains` and ordering/flap regexes).
7. Single-transition discipline — PASS. The `!regex "(?s)upgrade button available.*?upgrade button available"` expectation passed against the live run log; exactly one transition per direction observed.

## Test-overlap check
Existing `tests/scenarios/hud_controls_state.json` covers the same write-once discipline for this button but its expectations target a `[UI] upgrade button ...` trace and helper methods (`get_armed_mode_buttons`, `money_readout_settled`) that do not exist in current UI.gd; its last recorded result is status=timeout with failing expectations (pre-existing breakage, unrelated files — recorded below, not demoting any criterion). The new scenario asserts different surface (`[UPG-BTN]` trace + `is_upgrade_button_disabled()`), so it does not duplicate passing coverage.

## Changed-file quality findings
- `scripts/ui/UI.gd` diff (+33/-2): typed locals, guard-clause style, single-purpose helpers, debug-only tagged state-transition logging per CLAUDE.md — compliant. No violations.
- `tests/scenarios/issue_135_upgrade_button_disabled.json` (new): follows hud_controls_state pattern, fixed costs documented. No violations.
- No scope creep beyond the cluster's file set.

## Cross-cutting quality notes (advisory)
Appended to `.gen/quality-notes.md`: stale/broken pre-existing scenario `hud_controls_state.json` references methods absent from current UI.gd (`get_armed_mode_buttons`, `money_readout_settled`) and a `[UI] upgrade button` log tag that no longer exists — its harness result is status=timeout. Legacy issue, not introduced by this change; tracked separately per TECHNICAL DEBT policy.

## Blockers
None.

## Unverified items
- Manual testing (`manual_testing: required`) has not been performed yet — `.gen/manual-report.md` is absent. Owned by the manual-tester profile / leader; windowed screenshots per `.gen/ui_scenario.md` still owed before merge. This does not change the automated-criteria verdict above.
