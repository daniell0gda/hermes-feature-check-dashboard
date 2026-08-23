## ✅ Done
- tools/ contains no script whose sources are missing — `gen_hud_textures.py` deleted; scan of `tools/*.py` confirms every remaining `_source` reference resolves to an existing file (exit 0)
- Still-used HUD outputs exist and are referenced — `icon_coin.png` → PricedButton.tscn, `icon_heart.png` → UI.tscn, `towers_panel.png` → HudTheme.tres; committed PNGs unchanged; editor/import gate `godot --headless --path . --editor --quit-after 300` exit 0

## ⬜ Pending

## ❌ Impossible
- Restore crate JPGs to `textures/_source/` — `git log --all -- '*woden_panel*'` is empty: the three JPGs were never committed in any reachable history, so option 1 cannot be satisfied (option 2 was implemented instead)
