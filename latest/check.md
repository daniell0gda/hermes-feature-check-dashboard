# Check Report — issue-water-pressure-bonus (Water Pressure perk #46), iteration 1

Classification: **fixable**

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-water-pressure-bonus)

| Command | Exit | Result |
|---|---|---|
| `["git","status","--short"]` | 0 | runner reachable; 5 modified files + 1 new scenario |
| `["godot","--headless","--path",".","--editor","--quit-after","300"]` (typecheck/build) | 0 | PASS — scripts parse; only pre-existing HudTheme texture UID warnings |
| `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/water_pressure_progression.json"]` (focused) | 0 | PASS — `.gen/harness/water_pressure_progression/result.json` status=pass; [WATER-PRESSURE] L1/L2/L3 log lines observed (0.20/0.35/0.50) |
| `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/progression_pick.json"]` (full) | 1 | FAIL — `.gen/harness/progression_pick/result.json` status=fail; reproduced twice (12:42:33, 12:43:22): `progression.venom_miasma_bloom` expected 1 actual 0, `progression_call.enabled` expected true actual false; modal resolved to kind=money instead of venom_miasma_bloom |

## Acceptance criteria evidence

All nine criteria are demoted to Pending solely because the full-suite gate (`progression_pick`) fails reproducibly under the current worktree. The focused harness itself passes and covers criteria 1–9 well:

- Unowned baseline ratio 0.0, L1/L2/L3 exact ratios 0.2/0.35/0.5, non-Wet guard via get_water_wet_bonus()==0.0 unowned, level cap (4th apply rejected, is_eligible==false), reset returns bonus to 0.0, electric_wet_conduction coexistence, tooltip "Bonus vs Wet: +20%/+35%/+50%" present/absent — all asserted in `tests/scenarios/water_pressure_progression.json` and green.
- `[WATER-PRESSURE]` debug log lines confirmed in fresh runner stdout.
- Implementation reviewed against coding rules/CLAUDE.md: typed vars, debug-tag logging, small functions — no violations found in the diff.

## Failure analysis (for the coder)

`progression_pick` seeds 20260726 and uses auto-answer index 0 on upgrade modals. An earlier stored run of the same scenario at 12:33:25 passed (venom_miasma_bloom picked); after the focused water_pressure harness ran (which autosaves via SaveManager), the full scenario deterministically resolves its first modal to a `money` card and never picks `venom_miasma_bloom`. Likely causes to investigate: persisted save/progession state leaking between harness runs (scenario lacks a clean-state/reset step or the harness does not clear SaveManager data), or the new `water_pressure` entry changing the Common-perk card pool/order so index 0 no longer maps to venom_miasma_bloom. Fix by making `progression_pick` state-independent (reset progression + clear save at scenario start) or updating its pick expectations for the enlarged perk pool, then rerun both harnesses back-to-back.

## Blockers

None infrastructural. Runner healthy; classification is fixable (full-suite regression/flakiness to be repaired by coder).

## Unverified items

None beyond the failing full suite; manual_testing is none per plan.
