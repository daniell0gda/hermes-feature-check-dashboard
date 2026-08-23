# Cluster 2: venom-barbs-trap-poison-runtime

- owned file scope: `scripts/game/actors/Trap.gd`
- dependencies: 1
- parallel: false

## Acceptance criteria

- When `traps_venom_barbs` is owned, a trap hit applies lingering poison to the hit enemy through the enemy's existing `EffectsManager.apply_poison` path (a `PoisonStatus` appears on that enemy).
- When `traps_venom_barbs` is not owned, a trap hit applies no poison: no `PoisonStatus` is created on the hit enemy.
- Poison applied by a trap uses the Venom baseline timing: the same total-duration and tick interval a base Venom hit uses, at every perk level.
- Poison delivered by a trap keeps dealing damage after the trap hit, with HP decreasing across poison ticks during the poison duration without any further trap hits.
- Trap-sourced poison does not activate the Venom compounding-stack behavior even when the Venom stacking perk is active: repeated trap hits refresh rather than compound the stack.
- A debug-build log line with a stable `[VENOM-BARBS]` marker records each trap-sourced poison application (enemy id, trap id, level, poison total, duration, tick interval).
- A trap-sourced poison shows the same reused poison visual feedback (existing poison cloud/status VFX from `apply_poison`) that a Venom-applied poison shows, while unowned trap hits show none.

## Verification commands

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_venom_barbs_trap_poison.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/traps_venom_barbs_trap_poison.json"]` plus the same token array re-run for each of: `traps_venom_barbs_progression.json`, `undermining_trap_armor.json`, `traps_serrated_edges_progression.json`, `enemy_armor_trap.json`, `trap_stats_attribution.json`, `progression_pick.json` — each run must end `[Harness] status=pass exit=0`.
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
