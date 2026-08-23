## ✅ Done
- The `traps_venom_barbs` progression exists in the trap progression pool as a Unique perk and offers exactly 3 levels.
- With `traps_venom_barbs` at levels 1/2/3, the exposed poison total per trap hit scales strictly by level (level N deals more poison damage than level N−1), and reads as disabled (zero poison) when the perk is not owned.
- Taking a different trap perk (Serrated Edges or Undermining) does not enable trap poison, and owning `traps_venom_barbs` does not change trap hit damage or armor strip values.
- When `traps_venom_barbs` is owned, a trap hit applies lingering poison to the hit enemy through the enemy's existing `EffectsManager.apply_poison` path (a `PoisonStatus` appears on that enemy).
- When `traps_venom_barbs` is not owned, a trap hit applies no poison: no `PoisonStatus` is created on the hit enemy.
- Poison applied by a trap uses the Venom baseline timing: the same total-duration and tick interval a base Venom hit uses, at every perk level.
- Poison delivered by a trap keeps dealing damage after the trap hit, with HP decreasing across poison ticks during the poison duration without any further trap hits.
- Trap-sourced poison does not activate the Venom compounding-stack behavior even when the Venom stacking perk is active: repeated trap hits refresh rather than compound the stack.
- A debug-build log line with a stable `[VENOM-BARBS]` marker records each trap-sourced poison application (enemy id, trap id, level, poison total, duration, tick interval).
- A trap-sourced poison shows the same reused poison visual feedback (existing poison cloud/status VFX from `apply_poison`) that a Venom-applied poison shows, while unowned trap hits show none.
- A focused headless harness scenario proves the perk definition and level scaling: unowned yields no trap poison, and levels 1/2/3 yield strictly increasing poison totals through the exposed getter.
- A focused headless harness scenario proves live trap-hit poison: a scripted `Trap.perform_hit` on an underground enemy with the perk owned creates a `PoisonStatus`, deals lingering damage over time after the hit, and leaves the enemy's HP lower than an unowned-perk equivalent hit sequence; it also asserts the pre-existing trap hit damage and Undermining armor-strip behavior are unchanged alongside the new poison.
- The pre-existing `progression_pick` chest-draw scenario passes again: with `traps_venom_barbs` added to the chest-eligible pool, the scenario's exhaustive-grant setup accounts for it so the forced 2-card chest offer still contains `venom_miasma_bloom` and the auto-answered pick applies it (level reads 1 and miasma config enabled at scenario end).

## ⬜ Pending
- (none)

## ❌ Impossible
- (none)
