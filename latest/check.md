# Check report — Exposed Plating perk (issue #89) — check (r3)

classification: pass

## Verdict

Fresh verification through the approved runner (`project=godot-td`,
`workspace=poke-defense-godot/issue-exposed-plating`) confirms all five headless
code criteria are green. The plan's `--check-only` typecheck command fails on
this project for ANY status script (including pre-existing `BurnStatus.gd`)
because `godot --check-only --script` does not resolve autoloads — it is a tool
limitation, not a code defect; the authoritative parse/compile gate is the
editor import + full harness run, both exit 0. The plan's "full test" is the
`exposed_plating_vfx` harness, which passes 16/16 actions with zero failures.
Per request.md, headless green ⇒ checker writes `classification: pass` so the
leader can dispatch manual-tester; missing windowed PNGs/GIFs and the
`ui_feels_broken` verdict are manual-tester deliverables and remain Pending,
not code-check fixable.

## Verification commands (all via run_project_cmd, runner-reported exit codes)

1. Probe: `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
2. Editor import/parse gate: `["godot","--headless","--path",".","--editor","--quit-after","300"]`
   — exit 0 (9.2s), no script errors.
3. Focused semantics harness:
   `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]`
   — exit 0, result `.gen/harness/exposed_plating_once_per_shield/result.json`:
   `status=pass`, 38 actions, 0 failed. Log shows per-level legs L1 1625→1614→1603
   (×1.15), L2 →1613→1601 (×1.25), L3 →1612→1599 (×1.35); exactly one
   `[EXPOSED] triggered ... level=N bonus=…% dur=…` line per leg; one trigger per
   shield instance (`exposed_count == 1`); post-expiry hit at hp 1589 = exact −10
   unamplified after `[EXPOSED] expire`.
4. VFX lifecycle harness ("full test" per plan):
   `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]`
   — exit 0, result `.gen/harness/exposed_plating_vfx/result.json`: `status=pass`,
   16 actions, 0 failed, including `enemies.Orc Enemy_boss.exposed_vfx == true`
   during the Exposed window and screenshot/record_frames steps correctly skipped
   as `reason: headless`.
5. Plan's typecheck command: `["godot","--headless","--check-only","--script",
   "res://scripts/game/status/ExposedStatus.gd"]` — exit 1, `Identifier not found:
   SimulationClock`. Control run on the UNRELATED pre-existing
   `scripts/game/status/BurnStatus.gd` produces the identical error: the
   `--check-only --script` mode does not register autoload singletons in this
   project, so this command cannot validate any status script here. Gate treated
   as satisfied by the stronger editor-parse gate (step 2) plus harness compile
   and execution (steps 3–4). Not a feature failure.

## Criterion-by-criterion evidence

1. Perk registration / purchasable at 3 levels — Done. `scripts/progression/global.json`
   defines `exposed_plating` Common, maxLevels 3 (0.15/0.5s, 0.25/1s, 0.35/1.5s);
   harness exercised apply_progression across L1→L2→L3.
2. Once-per-shield-instance trigger (>0→0 only) — Done. Guard
   `if before > 0.0 and enemy.armor <= 0.0:` in `_consume_armor()`
   (EnemyHealthController.gd ~line 369); second hits against zero armor asserted
   baseline damage with `exposed_multiplier == 1.0`; re-trigger requires armor regain.
3. Multiplier per level for duration, clean expiry — Done. Exact hp deltas above
   match ×1.15/×1.25/×1.35; post-expiry hit unamplified; expiry logged once.
4. ExposedStatus/ExposedVFX following BurnStatus/BurnVFX pattern — Done at code
   level. `scripts/game/status/ExposedStatus.gd`, `scripts/game/actors/effects/
   ExposedVFX.gd`, lazy `show_exposed()`/`hide_exposed()` via EffectsManager;
   headless machine proof passes (`exposed_vfx == true` during window, clean state
   before breach). Remaining player-facing evidence moved to Pending (manual-tester).
5. `[EXPOSED]` debug logging gated by `OS.is_debug_build()` — Done. Trigger line
   (EnemyHealthController.gd `_apply_exposed_plating`) and expiry line
   (ExposedStatus.gd lines 34/54) behind `OS.is_debug_build()`; observed live in
   fresh run output.

## Test overlap check

Searched tests/scenarios: no prior exposed-plating coverage existed; both
scenarios are new, non-overlapping, and each asserts its own criterion (exact hp
deltas / multiplier values / once-per-instance counts / vfx visibility), not mere
execution.

## Changed files reviewed

git status: autoload/ProgressionManager.gd,
scripts/game/actors/effects/EffectsManager.gd,
scripts/game/actors/enemy/parts/EnemyHealthController.gd,
scripts/progression/global.json, scripts/progression/managers/CurseProgressionManager.gd,
scripts/testing/AgentHarness.gd, scripts/testing/HarnessValues.gd + new
ExposedStatus.gd, ExposedVFX.gd, and both test scenario JSONs. Identical to the
revision-1 reviewed diff; only declared workflow artifacts additionally changed;
no scope creep. New code follows sibling-perk patterns, typed vars/guard clauses;
no coding-rules violation found.

## Quality notes

quality-notes.md has one open advisory entry (static-breach-bypass): Static
Breach zeroes armor without passing through `_consume_armor()`, so a Static-Breach
shatter does not open an Exposed window. Issue wording pins the trigger site to
`_consume_armor()`, so behavior appears intended; advisory only, does not demote
any criterion. No new entries appended.

## Blockers

None infra. Runner healthy throughout (probe, import gate, both harnesses exit 0).
Remaining Pending work belongs to the manual-tester profile (windowed captures,
30fps record_frames recording, `ui_feels_broken` verdict into
`.gen/manual-report.md`).

## Unverified items

- Windowed visual captures / real-time recording / UI sanity verdict (manual-tester scope).
- The plan's literal `--check-only` typecheck command (tool limitation documented above; superseded by stronger gates).
