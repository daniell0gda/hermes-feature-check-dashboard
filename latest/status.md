# Issue #86 status

classification: design_failure

Fresh verification completed through the approved runner and worker released.

- Version gate: exit 0, Godot 4.4.1.
- Editor/import gate: exit 0; no targeted parse, failed-resource, or invalid-parameter diagnostics.
- Exact focused gameplay command: exit 1 (`success=false`, HTTP 422 wrapper); fresh `.gen/harness/issue_86_victory_underground_clear/result.json` ended `status: timeout` at action 5.
- Root evidence: `custom_map` loaded with `total_waves: 0`; after `trigger_wave` 5, `game.victory_pending_full_clear` stayed `false`, so late discovery, blocked exit removal, boss, defeat, victory, and post-clear exit retirement actions were never reached.
- Scenario boss assertion is optional, and exit presence/accessibility has no direct value source; the focused regression does not satisfy the required spawner/boss and exit criteria even aside from the invalid wave fixture.
- Direct Main scene startup: exit 0; complete combined stream had unrelated missing UI node, duplicate signal, and teardown leak diagnostics, but no targeted parse/resource/invalid-parameter errors.
- Hermes-side `git diff --check`: exit 0. No source/test edits by checker; no commit/push/merge/close.
