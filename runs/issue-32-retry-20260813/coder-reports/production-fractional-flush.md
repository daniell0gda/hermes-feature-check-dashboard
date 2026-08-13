# Cluster 1 coder report — production fractional-damage retirement flush

## Outcome
Implemented the production flush API and wired it to the three owned retirement paths. `Enemy.hp` remains an `int`; ordinary damage still accumulates with `floor()` and applies whole points immediately.

## Changed files
- `scripts/game/actors/Enemy.gd`
  - Added `flush_pending_damage()` forwarding method.
  - Documented that pending records retain attribution.
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd`
  - Pending per-instance values are now dictionaries containing `amount` and `tower_type_id`.
  - Preserves the latest non-empty tower type for an instance while retaining the existing instance key and trap `-1` behavior.
  - Added an idempotent `flush_pending_damage()` helper. It snapshots and clears the buffer before processing, uses `int(round(...))`, records attributed positive residuals via `StatsManager.record_damage`, and never changes HP.
  - Death calls the helper after marking the enemy dead and before kill/retirement bookkeeping, so late stats are recorded without re-entering damage/death handling.
- `scripts/game/actors/enemy/parts/EnemyMovementController.gd`
  - Flushes before cave-consumption notification, queue-free, and surface egg damage.
- `scripts/game/actors/enemy/parts/EnemySuctionController.gd`
  - Flushes before tube exit notification, queue-free, and suction egg damage.

No forbidden files were modified.

## Design decisions
- The existing key remains the tower instance ID, with `-1` for non-instance/trap attribution, so instance attribution is not merged across attackers.
- A non-empty incoming `tower_type_id` updates the pending record's stored type. An unattributed follow-up hit does not erase an already stored attributed type.
- The flush clears all pending records before iterating. A second flush is therefore a no-op, including after a rounded-zero residual.
- Rounded-zero residuals are intentionally not sent as zero stats events, matching existing whole-point recording behavior; they are still cleared.
- The death flush has no HP-subtraction option because retirement residuals are telemetry recovery only. Existing integer HP, kill credit, reward, cave consumption, and egg damage paths remain otherwise unchanged.

## Runner verification

Exact runner payload 1:
```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--version"]}
```
Result: exit code `0`, timed out `false`, duration `84 ms`, output `4.4.1.stable.official.49a5bc7b6`.

Exact runner payload 2:
```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
```
Result: exit code `0`, timed out `false`, duration `46364 ms`. Full combined output was persisted by the runner at `/tmp/hermes-results/call_HiVJZkxGUIM9JXDFBd2PFGAg.txt`.

The import/editor gate completed and did not report an error in any of the four changed scripts. The full output did contain one unrelated pre-existing parse diagnostic:
`SCRIPT ERROR: Parse Error: Function "get_process_frame()" not found in base self.` at `res://debug_enemy_parsing.gd:7`, followed by failure to load that unrelated debug script. This is not part of the owned change.

No focused scenario was run because Cluster 2 owns and had not yet added `tests/scenarios/issue_32_fractional_damage_retirement.json`.

## Git verification
- `git status --short` showed only the four owned production files at verification time before this report was added.
- `git diff --check`: passed with no output.
- `git diff --stat`: 4 files, 30 insertions, 4 deletions.

## Unresolved risks
- Runtime focused coverage for exact death, surface-arrival, and suction idempotence is deferred to Cluster 2's scenario/evidence work.
- The editor gate has unrelated existing `debug_enemy_parsing.gd` parse noise; changed-file parse status was clean in the gate output.
- The ExitTube fallback is for bodies without `begin_suction`; normal `Enemy` instances use the owned suction controller path, so no forbidden `ExitTube.gd` change was made.
