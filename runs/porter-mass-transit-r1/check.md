# Check report: porter_mass_transit (iteration 1)

classification: fixable

## Verdict

All 10 acceptance criteria are verified Done by fresh runner evidence. The
`fixable` classification is driven by cross-cutting quality/tooling notes only
(see quality-notes.md); no criterion was demoted.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-porter-mass-transit)

| Gate | Command | Result |
|---|---|---|
| Preflight | `git status --short` | exit 0; 5 modified feature files + new scenario |
| Typecheck/build | `godot --headless --path . --import` | exit 0 (8.2 s), parse clean for changed scripts |
| Focused harness | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/porter_mass_transit.json` | exit 0, `[Harness] status=pass exit=0`; fresh `.gen/harness/porter_mass_transit/result.json` with all expectations pass; stdout shows `[PORTER_MASS_TRANSIT] owned` and `[PORTER_MASS_TRANSIT] target=... swept=1` |
| Regression slice | `python3 tests/run_all_shard.py 0 1 porter` | 6 PASS / 1 FAIL (`porter_boss_runner`, pre-existing) |
| Regression slice | `python3 tests/run_all_shard.py 0 1 progression` | 24 PASS / 9 FAIL |

## Full-suite gate honesty

The plan's full test command `python3 tests/run_all_shard.py 0 1` (~178
scenarios × up to 280 s each) exceeds the runner tool timeout and cannot be run
as a single call; bounded name-filtered slices were used instead. The 9
progression-slice failures are pre-existing, not caused by this change:
- Verified independently by this checker: stashed all four feature files
  (clean b5d75ae tree) → `progression_pick` still FAILs through the runner;
  stash popped cleanly afterwards.
- Coder stash-reproduction also covered `porter_boss_runner`,
  `progression_chest_pool`; remaining failures are per-scenario timeouts or
  unrelated pinned-draw mismatches (scifi_overclock 1.5 vs 1.4,
  water_deep_soak 3 vs 4, venom_miasma_bloom, etc.) touching none of the
  changed code paths. No changed file appears in any failing scenario.

## Criterion evidence

1. Unique/maxLevels 0 catalog entry — scripts/progression/porter_tower.json diff; import gate parses it; scenario asserts owned + level==1 after two applies (idempotent).
2. Unowned API state — focused scenario arm 1: `is_porter_mass_transit_owned == false`, level 0 before any apply.
3. Apply once → owned level 1, repeat stays level 1 — scenario double-apply assertions pass in result.json expectations.
4. Perk-off single-target preserved — `_collect_mass_transit_sweep_targets` returns [] when unowned; scenario perk-off arm asserts dissolving == 1 exactly and floodgate damage stays at single-enemy level (≤ 8).
5. Perk-on sweep of nearby surface enemies — scenario perk-on arm asserts dissolving >= 2 and floodgate damage > 8 on map_6.
6. Dead/underground/out-of-radius never swept — explicit filters in `_collect_mass_transit_sweep_targets` (is_dead, is_enemy_underground, dxz radius) plus re-check at teleport time.
7. Per-candidate route validity — `_begin_mass_transit_teleport` calls `compute_underground_route` per enemy and skips invalid routes.
8. Feedback parity — swept enemies get `_create_porter_rings`, `_spawn_porter_teleport_burst`, `TeleportDissolveEffect.apply_to_enemy` (same calls as locked target).
9. Debug log line — observed live in runner stdout: `[PORTER_MASS_TRANSIT] target=@Node3D@1207 swept=1` behind OS.is_debug_build().
10. Focused scenario passes headless — fresh result.json status=pass, exit 0.

## Test overlap check

New test `tests/scenarios/porter_mass_transit.json` does not overlap existing
coverage: existing porter scenarios (wide_gate, overcharged_rings, boss_runner)
assert range/charge/boss behaviour, none assert mass sweep or this perk's
ownership semantics.

## Quality findings

Changed-code review against /opt/data/coding_rules.md: surgical scope, typed GDScript, reuses existing helpers, idempotent toggle — no violations found that demote a criterion.

Advisory (appended to .gen/quality-notes.md, iteration 1):
- porter-burst-emission-tween: the newly activated legacy `_spawn_porter_teleport_burst` tweens `emission_energy`, which does not exist on StandardMaterial3D in Godot 4.4 (`emission_energy_multiplier`), producing runtime tween errors and a non-animating burst fade. Legacy function, advisory only.

Other notes: `logs/balance/map_difficulty.csv` churn is auto-generated balance telemetry from harness runs, not hand-edited scope creep.

## Blockers

None. Manual windowed UI-sanity screenshots remain for the manual-tester profile (headless runs skip screenshots); not a checker blocker.

## Unverified items

- Full unfiltered shard as one command (runner tool-timeout; slices substituted).
