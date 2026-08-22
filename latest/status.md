## ✅ Done
- The `frozen_fracture` perk exists in the global Common progression pool as type Common with exactly 3 levels.
- With `frozen_fracture` at levels 1/2/3, the exposed armor-damage bonus is 10%/20%/30% respectively; when the perk is not owned the bonus is disabled (no increase).
- A chest draw that includes the pool offers `frozen_fracture` alongside other Common perks, and its level descriptions state the armor-damage bonus values.
- While an enemy is under an active Ice Tower slow, each point of incoming armor damage from any damage source is increased by the perk's level bonus (10%/20%/30%).
- An enemy that is not currently slowed takes unmodified armor damage even when `frozen_fracture` is owned.
- After an enemy's Ice slow expires, subsequent hits take unmodified armor damage again.
- The bonus scales armor damage only; the hit's HP damage is unchanged by the perk.
- A debug-build log line with a stable filterable `[FrozenFracture]` marker records each boosted armor-damage application (enemy id, level, bonus percent).
- A focused game-test scenario compares armor remaining after identical armor-damage hits on a slowed versus an unslowed enemy with the perk active, asserting the slowed enemy lost exactly the level-multiplied amount more.
- A focused game-test scenario asserts the three perk levels produce 10%/20%/30% extra armor damage respectively and that behavior reverts to baseline once the slow expires.

## ⬜ Pending

## ❌ Impossible
