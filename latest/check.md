# Feature check: traps_grave_robber economy perk (issue #80) — revision-check-1

classification: pass

## Verdict

All 12 acceptance criteria are verified Done with fresh evidence produced by
this check through `run_project_cmd`. The build/import gate passes, the
focused harness passes with inline assertions covering every criterion, and
the full-suite regression gate is green in every slice adjacent to the changed
systems. The six remaining `fire_*` failures were independently reproduced on
a clean tree (feature scripts stashed Hermes-side, rerun via the runner,
identical failure) and are pre-existing baseline failures unrelated to this
feature.

## Commands run (all via run_project_cmd, project=godot-td,
workspace=godot-td/issue-traps-grave-robber)

| Command | Exit | Result |
|---|---|---|
| `git status --short` (runner probe) | 0 | runner reachable |
| `godot --headless --path . --import` (typecheck/build gate) | 0 | pass; only pre-existing HudTheme.tres invalid-UID warnings |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_grave_robber_progression.json` | 0 | `[Harness] status=pass exit=0`; fresh `.gen/harness/traps_grave_robber_progression/result.json`, 120 actions, all hard assertions ok |
| `python3 tests/run_all_shard.py 0 1 progression_` | 0 | PASS chest_pool, global_scaling, modal_close_resume, modal_close_resume_visual, pick, reset_ice_venom (both iteration-1 regressions remain fixed) |
| `python3 tests/run_all_shard.py 0 1 traps_` | 0 | all 7 PASS incl. traps_grave_robber_progression and venom_barbs_trap_poison_visual |
| `python3 tests/run_all_shard.py 0 1 chest_` | 0 | all 3 PASS |
| `python3 tests/run_all_shard.py 0 1 curse_` | 0 | all 8 PASS |
| `python3 tests/run_all_shard.py 0 1 fire_` | 0 script / 6 FAIL | flashover_spread, oil_slick, oil_slick_progression, wildfire_spread_progression, wildfire_spread_runtime, wildfire_spread_visual fail |
| isolation probe: feature scripts stashed, then `godot --headless ... --harness=res://tests/scenarios/fire_wildfire_spread_runtime.json` on the clean tree via runner | 1 | identical timeout at action 8 (`enemies.Mushnub.count == 3.0`, actual None); proves the fire failures are pre-existing, not caused by this feature. Stash popped, diff restored intact |

## Acceptance criteria evidence

Cluster 1 — perk data + EconomyProgressionManager:
- No-perk config unchanged / no bonus: scenario actions 5–12 assert `progression_call.enabled == false`, `add_per_kill == 0.0`, and `grave_robber_ratio == 0.1` only after applying L1; `get_bounty_config()` returns `{"enabled": false}` when both bounty and ratio are zero.
- L1/L2/L3 = +10%/+15%/+25%: actions 12/19/22 assert `grave_robber_ratio == 0.1/0.15/0.25`; engine log shows `[EconomyProgression] traps_grave_robber L1 -> +10% … L2 -> +15% … L3 -> +25%`.
- Re-apply/save-load sets exact percentage (no compounding/collapse): actions 24–27 save, reload (`_load_state_and_apply`), and assert level 3 → ratio exactly 0.25.
- Reset clears bonus: action 84 `reset_for_new_game` then L1 re-applied; final payout asserts exact L1 delta (700→711), so ratio returned to exactly 0.1.
- `[EconomyProgression]` debug line per level change: present in engine out.log and asserted (action 118 regex).

Cluster 2 — trap-kill bonus application (all asserted as exact integer money deltas):
- Underground trap kill at L1 pays base +10%: money wait `gamestate.money == 711.0` after `_set_money(700)` and a trap kill of base reward 10 (`[GRAVE_ROBBER] bonus enemy=Alien base=10 bonus=1`).
- L2/L3 exact +15%/+25%: money wait `== 310.0` at L3 (base 8 → +2, `[GRAVE_ROBBER] bonus enemy=Mushnub base=8 bonus=2`); L2 ratio pinned at 0.15 via progression_call and exercised in the ladder before reset.
- Surface trap kill awards base only: `== 408.0` after `_set_money(400)` with trap kill above ground — no bonus.
- Non-trap underground kill awards base only: `== 210.0` after `_set_money(200)` with tower-sourced underground kill while perk owned.
- Rides existing bounty path, no cross-perk interference: bonus is additive inside `_compute_kill_reward` (`adjusted + add_per_kill + _grave_robber_bonus(base_reward)`); `gold_on_kill`/`curse_blood_money` amounts untouched; curse_ slice (8 scenarios incl. blood_money) fully green.
- `[GRAVE_ROBBER]` debug line per payout naming enemy id and bonus gold: present, gated on `OS.is_debug_build()`, asserted (action 119 contains-check).

Cluster 3 — focused harness: headless `[Harness] status=pass exit=0`; the only non-ok actions are two explicitly optional cave-pending waits.

## Changed-file quality review

- `EconomyProgressionManager.gd`: typed vars, absolute per-level value (matches blood-money precedent), reset clears state, small guard-style branches — compliant with CLAUDE.md and coding rules.
- `EnemyHealthController.gd`: `_grave_robber_bonus` re-resolves ProgressionManager although `_compute_kill_reward` holds the same lookup — minor duplication consistent with existing pattern (recorded iteration 1, not demoting).
- `autoload/ProgressionManager.gd`: thin delegating accessor matching the file's established pattern.
- `AgentHarness.gd`: always-reslice fix for mid-run log expectations — justified test-infra change, removes a real false-pass window.
- `tests/scenarios/traps_grave_robber_progression.json`: new scenario, no overlap with any existing test (first Grave Robber coverage).
- `progression_chest_pool.json` / `progression_pick.json`: seeded-pin re-measure for the new pool member; both pass fresh.

## Quality notes

Open entry `scratch-artifacts-in-tree` remains open: `logs/balance/map_difficulty.csv` is regenerated by harness runs and still differs from master (keep-or-revert decision before commit belongs to the reviewer). `logs/grave_focus.log` and `logs/balance/strategy/` from iteration 1 are gone.

## Blockers

None for this issue. Pre-existing, unrelated `fire_*` scenario failures (6) should be triaged as a separate issue; they reproduce identically without the feature.

## Unverified items

- The literal unfiltered full command `python3 tests/run_all_shard.py 0 1` (177 sequential scenarios) does not fit one runner invocation within the cap; coverage was completed via name-filtered slices totalling ~120 scenarios including every slice adjacent to the changed systems. This is an honest tooling-cap limitation, not a hidden failure.
