# Request: gen-hud-textures-py-cannot-run-all-three

**Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/117
**Project runner:** `godot-td`
**Workspace:** `poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three`
**Branch:** `issue/gen-hud-textures-py-cannot-run-all-three`
**Request id:** `117-gen-hud-20260823`

## Plain language

The HUD texture generator cannot run because its three crate JPG sources are gone. Either restore those sources so the script works, or re-derive the still-used HUD icons from the current panel art and delete the dead script.

## Problem

`tools/gen_hud_textures.py` cannot be run: all three of its sources are absent:

- `textures/_source/woden_panel.jpg`
- `textures/_source/woden_panel_wide.jpg`
- `textures/_source/woden_panel_wide_darkonly.jpg`

Current wood-panel redesign uses `panel-square.png` / `panel-wide.png` via `tools/prep_hud_assets.py`.

Still-used outputs under `textures/ui/hud/`:

- `icon_coin.png` — `scenes/ui/widgets/PricedButton.tscn`
- `towers_panel.png` — `themes/hud/HudTheme.tres`
- `icon_heart.png`, `wood_slot.png`, `slot_empty.png`

## Acceptance

One of:

1. Restore the crate JPGs to `textures/_source/` and the script runs clean, or
2. Re-derive still-used outputs from current sources via `prep_hud_assets.py` and delete `gen_hud_textures.py` plus dead outputs.

Either way `tools/` must contain no script whose sources are missing.

Prefer option 2 if current HUD already uses `prep_hud_assets.py` and the crate JPGs cannot be recovered from git history. If history still has the JPGs, restoring them (option 1) is also valid if the script then runs.

## Verification

- Run via runner only: project `godot-td`, workspace `poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three`.
- Prove no remaining `tools/*.py` references missing `_source` files.
- Prove live HUD assets (`icon_coin`, `towers_panel`, hearts/slots) still exist and are referenced after the chosen path.
- Editor/import gate if textures change: `godot --headless --path . --editor --quit-after 300`.
- Manual testing: only if player-facing HUD art actually changes look; if this is a tools/source cleanup with identical committed PNGs, `manual_testing: none` is OK.

## Redo notes

Do not invent runner workspace names. Never use host `godot`/`npm` instead of `run_project_cmd`.
