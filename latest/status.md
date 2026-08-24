## ✅ Done
- Calling the public preview API (`show_tower_shop_preview(tower_id)` / `hide_tower_shop_preview()`) with an unlocked combat-tower id shows a wood HUD preview card styled with the existing theme family (`SidePanel`/`WidePanel`/`WoodPanel` frame, inner `ModalWell`, `ModalTitle` title, HUD theme fonts), not Godot's default black tooltip popup.
- The card shows the tower display name as its title, the special-characteristic description as a wrapping body line with readable contrast against the wood panel, and Cost / Range / Damage (plus fire rate where applicable) as distinct HUD stat rows or cost badges in the same visual language as `TowerStatRow` / `CostBadge` — never one jammed "Cost: X | Range: Y" text line.
- Calling `hide_tower_shop_preview()` hides the card completely.
- Connecting to an existing shop slot's `mouse_entered` shows the same card and `mouse_exited` hides it, matching the public-API behaviour.
- Unlocked shop slots no longer set Godot's default `tooltip_text` to the built string (it is empty so no default popup can appear); locked slots keep their lock-reason tooltip and non-shop buttons keep their existing tooltips unchanged.
- The card content and `_build_tower_tooltip` share one content source: every fact shown on the card (name, description, cost, range, damage, perk lines) equals what `_build_tower_tooltip(tower_id)` returns, and the Porter card shows exactly one teleport/no-damage explanation with no duplicated hardcoded extra line (Floodgate likewise shows one flood explanation).
- All 12 combat towers keep their special description from `data/towers.xml`; Porter's still explicitly states it teleports enemies to the underground tunnels and deals no damage itself.
- Debug-build [SHOP-PREVIEW] log line per card show and per card hide event, naming the tower id.
- A focused harness scenario drives `show_tower_shop_preview` for representative towers (including Porter), asserts the shared content facts through the public API/content builder, and ends `status=pass`.
- Fresh editor/import gate run over the workspace exits 0 with no parse/resource/script diagnostics on the changed scenes and scripts.

## ⬜ Pending
- The card sits above the hovered shop slot without covering the entire Towers bar and does not overlap the right-side tower details panel when both are visible at once. — pending manual windowed screenshot pass (`.gen/manual-report.md` absent; headless geometry half verified green)

## ❌ Impossible
(none)
