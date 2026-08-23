# Cluster 3: venom-barbs-harness-and-regression-coverage

- owned file scope: `tests/scenarios/traps_venom_barbs_progression.json`, `tests/scenarios/traps_venom_barbs_trap_poison.json`, `tests/scenarios/progression_pick.json`
- dependencies: 1, 2
- parallel: false

## Acceptance criteria

- A focused headless harness scenario proves the perk definition and level scaling: unowned yields no trap poison, and levels 1/2/3 yield strictly increasing poison totals through the exposed getter.
- A focused headless harness scenario proves live trap-hit poison: a scripted `Trap.perform_hit` on an underground enemy with the perk owned creates a `PoisonStatus`, deals lingering damage over time after the hit, and leaves the enemy's HP lower than an unowned-perk equivalent hit sequence; it also asserts the pre-existing trap hit damage and Undermining armor-strip behavior are unchanged alongside the new poison.
- The pre-existing `progression_pick` chest-draw scenario passes again: with `traps_venom_barbs` added to the chest-eligible pool, the scenario's exhaustive-grant setup accounts for it so the forced 2-card chest offer still contains `venom_miasma_bloom` and the auto-answered pick applies it (level reads 1 and miasma config enabled at scenario end).

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_venom_barbs_trap_poison.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_pick.json"]` plus the same token array re-run for each of: `traps_venom_barbs_progression.json`, `traps_venom_barbs_trap_poison.json`, `undermining_trap_armor.json`, `traps_serrated_edges_progression.json`, `enemy_armor_trap.json`, `trap_stats_attribution.json` — each run must end `[Harness] status=pass exit=0`.
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
