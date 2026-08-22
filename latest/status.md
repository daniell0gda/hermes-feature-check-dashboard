## ✅ Done
- Clicking "Upgrade" in tower details triggers the same animation used for money increase
- Animation visually matches the existing money-increase effect

## ⬜ Pending
- Verified in-game via windowed screenshot — windowed run executed and both screenshot checkpoints report `outcome: captured` (1920x1080 PNGs under `.gen/harness/upgrade_click_money_popup/shots/`), but pixel inspection + vision review of `after_upgrade_click_popup_visible.png` finds no visible floating "+20 coins!" label (tower area occluded by the opened details panel or popup faded/behind geometry at capture time). Fix the scenario so the popup is unambiguously visible in the shot (e.g. capture before `on_tower_selected` reopens the panel, or reposition/deselect), then recapture.
