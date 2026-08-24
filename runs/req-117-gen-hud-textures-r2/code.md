# Coder report: remove-dead-hud-texture-generator\n\n# Coder report: remove-dead-hud-texture-generator

## Changed files
- `tools/gen_hud_textures.py` — deleted (dead generator; sources `woden_panel*.jpg` unrecoverable)
- `textures/ui/hud/wood_slot.png`, `slot_empty.png`, `wood_panel_wide.png`, `wood_panel_wide_dark.png` — deleted (unreferenced outputs; no `.import` files existed for them)
- `tools/gen_hud_icons.py` — docstring updated: no longer claims `icon_coin`/`icon_heart` come from `gen_hud_textures.py`
- `tools/check_hud_asset_refs.py` — new repo-local checker: fails non-zero if any `tools/*.py` references a missing `textures/_source/...` file or any `.tscn`/`.tres`/`.gd` references a missing `textures/ui/hud/*.png`

## Criteria
- No tools/*.py references missing `_source` inputs — Done (`check_hud_asset_refs.py` OK)
- `tools/gen_hud_textures.py` gone — Done
- `icon_coin.png`, `icon_heart.png`, `towers_panel.png` byte-identical — Done (`md5sum -c` OK vs pre-change hashes f7f27e0b…, 363528e9…, 781cdec1…)
- No dangling hud texture references from scenes/themes/scripts — Done
- Unreferenced generator outputs deleted (incl. wood_slot, slot_empty) — Done
- `gen_hud_icons.py` docstring fixed — Done
- Godot headless import + editor runs clean — Done

## Commands and results
- `python3 tools/check_hud_asset_refs.py` via run_project_cmd — exit 0; "OK: all tool _source references resolve; all referenced hud textures exist"
- `godot --headless --path . --import --quit-after 300` — exit 0
- `godot --headless --path . --editor --quit-after 300` — exit 0

## Notes
- The four deleted PNGs had no `.import` sidecars on disk, so none needed removal.
- Pre-existing warnings during import: HudTheme.tres ext_resources carry stale UIDs and fall back to text paths for textures that still exist. Not introduced by this change; left untouched.
- Deleted files were never referenced by any `.tscn`/`.tres`/`.gd`; rendered HUD pixels unchanged. manual_testing: none.
- Untracked scratch dir `.gen-blocked-117-gen-hud-20260823-attempt1/` predates this run and was not touched.
\n