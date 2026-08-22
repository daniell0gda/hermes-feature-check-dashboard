## ✅ Done

## ⬜ Pending
- After switching to the underground layer on `map_1`, all three underground controls (Carve, Place Block, Place Exit) render entirely inside the side panel's inner box and clear of its corner brackets.
- After switching to the underground layer on a map where Porter is unlocked, all three underground controls still render entirely inside the side panel's inner box and clear of its corner brackets.
- The `hud_underground` checkpoint in `tests/scenarios/hud_wood_panels.json` completes with no button rect overlapping the side panel frame. — quality: scripts/ui/UI.gd: parse error (Control.SIDE_* does not exist in Godot 4.4) prevents UI.gd from loading; harness cannot exercise the checkpoint (see .gen/quality-notes.md)
- The Place Exit button still displays its price text without clipping or truncation after the relayout.
- Each of the three controls keeps its existing behaviour after the relayout: Carve arms carve mode, Place Block arms block placement, and Place Exit places an exit when pressed.
- The underground controls appear only while the game is on the underground layer and hide again when returning to the surface layer.

## ❌ Impossible
