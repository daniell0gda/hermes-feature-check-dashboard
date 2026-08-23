# Check report — Exposed Plating perk (issue #89) — revision-check-2

classification: fixable

## Verdict

Fresh re-verification this iteration confirms the implementation is green through
the approved runner (`project=poke-defense-godot`, `workspace=poke-defense-godot/issue-exposed-plating`).
Four of five criteria are Done with passing automated harness evidence produced
in this run. Criterion 4 remains Pending only because the request mandates
player-facing windowed VFX evidence (screenshots / real-30fps `record_frames`
GIF + `ui_feels_broken` pass), owned by the manual-tester profile; no
`.gen/manual-report.md` exists yet and this headless worker cannot produce pixel
evidence. No build, parse, or test failures; no source changes since revision-1
review.

## Verification commands (all via run_project_cmd, exit codes from runner)

1. Preflight: `["godot","--version"]` — exit 0, Godot 4.4.1.stable.official.49a5bc7b6.
2. Import/parse gate: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
   — exit 0 (9.2s). Scripts register cleanly, no script errors.
3. Focused semantics harness:
   `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
   — exit 0, `status=pass`, 6/6 expectations. Fresh result:
   `.gen/harness/exposed_plating_once_per_shield/result.json`. Run log shows per-level legs
   L1 1625→1614→1603 (×1.15), L2 →1613→1601 (×1.25), L3 →1612→1599 (×1.35), exactly one
   `[EXPOSED] triggered` line per leg, `[EXPOSED] expire on Orc Enemy_boss`, post-expiry hit
   at hp 1589 (exact −10 unamplified).
4. VFX lifecycle bed:
   `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
   — exit 0, `status=pass`, 7/7 expectations including `exposed_vfx == true` during the Exposed
   window; screenshots/record_frames correctly skipped headless. Fresh result:
   `.gen/harness/exposed_plating_vfx/result.json`.

## Criterion-by-criterion

1. Perk registration / purchasable at 3 levels — **Done.** `scripts/progression/global.json`
   defines `exposed_plating` Common, maxLevels 3 (0.15/0.5s, 0.25/1s, 0.35/1.5s);
   `CurseProgressionManager.HANDLED` + `_apply_exposed_plating` apply absolute per-level figures;
   harness exercised apply_progression L1→L2→L3.
2. Once-per-shield-instance trigger (>0→0 only) — **Done.** Guard at `_consume_armor()`
   (`before > 0.0 and enemy.armor <= 0.0`); second hits against zero armor deal baseline damage
   with `exposed_multiplier == 1.0` (asserted in result.json expectations).
3. Multiplier per level for duration, clean expiry — **Done.** Exact hp deltas above match
   ×1.15/×1.25/×1.35; after 2s wait hp dropped exactly 10 (unamplified); expiry logged once.
4. ExposedStatus/ExposedVFX following BurnStatus/BurnVFX pattern — **Pending** (visual evidence
   only). Code exists: `scripts/game/status/ExposedStatus.gd`,
   `scripts/game/actors/effects/ExposedVFX.gd`, lazy `show_exposed()`/`hide_exposed()` in
   EffectsManager. Headless machine proof passes (`exposed_vfx == true` during window). Remaining:
   mandated windowed screenshots / real-30fps GIF + `ui_feels_broken` pass — manual-tester scope,
   not yet produced (no `.gen/manual-report.md`). Run `tests/scenarios/exposed_plating_vfx.json`
   windowed (`--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy`);
   captures land in `.gen/harness/exposed_plating_vfx/{shots,record}/`.
5. `[EXPOSED]` debug logging gated by `OS.is_debug_build()` — **Done.** Trigger/expiry lines all
   behind `OS.is_debug_build()`; observed in fresh run output.

## Changed files reviewed

Feature diff (git status): autoload/ProgressionManager.gd,
scripts/game/actors/effects/EffectsManager.gd,
scripts/game/actors/enemy/parts/EnemyHealthController.gd, scripts/progression/global.json,
scripts/progression/managers/CurseProgressionManager.gd, scripts/testing/AgentHarness.gd,
scripts/testing/HarnessValues.gd + new ExposedVFX.gd, ExposedStatus.gd,
tests/scenarios/exposed_plating_once_per_shield.json, exposed_plating_vfx.json. Identical to
revision-1 reviewed diff; only declared workflow artifacts additionally changed; no scope creep.

- Typed vars, guard clauses, small functions throughout new code; follows sibling perk patterns
  (frozen_fracture / static_breach). No coding-rules violation found in changed files.
- Test overlap: searched tests/scenarios — no prior exposed_plating coverage; both scenarios are
  new, non-overlapping, and each asserts its own criterion (exact hp deltas / multiplier values /
  vfx visibility), not mere execution.

## Quality notes

quality-notes.md did not exist; created with one advisory entry (Static Breach bypass) —
advisory only, does not demote any criterion.

## Blockers

None infra. Runner healthy throughout (probe, import gate, both harnesses exit 0).

## Unverified items

- Windowed/manual pixel evidence for criterion 4 (`ui_feels_broken` pass included) —
  manual-tester profile owns `.gen/manual-report.md`; not yet present.
