# Cluster full-smoke-docs — project smoke-test source of truth

- **parallel:** false
- **depends on:** `full-harness-scenarios` so scenario names and result conventions are final.
- **exclusive ownership:** create/modify `docs/tests/smoke-tests-reference.md`; modify only the three existing smoke scenario JSON files if a documented command/schema mismatch is demonstrated. No production, issue-62 focused scenario, harness, or `.gen` ownership.
- **forbidden overlap:** do not edit source/UI/save/runtime or verification reports.

## Implementation

1. Create `docs/tests/smoke-tests-reference.md` as the authoritative smoke matrix. Document Godot 4.4.1, runner boundary, exact tokenized headless command, fresh result/log requirements, and single-instance rule.
2. Define `smoke_placement`, `smoke_tower_roster`, and `smoke_underground_visible`: purpose, prerequisites, expected `result.json` status, assertions, evidence paths, and explicit non-claims. State that headless screenshots are not visual evidence.
3. Add the issue-62 smoke entry for save/reload only if the product team wants it in the standard smoke set; otherwise document it as focused regression coverage and link its scenarios without confusing the three baseline smoke tests.
4. Document windowed invocation and visual inspection requirements separately from headless logic checks, including fresh PNG paths and stale-result rejection.

## Acceptance

- The file exists at the exact requested path and is readable by future game-test workers.
- Every documented scenario has a matching file and exact command. Any corrected scenario remains within existing conventions and has a fresh passing result in final verification.
- The document clearly says what each test cannot prove, preventing aggregate smoke results from being used as active-wave restoration proof.

## Verification command

```json
{"project":"godot-td","workspace":"godot-td/issue-62","cmd":["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_placement.json","--harness-run=full-smoke-placement-1"]}
```

Run the equivalent tokenized command for `smoke_tower_roster.json` and `smoke_underground_visible.json`, then read fresh results. Documentation itself is checked Hermes-side.

## Bounded revision

Only correct factual command/path/schema errors in one revision. Do not turn the document into a generic testing guide or silently replace missing focused evidence.