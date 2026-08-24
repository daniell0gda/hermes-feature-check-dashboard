# Acceptance Plan: req-85-tooltip-polish-r2 — custom shop preview card

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/tower_shop_preview_card.json"]`
- Full test: `python3 tests/run_all_shard.py 0 1`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

All runner invocations go through `run_project_cmd` with project `godot-td`, workspace `poke-defense-godot/issue-update-tower-descriptions-with-special-c`. The manual windowed screenshot pass (Generic / Fire / Ice / Porter) is NOT headless-run; see `manual_testing`.

manual_testing: required

## Clusters

1. shop-preview-card-ui — files: `scripts/ui/UI.gd`, `scripts/ui/hud/TowerShopSlot.gd`, `scripts/ui/hud/TowerStatRow.gd`, new HUD card script/scene under `scripts/ui/hud/` + `scenes/ui/hud/`, `data/towers.xml` — depends on: none
- Calling the public preview API (`show_tower_shop_preview(tower_id)` / `hide_tower_shop_preview()`) with an unlocked combat-tower id shows a wood HUD preview card styled with the existing theme family (`SidePanel`/`WidePanel`/`WoodPanel` frame, inner `ModalWell`, `ModalTitle` title, HUD theme fonts), not Godot's default black tooltip popup.
- The card shows the tower display name as its title, the special-characteristic description as a wrapping body line with readable contrast against the wood panel, and Cost / Range / Damage (plus fire rate where applicable) as distinct HUD stat rows or cost badges in the same visual language as `TowerStatRow` / `CostBadge` — never one jammed "Cost: X | Range: Y" text line.
- The card sits above the hovered shop slot without covering the entire Towers bar and does not overlap the right-side tower details panel when both are visible at once.
- Calling `hide_tower_shop_preview()` hides the card completely.
- Connecting to an existing shop slot's `mouse_entered` shows the same card and `mouse_exited` hides it, matching the public-API behaviour.
- Unlocked shop slots no longer set Godot's default `tooltip_text` to the built string (it is empty so no default popup can appear); locked slots keep their lock-reason tooltip and non-shop buttons keep their existing tooltips unchanged.
- The card content and `_build_tower_tooltip` share one content source: every fact shown on the card (name, description, cost, range, damage, perk lines) equals what `_build_tower_tooltip(tower_id)` returns, and the Porter card shows exactly one teleport/no-damage explanation with no duplicated hardcoded extra line (Floodgate likewise shows one flood explanation).
- All 12 combat towers keep their special description from `data/towers.xml`; Porter's still explicitly states it teleports enemies to the underground tunnels and deals no damage itself.
- Debug-build [SHOP-PREVIEW] log line per card show and per card hide event, naming the tower id.

## Criteria

- Calling the public preview API (`show_tower_shop_preview(tower_id)` / `hide_tower_shop_preview()`) with an unlocked combat-tower id shows a wood HUD preview card styled with the existing theme family (`SidePanel`/`WidePanel`/`WoodPanel` frame, inner `ModalWell`, `ModalTitle` title, HUD theme fonts), not Godot's default black tooltip popup.
- The card shows the tower display name as its title, the special-characteristic description as a wrapping body line with readable contrast against the wood panel, and Cost / Range / Damage (plus fire rate where applicable) as distinct HUD stat rows or cost badges in the same visual language as `TowerStatRow` / `CostBadge` — never one jammed "Cost: X | Range: Y" text line.
- The card sits above the hovered shop slot without covering the entire Towers bar and does not overlap the right-side tower details panel when both are visible at once.
- Calling `hide_tower_shop_preview()` hides the card completely.
- Connecting to an existing shop slot's `mouse_entered` shows the same card and `mouse_exited` hides it, matching the public-API behaviour.
- Unlocked shop slots no longer set Godot's default `tooltip_text` to the built string (it is empty so no default popup can appear); locked slots keep their lock-reason tooltip and non-shop buttons keep their existing tooltips unchanged.
- The card content and `_build_tower_tooltip` share one content source: every fact shown on the card (name, description, cost, range, damage, perk lines) equals what `_build_tower_tooltip(tower_id)` returns, and the Porter card shows exactly one teleport/no-damage explanation with no duplicated hardcoded extra line (Floodgate likewise shows one flood explanation).
- All 12 combat towers keep their special description from `data/towers.xml`; Porter's still explicitly states it teleports enemies to the underground tunnels and deals no damage itself.
- Debug-build [SHOP-PREVIEW] log line per card show and per card hide event, naming the tower id.
- A focused harness scenario drives `show_tower_shop_preview` for representative towers (including Porter), asserts the shared content facts through the public API/content builder, and ends `status=pass`.
- Fresh editor/import gate run over the workspace exits 0 with no parse/resource/script diagnostics on the changed scenes and scripts.

## Notes

- Prior r1 screenshots with painted overlays are invalid evidence; the manual tester must capture real windowed shots of Generic, Fire, Ice, and Porter with the actual card pixels visible and the special line legible (vision-read the PNGs; missing card = fail). See `.gen/ui_scenario.md`.
- Out of scope: rewriting description copy beyond dropping duplicate Porter/Floodgate hardcoded extras, tower combat behaviour, restyling Options / Manage Towers / ProgressionModal.
