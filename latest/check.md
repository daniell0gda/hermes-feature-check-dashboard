# Check report — issue #132 move tower details panel to the right side (revision-check-1)

classification: fixable

## Verdict

Implementation exists on this workspace as uncommitted changes
(`M scenes/UI.tscn`, `M scripts/testing/HarnessValues.gd`, `M scripts/ui/UI.gd`,
`?? tests/scenarios/tower_details_panel_right_side.json`; base commit `e333243`).
`Root/UpgPanel` and its `TitlePlate` are re-anchored from top-center
(anchors_preset 5) to right-edge dock (anchors_preset 1, anchor_left/right = 1.0,
offset_right -16, grow_horizontal 0). New rendered-geometry helpers
(`UI.get_upgrade_panel_rect()`, right-edge-gap and overlap-ratio helpers) and a
new harness `ui` value source feed a new geometry scenario. Fresh verification
through the runner confirms criteria 1 and 3. Criterion 2 stays Pending: its
gameplay-UI half is proven, but the "or the tower it describes" half has no
assertion anywhere, and the request-required windowed manual testing
(`ui_feels_broken` verdict, `.gen/manual_testing.md`) does not exist yet.

## Acceptance criteria evidence

1. "Showing a tower's details displays the panel docked on the right side of the
   screen." — DONE. Fresh focused harness (run below) status=pass with all 12
   actions ok: panel visible, right-edge gap to viewport <= 20 px, panel
   position.x >= 960, end.x >= 1900, all read from `get_global_rect()` rendered
   pixels on the real `scenes/Main.tscn` selection path.
   Evidence: `.gen/harness/tower_details_panel_right_side/result.json`
   (fresh run, status=pass, exit 0).
2. "Panel does not overlap gameplay-critical UI or the tower it describes." —
   PENDING (missing evidence). Overlap ratio vs `Root/ItemList`,
   `Root/ButtonsContainer`, `Root/TopBar` all asserted == 0 and passing. But no
   automated or manual assertion covers the described tower's own screen area,
   and the request requires windowed manual testing with a `ui_feels_broken`
   verdict for visible player-facing UI changes; neither exists.
3. "Layout stays correct across window resize / different resolutions." — DONE.
   Docking is pure anchor-driven (`anchor_* = 1.0`); same scenario re-run at
   `--resolution 1280x720` through the runner: exit 0, status=pass, all six
   geometry conditions ok again.

## Commands run (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-move-tower-details-panel-right-side)

- `godot --version` → exit 0 (4.4.1.stable) — runner healthy.
- `godot --headless --path . --editor --quit-after 300` → exit 0 (import gate;
  pre-existing glb UID/import warnings unrelated).
- Focused geometry harness
  `godot --headless --path . scenes/Main.tscn --rendering-method gl_compatibility
  --rendering-driver opengl3 --audio-driver Dummy --
  --harness=res://tests/scenarios/tower_details_panel_right_side.json`
  → exit 0, result.json status=pass, all 12 actions ok.
- Second resolution: same command plus leading `--resolution 1280x720`
  → exit 0, status=pass.
- Regression `--harness=res://tests/scenarios/tower_details_panel.json`
  → exit 0, status=pass (panel text content unchanged).

## Changed-file quality findings

Reviewed against /opt/data/coding_rules.md and CLAUDE.md:

- `scripts/ui/UI.gd`: new methods are small, single-purpose, fully typed, reuse
  `get_upgrade_panel_rect()` instead of duplicating rect access. OK.
- `scripts/testing/HarnessValues.gd`: `_upgrade_panel_geometry` is typed and
  documented; the inline conditional for `"visible"` and the position/size/end
  ternary chain are dense but functional. Minor style only — advisory, not a
  violation.
- No type casts added; surgical scope (no unrelated edits). OK.
- Test overlap check: new `tests/scenarios/tower_details_panel_right_side.json`
  asserts geometry/placement; the pre-existing `tower_details_panel.json`
  asserts text content only. No duplicated coverage.

## Blockers

None infra. Remaining gap is content, not plumbing: criterion 2's tower-overlap
half and the required windowed manual test (owned by manual-testing, not this
checker).

## Unverified items

- Criterion 2 "or the tower it describes" (no assertion exists).
- Windowed visual sanity (`ui_feels_broken`) — manual_testing required by
  request, not yet produced.
