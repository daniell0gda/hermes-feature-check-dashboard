# Cluster 02 — focused premature-victory and exit-persistence regression

- `parallel: false`
- Depends on: cluster 01's authoritative clear query/guard and any documented harness observability seam.
- Downstream: cluster 03 runs this coverage and inspects fresh result evidence.
- Exclusive ownership (create/modify):
  - `tests/scenarios/issue_86_victory_underground_clear.json`
  - `tests/scenarios/` only for the focused issue-86 scenario variant if an existing scenario must be updated
  - `scripts/testing/AgentHarness.gd` only if cluster 01 identifies a concrete missing action/value source that the existing schema cannot express; keep the seam generic and minimal
  - A focused `.gd/.tscn` test may be added under `tests/` only if the JSON harness cannot instantiate the required late-discovery/spawner/boss state; document why before adding it.
- Forbidden overlap: do not edit gameplay scripts owned by cluster 01, UI, maps, balance, unrelated scenarios, or generated `.gen/harness` outputs.

## Scenario requirements
Create a deterministic, fresh scenario with an explicit seed and no reliance on stale result files. It must exercise the real game path and assert state rather than merely matching log text.

1. Start a map/config with at least one final surface wave.
2. Arrange or trigger underground discovery after the final wave has completed (or the closest supported action that causes the same late registration), then assert victory/show-victory is still false while the underground enemy is alive.
3. Include an underground spawner with a pending/active queue and a boss-bearing spawn. Assert each of these blockers independently where the schema permits: pending spawner blocks, live boss blocks, and killing/retiring the boss clears the blocker.
4. Assert the underground exit remains in the authoritative exit list and remains interactable/accessible while any blocker remains. Do not treat a visible tube alone as sufficient if a state query is available.
5. After all waves, queues, spawners, underground enemies, and the boss are actually clear, assert intended map flow: exit removal becomes allowed/occurs once and victory/show-victory occurs once.
6. Include explicit notes in the scenario for any supported action/value-source limitation. If the current harness cannot prove late discovery or exit accessibility, stop and return the smallest schema seam to cluster 01 rather than weakening assertions.

## Exact runner command
Use the approved project runner with project `godot-td`, workspace `godot-td/issue-86`, tokenized command:

```json
["godot", "--headless", "--path", ".", "--", "--harness=res://tests/scenarios/issue_86_victory_underground_clear.json"]
```

Expected evidence: `.gen/harness/issue_86_victory_underground_clear/result.json` (or a unique suffixed fresh result path if the harness supports it), with `status: "passed"` and expectation records for every acceptance assertion. Run only after the scenario exists and cluster 01's API is present.

## Handoff evidence
Report the scenario seed, map/config, action timeline, exact value sources/fields, and which assertions cover AC1–AC5. Do not claim visual proof; this issue's acceptance is logic/state based and headless evidence is appropriate.
