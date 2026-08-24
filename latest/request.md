# Request: gen-hud-textures-py-cannot-run-all-three (r2)

**Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/117
**Project runner:** `godot-td`
**Workspace:** `poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three`
**Branch:** `issue/gen-hud-textures-py-cannot-run-all-three`
**Request id:** `req-117-gen-hud-textures-r2`
**Starting revision:** `9d54964` (`origin/master` after hard reset)

## Why this is a fresh run

Daniel: "Rerun from the beginning, plan wasn't produced."

The previous run (`117-gen-hud-20260823`) coded and checked **without** writing `.gen/plan.md` or `.gen/clusters/*.md`. That attempt is archived at:

`.gen-blocked-117-gen-hud-20260823-attempt1/`

Historical commit `d06b5de` (`fix: drop broken gen_hud_textures.py and unused crate-derived HUD slices`) is **unverified reference only**. The worktree is reset to `origin/master`. Do **not** treat the archived check as done. Do **not** skip the planner.

## Hard planner gate

The planner **must** write:

- `.gen/plan.md`
- one or more `.gen/clusters/<id>.md` with exclusive file ownership, `parallel: true|false`, dependencies, acceptance criteria

Do not implement until those artifacts exist. Do not invent nested plan state.

## Plain language

The HUD crate-texture generator cannot run because its three source JPGs are gone. Either restore those sources so the script works, or delete the dead script and keep the still-used HUD icons.

## Problem

`tools/gen_hud_textures.py` (572 lines) cannot run. Its three sources are absent from the repo and from disk:

- `textures/_source/woden_panel.jpg`
- `textures/_source/woden_panel_wide.jpg`
- `textures/_source/woden_panel_wide_darkonly.jpg`

Current wood-panel redesign uses `panel-square.png` / `panel-wide.png` via `tools/prep_hud_assets.py`.

Still-used outputs under `textures/ui/hud/` (keep these unless you re-derive them):

- `icon_coin.png` — `scenes/ui/widgets/PricedButton.tscn`
- `towers_panel.png` — `themes/hud/HudTheme.tres`
- `icon_heart.png` — `scenes/UI.tscn`
- `wood_slot.png`, `slot_empty.png` (script also writes these; confirm live references before deleting)

The script already documents that the JPGs are missing (comment at lines 16–20). That comment is not a fix.

## Acceptance

One of:

1. Restore the crate JPGs to `textures/_source/` and `gen_hud_textures.py` runs clean, or
2. Re-derive still-used outputs from current sources via `prep_hud_assets.py` **or** keep the committed live PNGs unchanged, then delete `gen_hud_textures.py` plus **unreferenced** dead outputs.

Either way `tools/` must contain no script whose sources are missing.

Prefer option 2 if `git log --all -- '*woden_panel*'` is empty. Option 1 is valid only if the JPGs can actually be recovered.

If option 2: do **not** delete `icon_coin.png`, `icon_heart.png`, or `towers_panel.png`. Only delete outputs that are unused by scenes/themes/scripts.

`tools/gen_hud_icons.py` still claims `icon_coin` / `icon_heart` come from `gen_hud_textures.py` — update that docstring if the generator is deleted.

## Verification (runner only)

Project `godot-td`, workspace `poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three`.

Tokenized `run_project_cmd` examples:

```json
{"project":"godot-td","workspace":"poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three","cmd":["python3","--version"]}
{"project":"godot-td","workspace":"poke-defense-godot/issue-gen-hud-textures-py-cannot-run-all-three","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
```

- Prove no remaining `tools/*.py` references missing `_source` files.
- Prove live HUD assets still exist and are referenced.
- Editor/import gate if textures change.
- Git/worktree ops are Hermes-side, not runner: `git diff --check`, `git status --short`.

## Manual testing

`manual_testing: none` if committed player-facing PNGs are unchanged.
If HUD art look changes, `manual_testing: required` with windowed shots (never `--headless`) plus `ui_feels_broken: yes|no`.

## Redo notes

- Do not invent runner workspace names (`godot-td/issue-117` is wrong).
- Never use host `godot` / `npm` instead of `run_project_cmd`.
- Worker image may lack Pillow; do not require regenerating committed PNGs if they stay byte-identical.
- Dashboard publication is required on the leader path; include the public run URL in the terminal summary.
