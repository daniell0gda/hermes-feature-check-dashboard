# Cluster 01 — authoritative encounter clear and exit/victory gates

- `parallel: false`
- Depends on: none.
- Downstream: cluster 02 requires the final query/signals and any exposed state; cluster 03 verifies the resulting behavior.
- Exclusive ownership (create/modify):
  - `scripts/game/Game.gd`
  - `scripts/game/SpawnerSystem.gd`
  - `scripts/game/UndergroundSystem.gd`
  - `scripts/game/CaveSystem.gd` only if required to expose an existing native-enemy/spawner query
  - `scripts/game/systems/ExitRemovalSystem.gd`
  - `scripts/game/placement/ExitPlacementModule.gd` only if the existing removal call needs to consume the guard
- Forbidden overlap: do not edit `tests/scenarios`, test scripts, AgentHarness, UI, maps, balance, or unrelated enemy/tower code; cluster 02 owns scenario/harness evidence files.

## Implementation steps
1. Trace all existing registrations/removals for `Spawner.alive`, queue entries, cave spawners, native CaveSystem enemies, and underground scene enemies. Preserve instance de-duplication and invalid/dead filtering.
2. Add the smallest typed encounter-clear API at the gameplay owner boundary. It must report (or make queryable) pending queue/spawner state and all live entities from both surface and underground sources, including dynamically discovered cave spawners after the final wave. Avoid kill-count-only inference.
3. Make `_handle_wave_completion` gate victory on both `current_wave >= total_waves` and the authoritative full-clear predicate. A final-wave all-clear signal must not be treated as victory while late discovery can still register a spawner/enemy; re-evaluate at the actual trigger point.
4. Change `ExitRemovalSystem.can_remove_exit`/the placement path to reject removal, disabling, or inaccessible-state transitions while any underground enemy, underground spawner queue/activity, or boss remains. Keep removal atomic: only remove data and visuals after the predicate is true and retain the exit while blocked.
5. Preserve intended map flow: after full encounter clear, the existing exit removal path remains available and victory is emitted exactly once. Keep legacy signal compatibility and add debug-only tagged transition logging for blocked/allowed decisions per repository instructions.
6. Expose only the minimum stable state needed by AgentHarness (for example, typed clear/exit-presence queries or a debug read-only value), without test-only bypasses in production logic.

## Acceptance mapping
- AC1: victory/show-victory is impossible until all waves and all spawned/active enemies, underground enemies, spawners, and bosses are clear.
- AC2: a cave/enemy discovered after the final wave changes the authoritative query and prevents victory until defeated.
- AC3: an underground exit remains present and accessible while underground enemy/spawner/boss work remains.
- AC4: removal is permitted only after the full encounter clear and follows existing map flow.
- AC5 dependency: cluster 02 can drive/assert premature victory and exit persistence, including a spawner/boss case.

## Risks / decisions
- `SpawnerSystem._check_all_clear` currently only examines active spawner queue/alive arrays; do not broaden victory by blindly trusting its signal.
- `UndergroundSystem.get_all_underground_enemies()` already combines three sources; reuse it, but verify CaveSystem and SpawnerSystem references are initialized and late-added cave spawners are visible.
- Avoid queue-free timing races: an object is not clear merely because its death animation began; use the existing validity/dead semantics consistently.
- Do not permanently disable exit interaction after full clear or remove the exit as part of the first all-clear signal.

## Handoff evidence
Document changed API names, the exact entity sources included, and log/result fields that cluster 02 can assert. Cluster 01 must not claim the issue complete without the focused regression run.
