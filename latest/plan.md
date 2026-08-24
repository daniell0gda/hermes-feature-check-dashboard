# Acceptance Plan: gen-hud-textures-py-cannot-run-all-three

Resolution path: option 2 (delete the dead generator). `git log --all -- '*woden_panel*'` is
empty on this worktree, so the crate JPGs cannot be recovered from history; the three source
JPGs do not exist on disk either. `gen_hud_textures.py` is therefore unrunnable dead code.
Its still-referenced outputs (`icon_coin.png`, `icon_heart.png`, `towers_panel.png`) stay
committed and byte-identical. Its unreferenced outputs (`wood_slot.png`, `slot_empty.png`,
`wood_panel_wide.png`, `wood_panel_wide_dark.png`) have no references in any `.tscn`, `.tres`,
or `.gd` file and are deleted with the script. `tools/gen_hud_icons.py`'s docstring claim that
`icon_coin`/`icon_heart` come from `gen_hud_textures.py` is updated.

## Verification

- Focused test: `{"project":"godot-td","workspace":"poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three","cmd":["python3","tools/check_hud_asset_refs.py"]}`
  (a repo-local checker added by this feature: asserts no `tools/*.py` mentions missing `_source`
  paths, and every remaining `textures/ui/hud/*.png` referenced by scenes/themes/scripts exists;
  exit non-zero on failure)
- Full test: `{"project":"godot-td","workspace":"poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three","cmd":["godot","--headless","--path",".","--import","--quit-after","300"]}`
- Typecheck/build: `{"project":"godot-td","workspace":"poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}`

## Manual testing

manual_testing: none — no player-facing PNG referenced by scenes/themes is modified; deleted
files were unreferenced, so rendered HUD pixels are unchanged.

## Clusters

1. remove-dead-hud-texture-generator — files: `tools/gen_hud_textures.py`, `textures/ui/hud/wood_slot.png`, `textures/ui/hud/slot_empty.png`, `textures/ui/hud/wood_panel_wide.png`, `textures/ui/hud/wood_panel_wide_dark.png` (+ their `.import` files if present), `tools/gen_hud_icons.py`, `tools/check_hud_asset_refs.py` — depends on: none
- The repository contains no `tools/*.py` script whose input sources are missing under `textures/_source/`; after the change a reference check over all tool scripts reports zero references to nonexistent source paths.
- `tools/gen_hud_textures.py` no longer exists in the working tree.
- `textures/ui/hud/icon_coin.png`, `icon_heart.png`, and `towers_panel.png` still exist and remain byte-identical to their committed versions at the starting revision.
- Every remaining file under `textures/ui/hud/` that is referenced by any scene, theme, or script resource resolves to an existing file (no dangling texture references after deletion).
- Files under `textures/ui/hud/` written only by the deleted generator and not referenced by any scene, theme, or script (including `wood_slot.png` and `slot_empty.png`) no longer exist in the working tree.
- The docstring of `tools/gen_hud_icons.py` no longer states that `icon_coin` or `icon_heart` come from `gen_hud_textures.py`.
- A Godot headless editor/import run over the project completes without errors introduced by the removed textures.

## Criteria

- The repository contains no `tools/*.py` script whose input sources are missing under `textures/_source/`; after the change a reference check over all tool scripts reports zero references to nonexistent source paths.
- `tools/gen_hud_textures.py` no longer exists in the working tree.
- `textures/ui/hud/icon_coin.png`, `icon_heart.png`, and `towers_panel.png` still exist and remain byte-identical to their committed versions at the starting revision.
- Every remaining file under `textures/ui/hud/` that is referenced by any scene, theme, or script resource resolves to an existing file (no dangling texture references after deletion).
- Files under `textures/ui/hud/` written only by the deleted generator and not referenced by any scene, theme, or script (including `wood_slot.png` and `slot_empty.png`) no longer exist in the working tree.
- The docstring of `tools/gen_hud_icons.py` no longer states that `icon_coin` or `icon_heart` come from `gen_hud_textures.py`.
- A Godot headless editor/import run over the project completes without errors introduced by the removed textures.
