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

- Focused test: `["bash", "-lc", "for f in tests/scenarios/traps_venom_barbs_progression.json tests/scenarios/traps_venom_barbs_trap_poison.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Full test: `["bash", "-lc", "for f in tests/scenarios/traps_venom_barbs_progression.json tests/scenarios/traps_venom_barbs_trap_poison.json tests/scenarios/undermining_trap_armor.json tests/scenarios/traps_serrated_edges_progression.json tests/scenarios/enemy_armor_trap.json tests/scenarios/trap_stats_attribution.json tests/scenarios/progression_pick.json; do id=$(basename \"$f\" .json); godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://$f\" > /dev/null 2>&1; s=$(python3 -c \"import json;print(json.load(open('.gen/harness/$id/result.json'))['status'])\" 2>/dev/null); echo \"$id: $s\"; [ \"$s\" = pass ] || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--editor", "--path", ".", "--quit-after", "120"]`
