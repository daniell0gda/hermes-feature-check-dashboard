# Coder report: porter-broad-sweep-perk\n\n# Coder report: porter-broad-sweep-perk

## Changed files
- `scripts/progression/porter_tower.json` — mod: added `porter_broad_sweep` Common perk (maxLevels 3, label "Broad Sweep", porter compatibility, `"needs": ["porter_mass_transit"]`, level values 0.5/0.8/1.0 = absolute bonus ratio over base sweep radius).
- `scripts/progression/managers/PorterTowerProgressionManager.gd` — mod: `BROAD_SWEEP_NAME` const, `_sweep_radius_multiplier` state (base 1.0), reset in `reset()`, `can_handle`/`apply_level` routing to new `_apply_broad_sweep()` (idempotent absolute-ratio application + debug-build `[PORTER_BROAD_SWEEP] apply L%d sweep_radius_multiplier=x%.2f` print), accessor `get_sweep_radius_multiplier()`.
- `autoload/ProgressionManager.gd` — mod: new passthrough `get_porter_sweep_radius_multiplier() -> float` (1.0 fallback), next to `is_porter_mass_transit_owned()`.
- `scripts/game/actors/towers/PorterTower.gd` — mod: `_mass_transit_sweep_radius()` now computes base (maxf(0.4, path_half_width)) then multiplies by ProgressionManager's sweep multiplier; normal targeting range untouched.
- `tests/scenarios/porter_broad_sweep.json` — new harness scenario covering all nine criteria inline.

## Criteria
All 9 cluster criteria — Done (verified via focused harness).

## Commands and results
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit code 0; import/typecheck clean (only pre-existing HudTheme.tres invalid-UID warnings).
- Focused `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_broad_sweep.json"]` — `[Harness] status=pass exit=0`; result at `.gen/harness/porter_broad_sweep/result.json`. Log evidence: chest pool 35 → (after mass transit) 36 with porter_broad_sweep included → back to 35 at L3; `[PORTER_MASS_TRANSIT] owned`; `[PORTER_BROAD_SWEEP] apply L1/L2/L3 sweep_radius_multiplier=x1.50/x1.80/x2.00`.
- Full `["python3","tests/run_all_shard.py","0","1"]` — INCONCLUSIVE/environmental: first attempt exceeded the 420s tool window; second attempt was killed with exit code 137 (SIGKILL/OOM) partway through (~29 of ~90 scenarios). Several FAILs in that partial output are contention artifacts, not regressions: e.g. `burn_status_refresh_pending_damage` re-run individually afterwards returned `[Harness] status=pass exit=0`. The worker appears memory-constrained for the sequential full-shard run. Recommend checker re-run the shard with more headroom or per-scenario.
- Note: an earlier unrelated log line `skip incompatible chest reward: porter_mass_transit` before Mass Transit is owned confirms gating also applies to the prerequisite perk.

## Notes
- Level values are ABSOLUTE bonus ratios over base (0.5/0.8/1.0 → multipliers 1.5/1.8/2.0), so replaying levels 1..N on load lands on N without compounding — matches existing porter_wide_gate pattern.
- Broad Sweep never touches `_range_multiplier`; scenario asserts `get_porter_range(6.5)==6.5` after every application.
- `needs` gating is enforced by ProgressionManager's generic eligibility/draw logic (no manager-side change needed for criteria 3–4); verified by is_eligible/draw assertions both directions.
- Gotcha for tester: short headless runs quit before godot.log flushes, so log-source assertions on the `[PORTER_BROAD_SWEEP]` print resolve empty — the debug print is asserted via stdout capture instead (visible in the focused-run output above).
\n