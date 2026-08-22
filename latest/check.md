# Check report — iteration 1 — issue #101 (underground side panel carve/place exit)

Classification: **fixable**

## Verdict

Build/typecheck gate FAILED. The only implementation commit (`scripts/ui/UI.gd`) does not parse:
`Control.SIDE_LEFT/SIDE_TOP/SIDE_RIGHT/SIDE_BOTTOM` do not exist in Godot 4.4.1, producing four
SCRIPT ERRORs at UI.gd:1012-1015 and `Failed to load script res://scripts/ui/UI.gd`. Because the
project's UI script cannot load, every harness scenario that exercises the underground UI is
broken at runtime, so no acceptance criterion can be held Done. All six criteria moved to Pending.
No criteria were previously marked Done in a status file (none existed); this is a fresh verdict.

Runner gate: PASS — all project commands ran through `run_project_cmd`
(project=`godot-td`, workspace=`poke-defense-godot/issue-underground-side-panel-carve-place-exit-`).
The runner is healthy; this is a project code failure, not infra.

## Commands and exit codes (all via run_project_cmd)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| `godot --headless --path . --editor --quit-after 300` (typecheck/build gate) | 0 process-wise but SCRIPT ERRORs | Parse Error: Cannot find member "SIDE_LEFT"/"SIDE_TOP"/"SIDE_RIGHT"/"SIDE_BOTTOM" in base "Control" at res://scripts/ui/UI.gd:1012-1015; Failed to load script UI.gd |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/hud_wood_panels.json` (focused) | 1 (HTTP 422 from runner) | fails with the same parse error; screenshots skipped headless |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_underground_visible.json` (full) | 1 (HTTP 422 from runner) | fails with the same parse error |
| `godot --headless --path . res://tests/ui/test_enemy_armor_bar.tscn` | 0 | 35 ok / 0 failed (regression sanity: suite pattern healthy) |
| `godot --headless --path . res://tests/ui/test_titled_panel_close_corner.tscn` | 0 | 22 ok / 0 failed |
| `godot --headless --path . res://tests/ui/test_enemy_health_bar_oiled_icon.tscn` | 0 | 8 ok / 0 failed |

## Acceptance criteria — evidence and status

1. map_1 underground controls inside panel inner box — PENDING. The dedicated scenario
   `.gen/harness/hud_side_panel_fit/result.json` shows status=timeout with unmet
   `ui_call.all_inside == true` (actual null), and current runs cannot even reach it because
   UI.gd does not load.
2. Porter-unlocked map check — PENDING. Only covered by the same broken scenario; never passed.
3. hud_underground checkpoint shows no overlap — PENDING. hud_wood_panels has an old
   `status: pass` artifact, but that run predates the parse-breaking change; fresh rerun exits 1.
   Also note the checkpoint itself is screenshot-only and reports "skipped (headless)"; the
   geometry assertion lives in the separate hud_side_panel_fit scenario, which times out.
4. Place Exit price text unclipped — PENDING. Asserted only by hud_side_panel_fit
   (`place_exit_price_text contains "coins"`), which never gets past its first condition.
5. Behaviour preserved (carve/place block/place exit arms) — PENDING. Same broken scenario.
6. Controls visible only on underground layer — PARTIALLY evidenced but PENDING. The scene
   already places DigBtns inside SidePanel/SideColumn and SurfaceRightContainerController hides
   the surface row per layer; however the implementor also added duplicate visibility writes of
   `surface_right_container` inside `_update_button_visibility` (UI.gd ~1461/1475), which double-
   manages state the controller script already owns. No passing test proves the criterion end to
   end while the parse error stands.

## Changed-file quality findings

- scripts/ui/UI.gd:1012-1015 — `Control.SIDE_*` members do not exist (enum is global `Side` /
  legacy `MARGIN_*`). Four GDScript parse errors; blocks the entire feature. Fix required.
- scripts/ui/UI.gd (new `get_underground_panel_layout_report`, ~50 lines) — quality concern:
  uses type-cast style `as Label`/`as PanelContainer` contrary to /opt/data/coding_rules.md
  ("Type casts are forbidden"; pre-existing file uses them sparingly). Advisory; recorded in
  .gen/quality-notes.md. Not demoting beyond what the parse error already forces.
- scenes/UI.tscn — unchanged vs HEAD; the "relayout" was achieved by moving DigBtns into the
  panel in an earlier commit plus runtime offset changes in UI.gd. No new test overlap found
  (no prior test asserted this layout; hud_side_panel_fit.json is new).

## Blockers

- None infra. Runner, workspace, image, auth all healthy via `run_project_cmd`.

## Unverified items

- All six plan criteria (see above). Manual/windowed screenshot verification was not performed:
  the manual-tester profile owns manual evidence and the harness screenshot checkpoints skip in
  headless mode; regardless, nothing can pass until the parse error is fixed.

## Required next step for coder

Replace `Control.SIDE_LEFT/TOP/RIGHT/BOTTOM` with the correct enum references (e.g. global
`Side.SIDE_LEFT` etc.), rerun the editor/import gate, then rerun both harness scenarios through
the runner until `hud_side_panel_fit` reports `status: pass`.
