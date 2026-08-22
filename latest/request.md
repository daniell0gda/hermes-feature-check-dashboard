# Issue #128 — Tower details panel blinks and returns mid-reflow when a placement mode is armed

Implement the issue exactly as described in https://github.com/daniell0gda/poke-defense-godot/issues/128.

Acceptance criteria:
- Arming or clearing a placement mode leaves the tower details panel exactly as it was; hide it only when selection genuinely goes away.
- With a tower selected and Carve armed, the wood frame encloses Upgrade/Sell buttons and the Active row; no one-measurement-behind reflow.
- Update `tests/scenarios/hud_wood_panels.json` so `hud_mode_carve_armed` shows a complete frame and asserts `UI.get_upgrade_panel_text()` still reports the tower heading immediately after `_on_carve`.

Use native Linux Godot verification through the approved `godot-td` runner, including fresh visual/windowed evidence for the existing panel drawing correctly. Preserve the flat `.gen` contract and do not close, merge, or push the issue implicitly.
