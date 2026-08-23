# check.md — gen-hud-textures-py-cannot-run-all-three (issue #117)

classification: pass

## Verdict

Implementation chose option 2 (delete dead script + dead outputs) after proving
`git log --all -- '*woden_panel*'` is empty — the three crate JPGs were never
committed, so option 1 (restore) is impossible. Both acceptance criteria hold.

## Verification performed (fresh, this run)

| Check | Command | Result |
|---|---|---|
| Runner preflight | `run_project_cmd` `["python3","--version"]`, project godot-td | exit 0, Python 3.10.12 |
| No tools script references missing `_source` sources | `run_project_cmd` python scan of `tools/*.py` for `_source` refs vs filesystem | exit 0; only `prep_hud_assets.py` / `cut_towers_bar_assets.py` reference `_source`, and all their source files exist under `textures/_source/`. `gen_hud_textures.py` is deleted |
| Editor/import gate (textures changed: deletions) | `run_project_cmd` `["godot","--headless","--path",".","--editor","--quit-after","300"]` | exit 0, 9.2s warm run; import scan of all assets completed, no errors related to hud/wood/slot/theme/deleted files |
| Dead outputs unreferenced | host grep over scenes/themes/scripts/tests/systems for wood_slot, slot_empty, wood_panel_wide*, wood_chip | only remaining mention is the intentional historical note in `tools/gen_hud_icons.py` docstring |
| Live assets referenced | file inspection | `icon_coin.png` → `scenes/ui/widgets/PricedButton.tscn`; `icon_heart.png` → `scenes/UI.tscn`; `towers_panel.png` → `themes/hud/HudTheme.tres`; plus wood_panel/wood_button*/btn_chip/slot_frame in HudTheme.tres |
| Crate JPG recoverability | `git log --all -- '*woden_panel*'` | 0 commits — confirms option-1 impossibility |

## Evidence notes

- Coder-reported runs of `prep_hud_assets.py`, `gen_hud_icons.py`,
  `cut_towers_bar_assets.py` (exit 0 each, with ephemeral Pillow bootstrap)
  could not be byte-reproduced in this pass because the worker image has no
  pip/Pillow; this is an environment limitation only. It does not affect the
  acceptance criteria: the scripts' sources all exist, and committed player-
  facing PNGs were intentionally left unchanged (`git checkout` restored them),
  which the diff confirms (`tools/gen_hud_icons.py` docstring-only modification
  plus staged deletions).
- Manual testing: none required — no committed player-facing art changed;
  request.md explicitly allows `manual_testing: none` for this case.
- Pre-existing unrelated issues (not introduced by this change, recorded for
  awareness): parse error in `res://debug_enemy_parsing.gd`
  (`get_process_frame()`), glTF/FBX import failures on Venom/trap assets.

## Changed-file quality findings

- `tools/gen_hud_icons.py`: docstring-only correction removing a stale claim.
  Clean, surgical, matches coding rules. No violations.
- Deletions are minimal and trace directly to the issue. No scope creep.

## Blockers

None.

## Unverified items

- Byte-level regeneration of the PIL-based tool scripts was not repeated here
  (worker image lacks Pillow); covered by coder evidence + unchanged-diff proof.
