# Coder report: implementation — revision 2

## Scope of this iteration

Revision 2 instruction: "Redo the failed criteria, then wait for check."
The single Pending/failed item is criterion 4's mandated player-facing windowed
VFX evidence (screenshots / real-30fps `record_frames` GIF + `ui_feels_broken`
pass). That evidence is manual-tester-profile scope: this runner worker is
headless-only, so no code change can produce it here. This iteration therefore
redid the full automated verification of the implementation from a clean
worker to prove nothing regressed, and refreshed all `.gen` harness evidence.

No source files were changed in this iteration (git status identical to
revision 1's reviewed diff).

## Changed files
- (none this iteration)

## Criteria
- 1. Perk registered / purchasable at 3 levels — Done (unchanged)
- 2. Once-per-shield-instance trigger (>0→0 only) — Done (unchanged)
- 3. Multiplier per level for duration, clean expiry — Done (unchanged)
- 4. ExposedStatus/ExposedVFX pattern — code Done; windowed pixel evidence still owned by manual-tester profile (headless machine proof passes)
- 5. `[EXPOSED]` debug logs gated by `OS.is_debug_build()` — Done (unchanged)

## Commands and results (all via run_project_cmd, project=poke-defense-godot)
- `["godot","--version"]` — exit 0; Godot 4.4.1.stable.official.49a5bc7b6.
- `["git","status","--short"]` — exit 0; exactly the 7 modified + 4 new feature files from the reviewed diff, nothing new.
- Focused semantics harness:
  `godot --headless --path . res://scenes/Main.tscn --audio-driver Dummy -- --harness=res://tests/scenarios/exposed_plating_once_per_shield.json`
  — exit 0, status=pass, 6/6 expectations. Run log confirms per-level legs:
  L1 hp 1625→1614→1603 (×1.15), L2 →1613→1601 (×1.25), L3 →1612→1599 (×1.35);
  one `[EXPOSED] triggered` line per leg; after expiry wait hp 1589 (exact −10,
  unamplified) and `[EXPOSED] expire on Orc Enemy_boss`. Fresh result:
  `.gen/harness/exposed_plating_once_per_shield/result.json`.
- VFX lifecycle bed:
  `godot --headless --path . res://scenes/Main.tscn --audio-driver Dummy -- --harness=res://tests/scenarios/exposed_plating_vfx.json`
  — exit 0, status=pass, 7/7 expectations (`exposed_vfx` asserted true during
  the Exposed window); screenshots and record_frames correctly skipped headless.
  Fresh result: `.gen/harness/exposed_plating_vfx/result.json`.

## Notes / handoff for tester
- Remaining blocker to Done on criterion 4 is ONLY windowed evidence. Run
  `tests/scenarios/exposed_plating_vfx.json` windowed, e.g.:
  `godot --rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy res://scenes/Main.tscn -- --harness=res://tests/scenarios/exposed_plating_vfx.json`
  Captures land in `.gen/harness/exposed_plating_vfx/{shots,record}/`
  (`before_breach_no_wash`, `exposed_wash_on_breach`, `wash_cleared_after_expiry`,
  plus real engine frames for GIF export). End with `ui_feels_broken: yes|no`.
- Gotchas unchanged from revision 1: breaching hit itself IS amplified; Static
  Breach armor-zero path bypasses `_consume_armor` so it never fires Exposed;
  harness runs need the explicit scene arg before `--`; enemy model GLB missing
  in this worktree is pre-existing and unrelated (fallback path exercised).
