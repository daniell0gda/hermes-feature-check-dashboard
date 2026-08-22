# Check Report — issue-water-pressure-bonus (Water Pressure perk #46), iteration 2

Classification: **pass**

All verification commands were run fresh via `run_project_cmd` (project=poke-defense-godot,
workspace=poke-defense-godot/issue-water-pressure-bonus). No host-shell Godot was used.

## Verification commands (fresh, this iteration)

| Command | Exit | Result |
|---|---|---|
| `["git","status","--short"]` | 0 | runner reachable; 6 modified files + 1 new scenario |
| `["godot","--headless","--path",".","--editor","--quit-after","300"]` (typecheck/build) | 0 | PASS — all scripts parse; only pre-existing HudTheme texture-UID warnings |
| `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/water_pressure_progression.json"]` (focused) | 0 | PASS — `.gen/harness/water_pressure_progression/result.json` status=pass; `[WATER-PRESSURE] water_pressure L1 -> bonus=0.20 / L2 -> 0.35 / L3 -> 0.50` observed in fresh runner stdout |
| `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/progression_pick.json"]` (full) | 0 | PASS — `.gen/harness/progression_pick/result.json` status=pass; venom_miasma_bloom=1, enabled=true, paused=false, current_layer=underground |

The iteration-1 failure of `progression_pick` (exit 1: modal resolved to money instead of
venom_miasma_bloom) is fixed by the coder's change: three
`apply_progression(water_tower.json, water_pressure)` exhaustion calls were added to
`progression_pick.json`, restoring the deterministic 2-card chest pool the seeded draw relies on.
Both harnesses now pass back-to-back in the same worktree.

## Acceptance criteria evidence — 9/9 Done

1. Unowned baseline ratio 0.0 — focused harness asserts `get_water_wet_bonus() == 0.0`
   before any pick; implementation returns 0.0 when not owned (`WaterTowerProgressionManager._wet_bonus`
   init/reset; `ProgressionManager.get_water_wet_bonus()` fallback). Test would fail if broken.
2. L1/L2/L3 exact ratios 0.2/0.35/0.5 — asserted per level via `wait_for_condition` after each
   apply; log lines confirm applied values. EnemyHealthController multiplies by `1.0 + pbonus`
   for Water hits only while Wet.
3. Non-Wet guard — `_apply_wet_bonus_if_needed` early-returns when `wet_time_left <= 0.0`
   before any attacker-type branch; ratio stays 0.0 unowned. Guard verified at code path +
   baseline assertion (harness notes document that wet_time_left itself is not harness-readable).
4. Water-only multiplication — Electric branch preserved unchanged (`get_electric_wet_bonus`),
   other types fall through unmodified; harness asserts electric_wet_conduction still yields 0.5
   after water_pressure L3 (both bonuses coexist).
5. Reset — `reset()` zeroes `_wet_bonus`; harness calls `reset_for_new_game` then asserts
   level 0, bonus 0.0, tooltip line absent.
6. Debug log line — `OS.is_debug_build()` + `[WATER-PRESSURE]` prefix naming event, level, and
   resulting bonus ratio; observed in fresh runner stdout at L1/L2/L3.
7. Perk data + cap — `water_tower.json` defines Common `water_pressure`, maxLevels 3, bonuses
   0.2/0.35/0.5; harness applies 4 times: level 1→2→3, fourth rejected (`is_eligible == false`,
   level stays 3).
8. Focused AgentHarness scenario — `tests/scenarios/water_pressure_progression.json` covers
   baseline, each level's exact ratio, non-Wet guard path, cap, reset via `progression_call`
   expectations; passes headless with `status: pass`.
9. Tooltip — UI.gd appends `Bonus vs Wet: %+.0f%%` only while owned; harness asserts
   "+20%"/"+35%"/"+50%" present at each level and absent before pick and after reset.

No test-overlap issue found: no existing suite test already asserted these behaviors on the same
code paths; the new scenario is additive.

## Changed-file quality review (vs /opt/data/coding_rules.md + CLAUDE.md)

- ProgressionManager.gd / WaterTowerProgressionManager.gd / EnemyHealthController.gd / UI.gd:
  typed variables everywhere, small single-purpose functions, guard clauses with ≤2 if-nesting,
  debug-tag logging per state transition, surgical diff scoped to the feature. No violations.
- water_tower.json: valid JSON (Godot parses it); indentation of the inserted block uses tabs
  vs the file's spaces — cosmetic, recorded in quality-notes.md (advisory only).

## Blockers

None.

## Unverified items

None. manual_testing is none per plan.
