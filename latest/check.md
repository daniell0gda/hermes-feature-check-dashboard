# Check report — Exposed Plating perk (issue #89) — revision-check-1

classification: fixable

## Verdict

Implementation is functionally complete and freshly re-verified green through the approved
runner (`project=poke-defense-godot`, `workspace=poke-defense-godot/issue-exposed-plating`).
Four of five criteria are Done with passing automated harness evidence. Criterion 4 stays
Pending solely because the request mandates player-facing windowed VFX evidence (screenshots /
real-30fps `record_frames` GIF plus `ui_feels_broken` pass), owned by the manual-tester profile,
which has not been produced. All headless-verifiable work passes; no build, parse, or test failures.

## Verification commands (all via run_project_cmd)

1. Preflight: `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
2. Import/parse gate: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
   — exit 0 (10.2s). New scripts register cleanly; no script errors.
3. Focused semantics harness:
   `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
   — exit 0, `status=pass`, 6/6 expectations. Fresh result:
   `.gen/harness/exposed_plating_once_per_shield/result.json`.
4. VFX lifecycle bed:
   `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
   — exit 0, `status=pass`, 7/7 expectations including `Orc Enemy_boss.exposed_vfx == true`
   during the Exposed window. Fresh result: `.gen/harness/exposed_plating_vfx/result.json`.

## Criterion-by-criterion

1. Perk registration / purchasable at 3 levels — **Done.** `scripts/progression/global.json`
   defines `exposed_plating` Common, maxLevels 3 (0.15/0.5s, 0.25/1s, 0.35/1.5s);
   `CurseProgressionManager.HANDLED` + `_apply_exposed_plating` apply absolute per-level figures;
   harness ran apply_progression L1→L2→L3 (`exposed_plating == 3` expectation pass).
2. Once-per-shield-instance trigger (>0→0 only) — **Done.** Guard at the `_consume_armor()` site;
   run log shows exactly one `[EXPOSED] triggered` line per level leg; post-expiry hit dealt
   exactly baseline damage with `exposed_multiplier == 1.0` and `exposed == false` (asserted in result.json).
3. Multiplier per level for duration, clean expiry — **Done.** Exact hp deltas in run output:
   L1 1625→1614, L2 →1613, L3 →1612 inside the breaching hit (×1.15/×1.25/×1.35); after 2s wait
   hp dropped exactly to 1589 (=10 unamplified); `[EXPOSED] expire on Orc Enemy_boss` logged.
4. New VFX following BurnStatus/BurnVFX pattern, lazy EffectsManager instantiation — **Pending**
   (visual evidence only). Code exists and follows project patterns:
   `scripts/game/status/ExposedStatus.gd`, `scripts/game/actors/effects/ExposedVFX.gd`,
   lazy `show_exposed()`/`hide_exposed()` in EffectsManager. Headless machine proof of the wash
   lifecycle passes (`exposed_vfx` asserted true while exposed in the vfx scenario). Remaining:
   mandated windowed screenshots / real-30fps `record_frames` GIF of the VFX on a real enemy +
   `ui_feels_broken` UI-sanity pass — manual-tester profile scope, not yet produced. Run
   `tests/scenarios/exposed_plating_vfx.json` windowed (e.g. `--rendering-method gl_compatibility
   --rendering-driver opengl3 --audio-driver Dummy`); captures land in
   `.gen/harness/exposed_plating_vfx/{shots,record}/`.
5. `[EXPOSED]` debug logging gated by `OS.is_debug_build()` — **Done.** Trigger and expiry lines
   behind `OS.is_debug_build()` in ExposedStatus.gd and EnemyHealthController.gd; observed in run output.

## Changed files reviewed

Feature diff (git status): autoload/ProgressionManager.gd, EffectsManager.gd,
EnemyHealthController.gd, global.json, CurseProgressionManager.gd, AgentHarness.gd,
HarnessValues.gd + new files ExposedVFX.gd, ExposedStatus.gd,
tests/scenarios/exposed_plating_once_per_shield.json, exposed_plating_vfx.json. Only declared
workflow artifacts additionally changed; no scope creep.

- global.json — valid JSON, entry matches sibling progression perk formatting.
- ExposedStatus.gd / EnemyHealthController.gd — typed, guard clauses, debug-gated logs,
  multiplier applied at take_damage site; transition guard correct.
- ExposedVFX.gd — own-mesh material_override shell (StaticBreachVFX precedent; avoids the
  enemy-material-swap trap). Issue text says "swapped onto the enemy's material" but project
  precedent treats that as a known trap; advisory note only, no demotion.
- HarnessValues.gd / AgentHarness.gd — `exposed_vfx` observable mirrors existing
  `static_charge_vfx` pattern; headless `record_frames` skip mirrors screenshot-checkpoint semantics.
- Test overlap: searched tests/scenarios — no prior exposed_plating coverage; both scenarios are new, non-overlapping.

## Quality notes

No open entries in quality-notes.md (file not present; nothing appended this iteration).
Advisory (not demoting): Static Breach's `consume_static_breach_armor()` path zeroes armor
without passing through `_consume_armor`, so a Static-Breach armor shatter does NOT open an
Exposed window. Issue wording pins the trigger site to `_consume_armor()`, so current behavior
appears intended; leader may confirm against issue #89.

## Blockers

None. Runner healthy throughout (probe, import gate, both harnesses exit 0).

## Unverified items

- Windowed/manual VFX pixel evidence for criterion 4 (manual-tester profile owns it).
