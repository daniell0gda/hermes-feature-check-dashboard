# Check report — Exposed Plating perk (issue #89)

classification: fixable

## Verdict

Implementation is functionally complete and freshly re-verified green through the approved
runner. One criterion stays Pending only because the request mandates player-facing windowed
VFX evidence (manual-tester owned) that has not been produced yet. Everything headless-verifiable
passes; no build, parse, or test failures.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-exposed-plating)

1. Preflight: `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
2. Editor/import/parse gate: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
   — exit 0 (9.2s). New `class_name ExposedStatus` registered cleanly; no script errors.
3. Focused harness:
   `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
   — exit 0, harness `status=pass`, all 6 expectations pass. Fresh result at
   `.gen/harness/exposed_plating_once_per_shield/result.json` (finished_at 17:03, this run).

## Criterion-by-criterion

1. Perk registration / 3 levels — **Done.** `scripts/progression/global.json` defines
   `exposed_plating` Common, maxLevels 3 with value/duration per level;
   `CurseProgressionManager.HANDLED` includes it and `_apply_exposed_plating` applies absolute
   per-level figures (replay-safe). Harness ran apply_progression L1→L2→L3
   (`progression.exposed_plating == 3` expectation pass).
2. Once-per-shield-instance trigger — **Done.** Guard in `_consume_armor()`
   (`before > 0.0 and enemy.armor <= 0.0`) fires only on the >0→0 transition. Harness: second
   armor_hit while armor==0 amplified damage but produced exactly one `[EXPOSED] triggered` log
   line per leg; expiry asserted after 2s wait (`exposed=false`, multiplier back to 1.0,
   `[EXPOSED] expire on Orc Enemy_boss` logged).
3. Multiplier per level for duration, clean expiry — **Done.** Exact hp deltas prove L1 10→11,
   L2 →12, L3 →13 (×1.15/×1.25/×1.35) including inside the breaching hit; post-expiry hit dealt
   exactly 10 (unamplified) and `exposed_multiplier == 1.0`.
4. New VFX following BurnStatus/BurnVFX pattern — **Pending (visual evidence only).**
   Code exists and follows the established patterns: `scripts/game/status/ExposedStatus.gd`
   (timed mark, SimulationClock-driven, self-freeing), `scripts/game/actors/effects/ExposedVFX.gd`
   (amber pulsing shell on its OWN meshes via material_override — the StaticBreachVFX trap
   avoided), lazy `show_exposed()`/`hide_exposed()` in EffectsManager. Status-window wiring is
   proven by the harness, but the request requires windowed screenshots / real-30fps
   `record_frames` GIF of the VFX on a real enemy plus a `ui_feels_broken` UI-sanity pass —
   manual-testing scope, not yet delivered.
5. Debug logging gated by `OS.is_debug_build()` — **Done.** Trigger and expiry lines use
   `[EXPOSED]` prefix behind `OS.is_debug_build()` in both ExposedStatus.gd and
   EnemyHealthController.gd; observed in run output.

## Changed files reviewed

- scripts/progression/global.json — valid JSON (parsed), consistent entry formatting.
- scripts/progression/managers/CurseProgressionManager.gd — typed, small functions, reset wiring OK.
- autoload/ProgressionManager.gd — passthrough getter mirrors existing frozen_fracture pattern.
- scripts/game/status/ExposedStatus.gd — typed, guard clauses, signal cleanup safe, debug-gated logs.
- scripts/game/actors/effects/ExposedVFX.gd — own-mesh material_override (no enemy-material swap);
  note the issue text says "swapped onto the enemy's material" but project precedent
  (StaticBreachVFX/OverheatGlowVFX comment in-file) treats that as a known trap; overlay-shell
  approach matches existing accepted patterns. No quality violation found.
- scripts/game/actors/effects/EffectsManager.gd — lazy instantiation like BurnVFX/OilVFX.
- scripts/game/actors/enemy/parts/EnemyHealthController.gd — transition guard + multiplier site OK.
- scripts/testing/HarnessValues.gd — new exposed/exposed_multiplier/enemies-report fields.
- tests/scenarios/exposed_plating_once_per_shield.json — new focused scenario (no overlap found
  with existing scenarios; no prior scenario covered exposed_plating).

No duplicate-test overlap: searched tests/scenarios for existing exposed_plating coverage — none.

## Quality notes

- No open entries in .gen/quality-notes.md (file not present; nothing appended).
- Cross-cutting diff check (`git status`): only feature files + declared workflow artifacts
  changed; no scope creep.
- Advisory (not demoting): Static Breach's `consume_static_breach_armor()` path zeroes armor
  without passing through `_consume_armor`, so a breach that itself shatters armor does NOT open
  an Exposed window. Implementor flagged this as a possible intent question — recommend leader
  confirm against issue #89 wording ("same site as _consume_armor()" suggests current behavior is
  intended).

## Blockers

None. Runner healthy throughout (probe, import gate, harness all exit 0).

## Unverified items

- Windowed/manual VFX evidence for criterion 4 (manual-tester profile owns it).

## Commands summary

| Gate | Command | Exit |
|---|---|---|
| Runner probe | godot --version | 0 |
| Import/parse | godot --headless --path . --editor --quit-after 300 | 0 |
| Focused harness | godot --headless --path . res://scenes/Main.tscn --audio-driver Dummy -- --harness=res://tests/scenarios/exposed_plating_once_per_shield.json | 0 (status=pass) |
