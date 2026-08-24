# Request

- request_id: req-85-tooltip-polish-r2
- issue: https://github.com/daniell0gda/poke-defense-godot/issues/85
- project runner key: `godot-td`
- git workspace: `poke-defense-godot/issue-update-tower-descriptions-with-special-c`
- branch: `issue/update-tower-descriptions-with-special-c`
- worktree: `/workspace/git-workspaces/poke-defense-godot/issue-update-tower-descriptions-with-special-c`
- base: already rebased onto `origin/master` (42e05d6) before this run; do not reset the branch
- prior pass: `.gen-r1-pass-20260823/` — description **text** is done; Daniel rejected the **look**

## Feature

The special-characteristic lines already exist and work. Daniel: **"it works but it looks ugly, make it look professional and integrated with the app."**

Replace the Godot default `tooltip_text` popup (plain black box, default font, dumped newlines, covers the shop bar) with a custom shop preview card that matches the existing wood HUD — same family as the right-side tower details (`UpgPanel`: `TitledPanel` + `SidePanel` frame + `ModalWell` + `ModalTitle` + HUD theme fonts).

## What is already in the tree (keep)

- `data/towers.xml` `description=` on all 12 combat towers (Porter explicitly teleports / no damage).
- `TowersConfig.get_description()`.
- `UI._build_tower_tooltip` currently just appends that string into default tooltip text — that presentation is the bug.

Historical r1 reports/screenshots painted a fake black overlay because default tooltips do not render without a real mouse. Those shots are **not** the target look. Do not reuse annotated overlays.

## Required look

Build a real Control (not `tooltip_text`, not a debug ColorRect):

1. Wood HUD card, not a black slab. Reuse existing theme variations: `SidePanel` or `WidePanel` / `WoodPanel`, inner `ModalWell`, title `ModalTitle`. Same fonts/colors as tower details and the Towers bar. No new art style.
2. Structure, not a text dump:
   - Tower name as the title
   - Special-characteristic description as a wrapping body line (readable contrast; not tiny default gold-on-black)
   - Cost / Range / Damage (and other live stats) as HUD stat rows or cost badges — same language as `TowerStatRow` / `CostBadge`, not `"Cost: 20 | Range: 6.5"` jammed on one line
3. Placement: sit **above the hovered shop slot** (or a compact card that does not swallow the Towers bar). Must not cover the whole shop strip. Must not collide with the right-side tower details when both could show.
4. Deduplicate Porter/Floodgate: one teleport/flood explanation, not the XML line plus the old hardcoded extra lines.
5. Hide Godot's default tooltip on shop slots (`tooltip_text = ""` once the card owns the copy). Other buttons may keep default tooltips.
6. Trigger on shop-slot hover (`mouse_entered` / `mouse_exited`) **and** expose a public method the harness can call, e.g. `UI.show_tower_shop_preview(tower_id)` / `hide_tower_shop_preview()`, because the harness cannot drive OS hover. Windowed screenshots must show the **real** card with readable description text in the pixels.

## Acceptance criteria

- All 12 combat-tower special descriptions remain (Porter still says it teleports enemies and deals no damage).
- Hovering a shop slot shows the new wood card; leaving hides it.
- Card looks like part of this game (wood frame, HUD type, wrapping body, stat rows). `ui_feels_broken: yes` fails the run even if strings match.
- Windowed shots of Generic, Fire, Ice, and Porter with the **real** card visible and the special line readable. No painted overlays. No headless for manual tester.
- Existing tooltip string consumers (`_build_tower_tooltip` used by perk scenarios) still expose the same facts (Porter teleport, cost/range/damage, perk lines). Prefer one content builder the card and any harness string-read share.
- Editor/import gate clean on changed scenes. Focused shop-preview harness pass.

## Verification notes

- Runner: `godot-td` + workspace `poke-defense-godot/issue-update-tower-descriptions-with-special-c` only. Never invent `godot-td/issue-85`.
- `manual_testing: required`. Windowed only. Vision-read the PNGs: if the card is missing, the shot fails.
- Do not close, merge, push, or commit unless asked.

## Out of scope

- Rewriting description copy except to drop duplicate Porter/Floodgate lines.
- Changing tower combat behavior.
- Restyling Options / Manage Towers / ProgressionModal.
