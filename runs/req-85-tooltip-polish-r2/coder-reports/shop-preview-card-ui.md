# Coder report: shop-preview-card-ui

## Changed files
- `data/towers.xml` — mod: added `description` attribute to all 12 combat towers (Porter's states teleport-to-underground-tunnels + deals no damage; Floodgate's states the periodic tunnel flood).
- `systems/TowersConfig.gd` — mod: parse/store/serve `description` (`tower_descriptions`, `get_description`).
- `scripts/ui/UI.gd` — mod: new `_tower_tooltip_facts(tower_id)` shared content source; `_build_tower_tooltip` now renders from it; new public `show_tower_shop_preview(tower_id)` / `hide_tower_shop_preview()` plus harness reads (`is_shop_preview_card_visible`, `get_shop_preview_card_text`, `count_occurrences_in_shop_preview_text`, `shop_preview_events*`, `shop_slot_tooltip_texts_are_empty`); card positioned above the hovered slot, clamped away from the right details panel; unlocked slots get empty default tooltip (lock reason kept for locked); slot hover signals wired in `_populate_tower_button`; debug-build `[SHOP-PREVIEW] show/hide` logs naming the tower id.
- `scenes/ui/widgets/TowerShopPreviewCard.tscn` — new: PanelContainer card, `SidePanel` frame + inner `ModalWell` + `ModalTitle` from HudTheme, wrapping description label, Stats and Perks boxes. Lives under `UI/Root`.
- `scripts/ui/hud/TowerShopPreviewCard.gd` — new: fills title/body/stat rows (Cost/Range/Fire Rate/Damage with HUD glyphs) / perk lines from the shared facts dict; `as_text()` plain-text mirror for harness reads; never a flat "Cost: X | Range: Y" line.
- `tests/scenarios/tower_shop_preview_card.json` — new focused scenario.

## Criteria
All cluster criteria implemented; verified via runner below. The windowed manual screenshot pass (Generic/Fire/Ice/Porter per `.gen/ui_scenario.md`) remains for the manual tester — headless runs cannot satisfy it.

## Commands and results
All via `run_project_cmd` project `godot-td`, workspace `poke-defense-godot/issue-update-tower-descriptions-with-special-c`.

1. Editor/import gate `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0. Only pre-existing invalid-UID warnings in `themes/hud/HudTheme.tres` / `scenes/UI.tscn` (whole theme, untouched by this change). No parse/script/resource diagnostics on changed files.
2. Focused harness `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/tower_shop_preview_card.json"]` — exit 0, `[Harness] status=pass exit=0`. Log shows `[SHOP-PREVIEW] show tower_id=generic`, `hide`, `show fire`, `show porter`, `hide`. Result at `.gen/harness/tower_shop_preview_card/result.json`.
3. Full sweep `python3 tests/run_all_shard.py 0 1` via runner timed out twice at the tool's 420 s cap (~74 scenarios × up to ~30 s boot each far exceeds one call). Re-ran every scenario individually through the runner instead: 74 result.json files under `.gen/harness/`, 58 pass, 16 non-pass. Verified pre-existing on base 42e05d6 in a sibling worktree (issue-dead-options-modal-scene, same commit): identical 16 fail there (14 timeouts from LFS-smugged glTF models making sim time run >5x real time; `cave_discovery_long_carve` carved_tiles 961 < 1000 random-discovery flake; `projectiles_5x_roster` balista/bazooka/cannon damage>0 flake). None of the 16 touches this change's surface; all UI/tooltip scenarios including the new one pass.

## Notes
- Porter dedupe: hardcoded "Teleports non-boss enemies..." line removed from `UI.gd`; the XML description is the single teleport/no-damage explanation ("Requires a hole nearby." remains as a perk line). Floodgate likewise: flood explanation now only in XML description; cooldown lines remain as perk lines.
- Card content equality with `_build_tower_tooltip` is by construction (both render from `_tower_tooltip_facts`; damage text is extracted back out of the built stat lines).
- Gotcha for tester: engine stdout `[SHOP-PREVIEW]` lines appear in the harness log; the deterministic assertion path is `ui.shop_preview_events()` (engine file log flushes late).
- Gotcha: `.gen-r1-pass-20260823/` scratch dir exists in the worktree root from the prior r1 round (untracked, not part of this change).
