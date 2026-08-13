# Cluster 1 — production fractional-damage retirement flush

- `parallel: false`
- `depends_on: []`
- `downstream_blockers: Cluster 2 and Cluster 3 cannot proceed until the flush API and retirement wiring are implemented and parse-clean.`
- `exclusive ownership:`
  - `scripts/game/actors/Enemy.gd`
  - `scripts/game/actors/enemy/parts/EnemyHealthController.gd`
  - `scripts/game/actors/enemy/parts/EnemyMovementController.gd`
  - `scripts/game/actors/enemy/parts/EnemySuctionController.gd`
- `forbidden overlap:` do not modify `StatsManager.gd`, IceTower, scenario JSON, AgentHarness, or comparison/evidence files. Do not convert `Enemy.hp` to float. Do not refactor unrelated movement/status code.

## Implementation steps
1. Preserve the existing per-enemy/per-instance accumulator boundary but store a typed pending record containing the residual float and `tower_type_id` (and retain the existing instance key).
2. Add one small idempotent health-controller helper to flush one pending record: calculate `int(round(amount))`, record the rounded amount through `StatsManager.record_damage` with the stored type and instance when attribution is valid, optionally apply HP only when explicitly requested, and clear the record in all cases.
3. Keep ordinary hit behavior (`floor` whole-point application, HP subtraction, health-bar update) unchanged.
4. Call the helper once from the death path with no HP subtraction. Ensure it runs before queue-free and cannot trigger a second death/kill event.
5. Expose a narrow Enemy forwarding method if needed, then call it from surface egg retirement and suction retirement before `queue_free`; ensure cave-consumption notification and egg damage remain exactly once. Cover the ExitTube fallback if it bypasses the controller.
6. Preserve trap attribution and last-hit kill-credit semantics; use explicit typed declarations and existing indentation/style.

## Acceptance criteria
- A pending `0.6` residual flushes as one recorded damage point via `int(round(...))`; a pending `0.4` clears and records zero.
- Repeated flush calls do not add damage twice.
- Death path records residual stats but does not subtract HP again and does not produce a second kill.
- Surface-path arrival and suction/pipe consumption each flush before queue-free and still deal egg damage once.
- Stored `tower_type_id` and tower instance ID are the values passed to `record_damage`; no `Unknown` attribution is invented for an unattributed residual.
- `Enemy.hp` remains `int`.
- Godot parse/import gate is clean.

## Exact tokenized runner commands
Use these exact calls, never shell strings:

```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--version"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
```

The implementation cluster may run the focused scenario only after Cluster 2 adds it; it must use:

```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","--","--harness=res://tests/scenarios/issue_32_fractional_damage_retirement.json"]}
```

## Evidence paths
- `.gen/harness/issue_32_fractional_damage_retirement/result.json`
- `.gen/harness/_logs/issue_32_fractional_damage_retirement.out.log`
- `.gen/issue-32/production-notes.md`
- Hermes-side `git diff --check` and diff restricted to the four owned production files.

## Risks
- A single instance may receive hits from multiple tower types; follow existing last-hit attribution semantics and document the chosen stored-type update rule.
- A flush after queue-free is invalid; all callers must flush first.
- Calling the helper from both `_handle_death` and a generic `_notification` would double-count; choose one explicit owner.
- Preserve integer HP and do not use residual flush to alter gameplay survival after a death.

## Handoff
Report changed lines, parse output, and the exact residual/attribution evidence to Cluster 2 and Cluster 3. Do not touch test/evidence ownership.
