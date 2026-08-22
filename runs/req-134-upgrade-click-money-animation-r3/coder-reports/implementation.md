# Coder report: implementation (revision 2, task revision-code-2)

## Changed files
- `tests/scenarios/upgrade_click_money_popup.json` — modified (only file changed this iteration)

## Criteria
- Verified in-game via windowed screenshot — Done (was the sole Pending criterion; other two were already Done and untouched)

## Commands and results
- `godot --version` — exit 0; 4.4.1.stable.official.49a5bc7b6
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` — exit 0; status=pass; all 3 expectations pass (money 1460 < 1500, tower level == 2, reward_popups == 0)
- `godot --path . res://scenes/Main.tscn --resolution 1280x720 -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` — exit 0; status=pass; both screenshots `outcome: captured` (1920x1080 PNGs in `.gen/harness/upgrade_click_money_popup/shots/`)

## Notes
- Root cause of the previous visual failure was twofold, both scenario-side (no gameplay code changed):
  1. The details panel occluded the tower at capture time → fixed by deselecting before the shot
     (`towers.select_at` at [100,0,100] + `ui.on_tower_selected` empties/hides `upg_panel`; verified:
     panel gone from the after shot).
  2. The scene fade-in overlay (TransitionUtils, ~1.9 s wall in windowed llvmpipe runs) was still up
     during both captures, dimming/washing out the small yellow Label3D → fixed by inserting a
     1.2 s wait before the first screenshot.
- Popup visibility evidence: yellow text pixels isolated at screen ~(692-999, 351-519) in the after
  shot (present only in "after", absent in "before"); vision review of tight crops reads floating
  yellow coin text next to the portal/tower position ("+20 coins!" rendered softly under llvmpipe,
  OCR is fuzzy but two independent reads confirm a yellow floating coin label).
- Gotcha for tester: windowed harness runs must budget ≥ ~2 s of fade-in before any screenshot
  checkpoint; headless runs are unaffected (fade completes near-instantly).
