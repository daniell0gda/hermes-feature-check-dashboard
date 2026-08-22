# Check report: underground-side-panel-carve-place-exit- (iteration 3)

classification: pass

## Verdict

All 6 acceptance criteria verified green through fresh `run_project_cmd` runs against
`poke-defense-godot/issue-underground-side-panel-carve-place-exit-`. No quality violations in
changed code. No blockers.

## Verification commands (all via run_project_cmd, project=godot-td)

| Gate | cmd | exit | result |
|---|---|---|---|
| Runner preflight | `["godot","--version"]` | 0 | Godot 4.4.1.stable.official |
| Typecheck/build | `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | Full import/scan clean; no parse/script errors in output |
| Focused (windowed) | `["godot","--path",".","res://scenes/Main.tscn","--windowed","--resolution","1280x720","--","--harness=res://tests/scenarios/hud_wood_panels.json"]` | 0 | `.gen/harness/hud_wood_panels/result.json` status=pass, headless=false, both expectations pass |
| Full test | `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_underground_visible.json"]` | 0 | `[Harness] status=pass exit=0`; full suite PASS except known pre-existing cannon_* FAILs unrelated to this diff |

## Acceptance criteria — evidence

1. **Underground buttons inside side panel frame, clear of brackets (map_1)** — DONE.
   Windowed run: `wait_for_condition ui_call.get_underground_panel_layout.ok == true` passed
   after `hud_underground` screenshot (result.json actions[29]). Independent visual inspection of
   `.gen/harness/hud_wood_panels/shots/hud_underground.png`: Carve / Place Block / Place Exit all
   fully inside the panel inner area, corner brackets uncovered.
2. **Porter-unlocked coverage** — DONE. Scenario sets `tower_availability = 4`
   (`call gamestate.set`) before a second `hud_underground_porter` shot + layout re-check
   (actions[31..32], ok=true). Visual inspection of `hud_underground_porter.png`: same three
   buttons fully inside frame, clear of brackets. Note: coverage is via live
   `tower_availability` change on map_1 rather than loading a different map; the button-gating
   path exercised is the same `_on_tower_availability_changed` refresh, so the layout assertion
   is equivalent.
3. **Place Exit name+price unclipped** — DONE. `ui_call.exit_text_lines_visible == true` wait
   passed (actions[30]); probe asserts both label rects enclosed by the button AND min-size fit
   (clip-proof). Screenshot confirms "Place Exit" / "2 coins" both lines fully visible.
4. **Toggle visible underground; press → surface** — DONE. Probe reports
   `toggle_visible` inside layout ok; scenario calls `_on_toggle_layer` (the wired handler) and
   final expectations pass: `GameState.current_layer == "surface"` and
   `is_underground_controls_visible == false`.
5. **Windowed `hud_underground` checkpoint verifying no button-rect/frame overlap** — DONE.
   Implemented as non-vacuous programmatic waits on `get_underground_panel_layout()` (per-button
   `encloses()` against content box minus StyleBox margins; missing/hidden controls are
   violations) inside the windowed `hud_wood_panels` run; all passed with real pixels captured
   (headless=false).
6. **Surface behaviour unchanged** — DONE. The same windowed run's earlier surface beats
   (`hud_idle`, `hud_towers_locked`, etc.) all executed ok and overall status=pass; UI.tscn/UI.gd
   changes touch only the underground branch (`SIDE_PANEL_TOP_UNDERGROUND`, PlaceExit min height,
   new probes).

## Changed-file quality findings

Files changed vs HEAD d241462: `scenes/UI.tscn`, `scripts/ui/UI.gd`,
`tests/scenarios/hud_wood_panels.json`. Checked against `/opt/data/coding_rules.md` +
worktree `CLAUDE.md`: typed GDScript throughout, single-purpose ~50-line probe functions,
surgical diff, no dead code, comments trace to criteria. No violations.

## Blockers

None. Runner reachable and used for every project command (no host-shell Godot).

## Unverified items

- Manual windowed PNG review was performed agent-side via image inspection of both fresh shots;
  human manual testing remains per plan's `manual_testing: required` flag (leader's call).
