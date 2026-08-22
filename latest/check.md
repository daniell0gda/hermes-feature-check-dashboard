# Check Report — overcharge-capacitors (iteration 2)

Classification: **fixable**

## Verdict

Implementation code exists this iteration (unlike iteration 1) and the early
scenario actions prove registration, eligibility/draw, below-threshold,
tier-1-at-3, and composition math. But the focused harness scenario
`overcharge_capacitors_progression.json` fails at action 25 (status: timeout)
because its own count-drop segment selects an unplaced tower position, so no
tower is sold and the bonus never drops. Everything after action 25 — tier 2 at
6+ towers, per-type isolation, reset clearing, and the final expectation — is
therefore unverified. No criterion can be marked Done because the plan's
authoritative harness does not pass end-to-end.

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-overcharge-capacitors)

1. Preflight `godot --version` — exit 0, Godot 4.4.1.stable.
2. Build/typecheck gate `godot --headless --path . --editor --quit-after 300` — exit 0 (~9s). No parse errors from `autoload/ProgressionManager.gd`; only pre-existing asset/UID import warnings.
3. Focused test `--harness=res://tests/scenarios/overcharge_capacitors_progression.json` — **exit 1**, harness result `.gen/harness/overcharge_capacitors_progression/result.json`: `status: timeout`, unmet at action index 25: `get_overcharge_bonus_for_kind("generic") == 0.0`, actual 0.1. Final expectation also failed (`overcharge_capacitors == 0`, actual 1).
4. Full test `--harness=res://tests/scenarios/display_damage_surface_parity.json` — exit 0, harness `status=pass`. Pre-existing regression coverage; asserts nothing about overcharge.

## Root cause of focused failure (new-code defect)

In `tests/scenarios/overcharge_capacitors_progression.json`, the count-drop
segment calls `towers.select_at` with `{"v3": [-6.0, 0.0, 2.0]}` but the placed
generic towers are at (-6,0,0), (-4,0,-6), and (0,0,-3). Nothing is selected,
`ui._on_sell_pressed` removes no tower, and the tier-1 bonus stays applied
(0.1 ≠ 0.0). Fix: select an actually-placed tower position (e.g. (-6.0, 0.0,
0.0)) so the sell path removes one generic tower, then rerun to green.

## Acceptance criteria status

All 10 criteria remain Pending:

- Registration/eligibility/draw, below-threshold, exactly-3 tier-1, and
  multiplier-composition behaviors were exercised and passed in actions 0–20 of
  the run, but the scenario as a whole does not pass, so they cannot be marked
  Done on partial evidence.
- Tier-2 at 6+, per-type isolation, count-drop removal, reset clearing, and the
  `[OVERCHARGE]` debug log were never reached (timeout at action 25); note the
  headless official build is a release build so the log line cannot be observed
  in this environment regardless (same limitation as display_damage_surface_parity).

## Code quality (diff review)

Changed files: `autoload/ProgressionManager.gd` (+45/-2),
`scripts/progression/global.json` (+12), new
`tests/scenarios/overcharge_capacitors_progression.json`.

- `get_overcharge_bonus_for_kind` / `_overcharge_bonus_for_kind` /
  `_same_type_tower_count`: typed GDScript, guard clauses, nesting ≤ 2, debug
  print behind `OS.is_debug_build()` with `[OVERCHARGE]` tag per CLAUDE.md —
  consistent with project rules.
- Perk definition follows existing global.json schema (`Common`, maxLevels 3,
  level values 0.10/0.20/0.30 matching the issue's tiers).
- The only concrete violation is in the new scenario file itself: the sell-step
  coordinates do not match any placed tower (see quality-notes.md entry,
  appended this iteration).

## Quality notes

Appended one open entry:
`## overcharge-scenario-sell-target-mismatch (iteration 1)` in
`.gen/quality-notes.md` describing the mismatched `select_at` position and the
required fix.

## Blockers

None infrastructural. Runner healthy (all commands executed through
run_project_cmd). Blocker is a fixable single-coordinate bug in the new
scenario's sell step.

## Unverified items

- Tier-2 stacking math at 6+ same-type towers (never executed).
- Per-type isolation (3 generic + 3 cannon) — never executed.
- Bonus removal when count drops below threshold — attempted but defeated by
  the scenario coordinate bug.
- `reset_for_new_game()` clearing selection and bonus — never executed.
- `[OVERCHARGE]` debug-build log line — not observable in release headless
  builds; code inspection confirms it exists behind `OS.is_debug_build()`.
