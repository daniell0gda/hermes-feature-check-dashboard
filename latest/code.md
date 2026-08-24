# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- none this iteration (re-verification pass; the uncommitted feature diff from
  revision 1 is preserved intact: autoload/ProgressionManager.gd,
  scripts/game/actors/effects/EffectsManager.gd, ExposedVFX.gd,
  scripts/game/status/ExposedStatus.gd, EnemyHealthController.gd,
  scripts/progression/global.json, CurseProgressionManager.gd,
  HarnessValues.gd / AgentHarness.gd, both test scenarios)

## Criteria
- Perk registered in progression config, purchasable at 3 levels — Done
- Trigger fires exactly once per shield instance (>0 → 0 only) — Done
- Multiplier per level for duration, clean expiry — Done
- ExposedStatus/ExposedVFX per BurnStatus/BurnVFX pattern — code Done;
  player-facing windowed evidence remains manual-tester scope (cluster 3)
- `[EXPOSED]` debug logging gated by OS.is_debug_build() — Done

## Commands and results (all via run_project_cmd, project=godot-td)
- `["godot","--version"]` — exit 0; Godot 4.4.1.stable.official.49a5bc7b6
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0 (11.4s); no script errors; ExposedStatus/ExposedVFX register as global classes
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]` — exit 0, status=pass, 6/6 expectations. Per-level hp legs: L1 1625→1614→1603 (×1.15), L2 →1613→1601 (×1.25), L3 →1612→1599 (×1.35); one `[EXPOSED] triggered` line per leg with level+duration; `[EXPOSED] expire on Orc Enemy_boss`; post-expiry hit at hp 1589 (exact −10). Result: `.gen/harness/exposed_plating_once_per_shield/result.json`
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]` — exit 0, status=pass, 7/7 including exposed_vfx true during the window; record_frames correctly skipped headless. Result: `.gen/harness/exposed_plating_vfx/result.json`
- `["godot","--headless","--check-only","--script","res://scripts/game/status/ExposedStatus.gd"]` — exit 1 "Identifier not found: SimulationClock"; the pre-existing `BurnStatus.gd` fails identically under this mode. `--check-only --script` does not register project autoloads in Godot 4.4, so it cannot validate any autoload-dependent script. Import gate + full-scene harnesses are the compile evidence.

## Notes
- No source changes were needed or made; fresh post-rebase verification is green.
- Pre-existing unrelated warnings/errors observed on every run (invalid UID ext_resources in HudTheme/UI.tscn, missing GLBs incl. `Orc Enemy.glb`, exit-time RID leak noise under Dummy renderer) — present on master paths, not introduced by this feature.
- Manual-tester still owns cluster 3: run `exposed_plating_vfx` windowed (`--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy`) for shots + real 30fps record_frames GIF into `.gen/harness/exposed_plating_vfx/{shots,record}/`, then record `ui_feels_broken: yes|no`.
\n