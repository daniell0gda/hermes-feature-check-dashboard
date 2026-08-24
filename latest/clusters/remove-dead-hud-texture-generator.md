# Cluster 1: remove-dead-hud-texture-generator

- Owned file scope: `tools/gen_hud_textures.py`, `textures/ui/hud/wood_slot.png`, `textures/ui/hud/slot_empty.png`, `textures/ui/hud/wood_panel_wide.png`, `textures/ui/hud/wood_panel_wide_dark.png` (plus their `.import` files if present), `tools/gen_hud_icons.py` (docstring only), `tools/check_hud_asset_refs.py`
- Dependencies: none
- parallel: false

## Acceptance criteria

- The repository contains no `tools/*.py` script whose input sources are missing under `textures/_source/`; after the change a reference check over all tool scripts reports zero references to nonexistent source paths.
- `tools/gen_hud_textures.py` no longer exists in the working tree.
- `textures/ui/hud/icon_coin.png`, `icon_heart.png`, and `towers_panel.png` still exist and remain byte-identical to their committed versions at the starting revision.
- Every remaining file under `textures/ui/hud/` that is referenced by any scene, theme, or script resource resolves to an existing file (no dangling texture references after deletion).
- Files under `textures/ui/hud/` written only by the deleted generator and not referenced by any scene, theme, or script (including `wood_slot.png` and `slot_empty.png`) no longer exist in the working tree.
- The docstring of `tools/gen_hud_icons.py` no longer states that `icon_coin` or `icon_heart` come from `gen_hud_textures.py`.
- A Godot headless editor/import run over the project completes without errors introduced by the removed textures.

## Verification commands

```json
{"project":"godot-td","workspace":"poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three","cmd":["python3","tools/check_hud_asset_refs.py"]}
{"project":"godot-td","workspace":"poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three","cmd":["godot","--headless","--path",".","--import","--quit-after","300"]}
{"project":"godot-td","workspace":"poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
```

## Notes

- Do NOT delete or modify `icon_coin.png`, `icon_heart.png`, or `towers_panel.png` — they are live references in `PricedButton.tscn`, `UI.tscn`, and `HudTheme.tres`.
- Worker image may lack Pillow; committed PNGs must stay byte-identical, so no regeneration.
- No debug logging criterion: this change removes dead code; no new runtime state transitions are introduced.
