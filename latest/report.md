# Team-leader report

- **Result:** failed
- **Classification:** **fixable**
- **Feature:** Tower details panel blink and reflow
- **Run:** issue-128-tower-details-blinks-on-mode-arm
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- Arming Carve while a tower is selected leaves the tower details panel visible with its content unchanged: `UI.get_upgrade_panel_text()` still reports the selected tower's heading immediately after `_on_carve` runs.
- Arming each other placement mode (Dig Hole, Place Exit, Place Block) while a tower is selected likewise leaves the details panel visible and unchanged.
- Cancelling the armed Carve mode restores the details panel to exactly what it showed before Carve was armed: same heading and stat rows from `get_upgrade_panel_text()`.
- The details panel hides only when the selection genuinely goes away: with no tower, trap, hole or exit selected, the panel is hidden and `get_upgrade_panel_text()` no longer reports any previous selection's heading.
- Debug-build [HUD] log line per placement-mode arm and clear event, naming which mode changed.
- The updated `hud_wood_panels.json` drives a tower selection followed by `ui._on_carve`, captures the `hud_mode_carve_armed` screenshot, and asserts via `wait_for_condition` (`source: ui_call`, `get_upgrade_panel_text`) that the tower heading is still reported immediately after `_on_carve`; the scenario passes end to end.

## ⬜ Pending
- With a tower selected and Carve armed, the windowed `hud_mode_carve_armed` screenshot shows the wooden details-panel frame completely enclosing the Upgrade/Sell buttons and the Active row, with no one-measurement-behind reflow clipping the frame. — quality: themes/hud/HudTheme.tres: references non-existent res://textures/ui/hud/wood_panel.png (absent from worktree and git history), so HudTheme fails to load and the fresh windowed screenshot shows the panel without any wooden frame; regenerate/restore the texture, then re-run the windowed hud_wood_panels scenario

## ❌ Impossible
- (none)

## Check

# Check report — issue-128-tower-details-blinks-on-mode-arm (iteration 1)

Classification: **fixable**

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-tower-details-blinks-on-mode-arm)
- `["godot","--version"]` — exit 0, Godot 4.4.1.stable.official.49a5bc7b6
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0, clean import/parse of UI.gd
- Focused/windowed: `["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/hud_wood_panels.json"]` — exit 0, `[Harness] status=pass exit=0`, fresh `.gen/harness/hud_wood_panels/result.json` (29 actions ok); log shows `[HUD] placement mode armed: Carve` and `[HUD] placement mode cleared: Carve`
- Full: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/tower_details_panel.json"]` — exit 0, `[Harness] status=pass exit=0`, fresh `.gen/harness/tower_details_panel/result.json`

## Acceptance criteria evidence

### Cluster 1 — panel-persists-across-mode-arm-clear (scripts/ui/UI.gd) — Done
- Arming Carve keeps the panel: scenario asserts `get_upgrade_panel_text()` contains "Tower" immediately after `_on_carve` (result.json action index 15→16, ok=true). Implementation removes the `_upd_upg_panel(null)` blink from `_on_carve` / `_on_place_exit` / `_on_place_block`.
- Other modes: all four arm handlers route through shared `_begin_placement_mode(label)` and none touch the panel; same code path as the asserted Carve case.
- Cancel restores readout: `_end_placement_mode` re-shows the panel when a pre-arm readout existed; property setters on `carving_active`/`placing_exit`/`place_block_active` catch clears driven directly by Placement/Game (`set("carving_active", false)`), plus scenario-facing `clear_carve_mode()`. Scenario action 19→20 asserts heading still present after clear.
- Hide only when selection goes away: `_upd_upg_panel(null)` empty-selection path unchanged; full `tower_details_panel` suite passes (clear_selection hides panel, hole/trap/exit selections assert correct content).
- Debug [HUD] logs: observed in live run output for arm ("placement mode armed: Carve") and clear ("placement mode cleared: Carve"), gated by `OS.is_debug_build()`.

### Cluster 2 — hud-carve-armed-scenario-and-frame (tests/scenarios/hud_wood_panels.json) — partially done
- Scenario wiring (selection → `_on_carve` → immediate heading assertion → settle → `hud_mode_carve_armed` screenshot → `clear_carve_mode` → assertion → `hud_mode_carve_cleared` shot): implemented exactly as planned and passing end to end.
- Visual criterion FAILED on fresh evidence: `.gen/harness/hud_wood_panels/shots/hud_mode_carve_armed.png` shows the details panel with NO wooden frame. Root cause is not this diff: `themes/hud/HudTheme.tres` references `res://textures/ui/hud/wood_panel.png`, which exists neither in the worktree nor anywhere in git history (its generator source JPGs were dropped before commit 72fc03b; see commit 04bd73c's own note "it cannot run"). The whole theme fails to load at runtime, so every themed control renders unstyled in a fresh checkout. No clipping/reflow regression is observable, but the "wooden frame" requirement itself cannot be confirmed until the texture is restored/regenerated.

## Changed-file quality
- `scripts/ui/UI.gd` (+82/−9): surgical; typed vars, guard clauses ≤2 nesting, docstrings, debug-only logging per CLAUDE.md. Property-setter pattern for mode flags is reasonable and minimal. No violations.
- `tests/scenarios/hud_wood_panels.json` (+21): matches plan's required wait_for_condition/call/screenshot shape.
- Untracked/unrelated: none beyond workflow artifacts.

## Blockers
- Missing asset `res://textures/ui/hud/wood_panel.png` (pre-existing repo defect, recorded in .gen/quality-notes.md). Blocks only the visual frame criterion.

## Unverified items
- Wooden-frame appearance of the details panel under Carve (pending texture fix + windowed re-run).

Classification rationale: one criterion pending due to a concrete missing-asset defect that is fixable within the project; runner and all gates healthy. Not blocked (runner works via run_project_cmd throughout).
