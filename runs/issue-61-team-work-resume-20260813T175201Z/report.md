# Issue #61 checker report

## Verdict

done

## Classification

pass

## Exact commands and evidence

Approved runner parameters for every project command: `project=godot-td`, `workspace=godot-td/issue-61`.

1. `godot --version` — exit 0; Godot 4.4.1.stable.official.49a5bc7b6.
2. `godot --headless --path . --editor --quit-after 300` — exit 0.
3. `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/issue_61_residual_flush.json` — exit 0, fresh structured `status=pass`; result `.gen/harness/issue_61_residual_flush/result.json`.
4. `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/ice_focus_cone_cadence.json` — exit 0, fresh `status=pass`; raw `/tmp/hermes-results/call_rJUWECjtuLD7QcrPkzumRo8K.txt`.
5. `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json` — exit 0, fresh `status=pass`; raw `/tmp/hermes-results/call_LMFVZfpKz3L5bqr8Hl0thMnW.txt`.
6. `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/projectiles_10x_beam_cone.json` — exit 0, fresh `status=pass`; raw `/tmp/hermes-results/call_xPC2Zm4YqELF9hVBD0qpJ1hf.txt`.
7. Final `git diff --check` — exit 0; final status remains the four intended implementation files (three modified plus the untracked focused scenario).

The focused result has 32 actions and 5/5 final expectations passing. Pre-reload action records independently prove 0.4 -> 0 and idempotence, 0.6 -> 1 with HP/`fire`/6101 attribution and idempotence, real Cactoro death attribution plus cave UID consumption, separate surface egg decrement, signal-backed tube `captured=1` and `exited=1`, and pre-reload checkpoints. Reload then yields `map_1`, egg 20, and zeroed stats. All three baseline result files have all expectations passing.

Independent complete-output scans of the three persisted baseline captures found zero `Parse Error`, `Failed loading resource`, `Failed to load script`, `Invalid parameter`, and `SCRIPT ERROR` markers. Focused runner output was independently inspected and showed no targeted parse/resource/script diagnostics. Known unrelated `Node not found` UI/health-bar messages, duplicate signal warnings, and renderer/RID/ObjectDB teardown leak diagnostics remain in fresh output and are recorded in `.gen/check.md`; they are not changed-file parse/resource failures and also occur in baseline runs.

## Dashboard publication state

No dashboard publication or notification was performed by this checker. Existing local `.gen/team-work-dashboard` artifacts were preserved. Parent owns dashboard phase/terminal publication and lifecycle actions.

## Unverified criteria

None.

## Next action

Parent should reconcile this report with the dashboard and complete the issue lifecycle. Do not modify source based on the known unrelated teardown/UI diagnostics.

## Files written by this checker

- `.gen/check.md`
- `.gen/status.md`
- `.gen/report.md`

No source, scenario, or dashboard artifact was changed by the checker.
