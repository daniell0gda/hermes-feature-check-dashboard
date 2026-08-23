# Coder report: implementation

## Changed files
- `scripts/progression/global.json` — new `exposed_plating` Common perk, 3 levels (+15%/0.5s, +25%/1s, +35%/1.5s)
- `scripts/progression/managers/CurseProgressionManager.gd` — HANDLED + `_apply_exposed_plating`, `get_exposed_plating_config` (absolute per-level figures, replay-safe)
- `autoload/ProgressionManager.gd` — passthrough getter `get_exposed_plating_config()`
- `scripts/game/status/ExposedStatus.gd` — NEW timed Exposed mark (BurnStatus pattern), `[EXPOSED]` trigger/expire logs gated by OS.is_debug_build()
- `scripts/game/actors/effects/ExposedVFX.gd` — NEW amber cracked-shield pulsing wash shell (own meshes via material_override, StaticBreachVFX pattern; never the enemy model slot)
- `scripts/game/actors/effects/EffectsManager.gd` — lazy `show_exposed()` / `hide_exposed()` (BurnVFX/OilVFX pattern)
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — trigger in `_consume_armor()` on the armor >0 -> 0 transition only; `exposed_damage_multiplier()` applied to hit_damage in take_damage
- `scripts/testing/HarnessValues.gd` — REVISED this iteration: new observable `exposed_vfx` per-enemy flag and root count (`ExposedVFX` node present AND its `ExposedShell` mesh visible), mirroring `static_charge_vfx`
- `scripts/testing/AgentHarness.gd` — REVISED this iteration: `record_frames` now skips cleanly under --headless (same semantics as screenshot checkpoints) instead of failing on an empty capture set
- `tests/scenarios/exposed_plating_once_per_shield.json` — NEW focused once-per-shield semantics scenario
- `tests/scenarios/exposed_plating_vfx.json` — NEW this iteration: VFX visual bed (state machine asserted + screenshot/record_frames checkpoints for windowed runs)

## Criteria (issue #89 acceptance)
1. Perk registered like other progression perks, purchasable at 3 levels — Done (global.json; apply_progression ladder exercised L1->L2->L3 in the run)
2. Trigger exactly once per shield instance (>0 -> 0 only) — Done (`_consume_armor` guard `before > 0.0 and enemy.armor <= 0.0`; second hit at armor 0 does not re-fire; re-trigger requires regained armor)
3. Damage multiplier per level for duration then expires cleanly — Done (exact hp deltas 11/12/13 vs baseline 10 prove x1.15/x1.25/x1.35 including inside the breaching hit; expiry asserted by unamplified hit and multiplier == 1.0)
4. New VFX ExposedStatus/ExposedVFX following BurnStatus/BurnVFX pattern, lazy EffectsManager instantiation — Done code-side; the VFX lifecycle is NOW machine-proven headless (`exposed_vfx` 0 before breach -> 1 while exposed -> 0 after expiry). Actual pixel evidence (windowed screenshot / real-30fps record_frames GIF + ui_feels_broken pass) remains manual-tester scope: run `tests/scenarios/exposed_plating_vfx.json` with a windowed renderer.
5. `[EXPOSED]` debug logs gated by OS.is_debug_build() — Done (trigger + expire lines observed in run output)

## Commands and results (all via run_project_cmd, project=godot-td)
- `git status --short` — exit 0; only feature files + declared workflow artifacts changed
- `godot --headless --path . --editor --quit-after 300` — exit 0 (9.2s import/parse gate, class cache fresh)
- `godot --headless --path . res://scenes/Main.tscn --audio-driver Dummy -- --harness=res://tests/scenarios/exposed_plating_once_per_shield.json`
  — exit 0, harness status=pass, 6/6 expectations.
  Breaching-hit deltas L1 1625->1614 (10x1.15=11), L2 ->1613, L3 ->1612; second armor_hit at armor 0 amplified but exactly one `[EXPOSED] triggered` line per leg; after 2s wait hp -10 exactly, exposed_multiplier == 1.0, `[EXPOSED] expire on Orc Enemy_boss` logged. Fresh result at `.gen/harness/exposed_plating_once_per_shield/result.json`.
- `godot --headless --path . res://scenes/Main.tscn --audio-driver Dummy -- --harness=res://tests/scenarios/exposed_plating_vfx.json` (NEW this iteration)
  — exit 0, harness status=pass, 7/7 expectations. Action trace proves the full wash lifecycle:
  index 4 `exposed_vfx == 0` pre-breach, index 9 `exposed_vfx == 1` right after the breaching hit,
  index 13 `exposed_vfx == 0` after expiry; screenshots skipped headless as designed;
  record_frames skipped headless via the new guard. Fresh result at `.gen/harness/exposed_plating_vfx/result.json`.

## Notes
- The breaching hit itself IS amplified (take_damage multiplies after _consume_armor) — deliberate, covered by exact-delta assertions.
- Static Breach's `consume_static_breach_armor()` zeroes armor without flowing through `_consume_armor`, so a breach that itself shatters armor does NOT fire Exposed (issue wording pins the site to `_consume_armor`; flagged to leader previously).
- Harness runs need the explicit scene arg `res://scenes/Main.tscn` before `--`.
- Manual/windowed VFX verification remains REQUIRED per request: run the new `exposed_plating_vfx` scenario windowed (e.g. add `--rendering-method gl_compatibility --rendering-driver opengl3`) to capture `before_breach_no_wash`, `exposed_wash_on_breach`, `wash_cleared_after_expiry` PNGs plus a real frame-sequence GIF from `record_frames` (frames land in `.gen/harness/exposed_plating_vfx/shots|record/`), then end with a `ui_feels_broken: yes|no` UI-sanity pass.
