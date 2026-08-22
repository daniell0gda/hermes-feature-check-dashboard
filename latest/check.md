# Check report: hud-theme-missing-wood-panel (issue #107)

Classification: pass

## Verdict

All 6 acceptance criteria verified fresh this iteration through the approved
project runner (`run_project_cmd`, project=`godot-td`,
workspace=`poke-defense-godot/issue-hud-theme-missing-wood-panel`). All
commands returned exit code 0.

## Verification commands and results (all via run_project_cmd, exit codes real)

1. Preflight `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
2. Fresh-import gate: deleted `.godot/imported` host-side (gitignored cache),
   then `["godot","--headless","--path",".","--editor","--quit-after","300"]`
   — exit 0 (~54s), full reimport including `wood_panel.png`. Only
   pre-existing dummy-renderer noise in the log, no HudTheme/wood_panel load
   errors.
3. Focused harness
   `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/hud_wood_panels.json"]`
   — exit 0, `[Harness] status=pass exit=0`. Result:
   `.gen/harness/hud_wood_panels/result.json` — all 6 expectations pass:
   source_png_exists=true, texture_path=res://textures/ui/hud/wood_panel.png,
   texture_loaded=true, style_class=StyleBoxTexture, log !contains "Failed to
   load resource ... wood_panel.png", game_state=paused.
4. Full harness `["...","--harness=res://tests/scenarios/smoke_placement.json"]`
   — exit 0, status=pass exit=0.

## Criterion evidence

- Panel texture path resolves to a repo PNG: PASS —
  `textures/ui/hud/wood_panel.png` exists on disk (untracked new file);
  HudTheme.tres ext_resource points at it; expectation source_png_exists=true.
- Theme loads after `.godot/imported` deleted: PASS — import gate + focused
  harness ran immediately after cache delete; texture_loaded=true with a live
  StyleBoxTexture.
- Named orphan `.import` files absent: PASS — after fresh import,
  `textures/ui/hud/` contains only `wood_panel.png` and `wood_panel.png.import`;
  icon_speed/wide_panel/woden_panel_wide_lightonly/wood_chip_on imports absent.
- Every `.import` under textures/ui/hud/ has a matching source: PASS — only
  wood_panel.png.import, matched by wood_panel.png beside it.
- hud_wood_panels scenario passes after imported-cache delete: PASS — see #3.
- HUD panels show wood panel backing: PASS (automated portion) — CaveProgressPanel's
  live stylebox resolves to a loaded StyleBoxTexture backed by the committed
  wood_panel.png; scenes/UI.tscn Root theme = HudTheme.tres. Pixel-level visual
  confirmation remains for the windowed manual-tester pass (headless renderer
  skips screenshots by design; plan declares `manual_testing: required`).

## Changed-file quality findings

Diff (`scenes/UI.tscn`, `scripts/ui/UI.gd` +26 lines, new
`tests/scenarios/hud_wood_panels.json`, `themes/hud/HudTheme.tres`,
`textures/ui/hud/wood_panel.png`): surgical scope, typed GDScript per
CLAUDE.md, guard-clause structure, no rule violations. No demotions.
No open quality-notes entries existed; none appended.

## Blockers

None.

## Unverified / deferred to manual tester

- Windowed screenshot/pixel inspection of the wood panel backing (request.md
  requires windowed manual testing; headless cannot capture pixels).
