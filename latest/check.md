# Feature check: traps_grave_robber economy perk (issue #80) — iteration 1

classification: fixable

## Verdict

Implementation of the Grave Robber perk is correct and its focused harness
passes, but the full-suite regression gate fails: the newly added Common perk
changes the seeded chest-pick pool, breaking two pre-existing scenarios
(`progression_chest_pool`, `progression_pick`). The plan's full-test criterion
is therefore not green.

## Commands run (all via run_project_cmd, project=godot-td,
workspace=godot-td/issue-traps-grave-robber)

| Command | Exit | Result |
|---|---|---|
| `git status --short` (preflight probe) | 0 | runner reachable |
| `godot --headless --path . --import` (typecheck/build gate) | 0 | pass; only pre-existing HudTheme.tres invalid-UID warnings |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_grave_robber_progression.json` | 0 | `[Harness] status=pass exit=0`; all 117 actions ok; result `.gen/harness/traps_grave_robber_progression/result.json`; observed `[EconomyProgression] traps_grave_robber L1/L2/L3 -> +10/+15/+25%` and `[GRAVE_ROBBER] bonus enemy=Mushnub base=8 bonus=2`, `enemy=Alien base=10 bonus=1`; money deltas asserted in-scenario: 210 (non-trap underground, base only), 310 (L3 underground trap kill = 300+floor(8*0.25)=302→ asserted 310 per scenario staging), 408 (surface trap kill, base only), 711 (L1 underground trap kill after reset = 700+1) |
| `python3 tests/run_all_shard.py 0 1` (full test command as planned) | — | timed out at runner 420 s cap (177 sequential scenarios × ~40 s); same timeout hit by implementor twice |
| `python3 tests/run_all_shard.py 0 1 traps_` | 0 | PASS frostbite_fangs_progression, FAIL grave_robber_progression (rerun alone: PASS), PASS serrated_edges_panel/progression, PASS venom_barbs_progression, FAIL venom_barbs_trap_poison_visual (rerun alone: PASS → load-order flakes) |
| `python3 tests/run_all_shard.py 0 1 progression_` | 0 | FAIL progression_chest_pool (reproduced alone), FAIL progression_pick (reproduced alone), others PASS |

## Acceptance criteria evidence

Cluster 1 (perk data + EconomyProgressionManager) — verified:
- No-perk config unchanged: `get_bounty_config()` returns `{"enabled": false}` when no bounty and ratio == 0 (`EconomyProgressionManager.gd` diff); asserted via progression_call expectations in scenario.
- L1/L2/L3 ratios: harness log lines `[EconomyProgression] traps_grave_robber L1 -> +10%...L3 -> +25%` plus wait_for_condition `progression_call.grave_robber_ratio == 0.1 / 0.15 / 0.25` all ok. Absolute per-level values (no compounding) — reset/replay arm asserted (700→711 at L1 after L3).
- Reset clears bonus: scenario resets progression then re-applies L1; ratio returns to exactly 0.1.
- `[EconomyProgression]` debug line per level change: present and asserted via log expectation.

Cluster 2 (trap-kill bonus application) — verified:
- Underground trap kill pays exact integer delta (base + floor(ratio*base)): asserted money waits (300→310 L3 staged base 8 → +2; 700→711 L1 base 10 → +1).
- Surface trap kill: base only (asserted 400→408).
- Non-trap underground kill: base only (asserted 200→210).
- Rides existing bounty path, additive in `_compute_kill_reward` (`adjusted + add_per_kill + _grave_robber_bonus(base_reward)`); gold_on_kill/blood_money amounts untouched.
- `[GRAVE_ROBBER] bonus enemy=... base=... bonus=...` debug line per payout: present, gated on `OS.is_debug_build()`, asserted via log expectation.

Cluster 3 (focused harness) — verified: headless run status=pass, exit 0, all inline assertions ok.

## Failed criteria

Full-suite regression (build/test gate): `progression_chest_pool` and
`progression_pick` fail reproducibly with the feature applied.
- `progression_chest_pool`: seeded `draw_choices_for_chest(2)` no longer contains `curse_blood_money` (draw now yields chest_duplication + sundering_bolts) and `tower_dmg.level` ends 0 instead of 1. Cause: the new Common perk `traps_grave_robber` joins the eligible normal chest pool, changing the seeded weighted pick that these deterministic scenarios rely on.
- `progression_pick`: auto-answer picks leave `venom_miasma_bloom.level == 0` and `get_venom_miasma_config().enabled == false` — same pool-composition root cause shifting the seeded choice sequence.
These are regressions caused by this feature's data addition, not infra failures.

Also noted (non-blocking): `traps_grave_robber_progression` and
`traps_venom_barbs_trap_poison_visual` FAIL when run inside a multi-scenario
shard slice but PASS standalone — likely shared-state/load-order flakiness;
retest after fixing the two real failures.

## Quality findings (changed files)

- `EnemyHealthController.gd`: `_grave_robber_bonus` re-resolves ProgressionManager via `get_node_or_null` although `_compute_kill_reward` already holds the identical lookup (`pm_bounty`) — minor duplication, acceptable pattern-consistency with existing code; no demotion.
- `global.json` new perk entry is clean and follows existing schema.
- Untracked scratch artifacts `logs/grave_focus.log` and `logs/balance/strategy/` must not be committed; `logs/balance/map_difficulty.csv` is regenerated balance output whose values changed because harness runs place towers/waves differently — reviewer should decide keep-or-revert before commit (per project convention previous CSVs are committed on master).

## Blockers / required fixes for next iteration

1. Restore green suite: either update the two seeded scenarios' expectations to the new pool composition (if the new perk legitimately belongs in the normal chest pool), or exclude it from chest draws if the design says Common economy perks shouldn't be chest-offered — a design decision, hence `fixable`.
2. Re-run full verification in slices that fit the 420 s runner cap (e.g. `run_all_shard.py 0 N` with N slices, or name-filtered invocations), since one invocation cannot complete 177 scenarios within the cap.
3. Clean scratch files before commit.

## Unverified items

- Full 177-scenario suite never completed end-to-end within tool cap (timeout, both attempts). Covered partially via focused slices listed above.
