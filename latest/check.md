# Check report: cave-dead-placement-duplicate (issue #120) — iteration 1

classification: fixable

## Verdict
The dead-code removal itself is correct, but the full-suite gate fails because `cave_discovery_long_carve` fails on a pre-existing deterministic expectation (`carved_tiles` 961 < 1000, identical on unmodified HEAD). All criteria moved to Pending per the build/test gate.

## Verification (all via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-cave-dead-placement-duplicate)
- Preflight `git status --short` — exit 0. Runner reachable; only change is `M scripts/game/CaveSystem.gd`.
- Typecheck/build gate: `godot --headless --path . --editor --quit-after 300` — exit 0. No script parse or class-cache errors; only pre-existing asset-import noise.
- Focused test 1: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/cave_discovery_chance.json` — exit 0, `[Harness] status=pass exit=0`, result `.gen/harness/cave_discovery_chance/result.json`.
- Focused test 2: same invocation for `cave_discovery_long_carve.json` — exit 1, status=fail. Expectation `carved_tiles >= 1000.0` got actual 961; all four other expectations pass. Result `.gen/harness/cave_discovery_long_carve/result.json`.
- Focused test 3: same invocation for `cave_discovery_pending_placement.json` — exit 0, `[Harness] status=pass exit=0`, result `.gen/harness/cave_discovery_pending_placement/result.json`.
- Plan's full-suite loop command (`sh -c for ...`) is rejected by the runner allowlist ("sh" not allowed); each scenario was invoked individually as a tokenized godot command with identical semantics. The long_carve failure makes the full suite red regardless of wrapper.

## Criterion evidence and status
1. `_find_suitable_cave_position` search under scripts/ returns zero matches — verified: `grep -rn "_find_suitable_cave_position" scripts/` exit 1 (no matches); diff shows clean 29-line removal from `scripts/game/CaveSystem.gd`. Demoted to Pending only because the global build/test gate failed.
2. Only runtime placement lookup is `CaveUtils.find_suitable_cave_position` — verified: grep shows only the CaveUtils definition plus single call site at CaveSystem.gd line 245. Pending per gate rule.
3. cave_discovery_chance passes fresh run — PASS with fresh runner evidence (this session). Listed Pending strictly because a criterion may stay Done only while the whole suite is green; implementation evidence is green.
4. cave_discovery_long_carve passes fresh run — FAILS fresh run (exit 1). Coder's baseline check reproduced identical failure (carved_tiles=961) on stashed/unmodified HEAD d241462 with seed 20260820 — pre-existing, deterministic, not caused by this change.
5. cave_discovery_pending_placement passes fresh run — PASS with fresh runner evidence; Pending per gate rule.
6. Editor parse gate completes without script parse/class-cache errors — PASS fresh this session (exit 0, zero script errors); Pending per gate rule.

## Changed-file quality findings
- `scripts/game/CaveSystem.gd`: pure removal of an uncalled private function; no new code added; no violations of /opt/data/coding_rules.md or CLAUDE.md. No quality demotions.

## Quality notes
- Appended new entry to `.gen/quality-notes.md`: pre-existing `cave_discovery_long_carve` threshold mismatch on map_6 (expects >= 1000 carved tiles, yields 961 deterministically).

## Blockers
- None infra/runner-related. The only fixable blocker is the pre-existing scenario threshold: recalibrate `tests/scenarios/cave_discovery_long_carve.json` `carved_tiles` threshold to map_6's actual carve area (or fix map_6 carve geometry), then re-run all three scenarios.

## Unverified items
- Full suite beyond the three named scenarios was not run separately; the plan defines the focused set as these three scenarios and the full loop cannot pass while long_carve fails.

manual_testing: none (dead-code removal, no user-facing change)
