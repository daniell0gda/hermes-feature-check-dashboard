# Coder report: 1-upgrade-button-affordability-gating\n\n# Coder report: 1-upgrade-button-affordability-gating

## Changed files
- `scripts/ui/UI.gd` — modified
- `tests/scenarios/issue_135_upgrade_button_disabled.json` — new

## Criteria
- Upgrade button disabled without further input when money < upgrade cost — Done
- Funding while panel open enables within one frame — Done
- Spending below cost while panel open disables within one frame — Done
- Panel opened while broke shows disabled from first frame — Done
- `[UPG-BTN]` debug log on each state change (kind, level, cost, money), silent in release — Done
- Harness scenario drives no-money → disabled, funded → enabled, re-drained → disabled via `ui_call` on live button state — Done
- Single-transition discipline: at most one availability transition per direction — Done

## Commands and results
- `godot --headless --path . --editor --quit-after 300` — exit 0; import clean, no script errors
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_135_upgrade_button_disabled.json` — exit 0, `[Harness] status=pass`. Log shows exactly:
  `[UPG-BTN] upgrade button unavailable kind=generic level=1 cost=20 money=0`
  `[UPG-BTN] upgrade button available kind=generic level=1 cost=20 money=9999`
  `[UPG-BTN] upgrade button unavailable kind=generic level=1 cost=20 money=5`
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/tower_details_panel.json` — exit 0, `[Harness] status=pass`

## Notes
- Root defect: `_set_upgrade_button` wrote `upg_btn.disabled = true` unconditionally before every reprice, so the 0.2s selection poll and every money_changed flipped the button false→true→false (BaseButton::set_disabled(true) clears press_attempt, swallowing in-flight clicks).
- Fix: `_set_upgrade_button` now routes through `_apply_upgrade_availability(affordable, cost)` which writes `disabled` only on a real change and prints the `[UPG-BTN]` trace (debug builds only) on transitions. Unpriced selections (trap/hole/exit/max level) are always "available" (not disabled).
- New public getter `UI.is_upgrade_button_disabled()` is the `ui_call` assertion surface — the harness cannot click a Button.
- Scenario costs are fixed: generic tower 20 (200→180 on placement), upgrade cost at level 1 = 20; funding to 9999 / draining to 5 straddles any threshold.
- Manual testing required (player-visible UI): see `.gen/ui_scenario.md` — windowed screenshots of the Upgrade button in both states while the panel stays open.
- classification: pass
\n