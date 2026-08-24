# Check report — issue-exposed-plating (iteration: check)

classification: fixable

## Verdict

Headless code work is verified green through `run_project_cmd`
(project=godot-td, workspace=poke-defense-godot/issue-exposed-plating). All
windowed close-up visual-evidence criteria remain Pending: they belong to the
manual-tester profile (no `.gen/screenshots/`, no `.gen/manual-report.md`
exist). This keeps them honestly open instead of claiming player-facing
proof that does not exist.

## Verification commands (all via run_project_cmd)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `["godot","--version"]` | 0 | Godot 4.4.1.stable |
| Typecheck/build | `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | Import/scan OK; pre-existing invalid-UID warnings in legacy `.tres/.tscn` files only |
| Focused test | `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]` | 0 | `[Harness] status=pass exit=0`; fresh result at `.gen/harness/exposed_plating_once_per_shield/result.json`, all 6 expectations pass |
| Full test | `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]` | 0 | `[Harness] status=pass exit=0`; fresh result at `.gen/harness/exposed_plating_vfx/result.json`, all 7 expectations pass incl. log `contains [EXPOSED] triggered on` and `[EXPOSED] expire on` |

Fresh run logs show the full behavior chain live:
`[Armor] Orc Enemy_boss depleted` → `[EXPOSED] trigger on Orc Enemy_boss dur=…`
→ `[EXPOSED] triggered on … level=1/2/3 bonus=15%/25%/35% dur=0.5/1.0/1.5` →
one trigger per shield instance → `[EXPOSED] expire on Orc Enemy_boss`.
`exposed_damage_multiplier()` returns 1.0 after expiry (expectation
`exposed_multiplier == 1.0`, `exposed == false`).

## Acceptance criteria status

Done (headless-proven this run):
1. Perk registered / purchasable at 3 levels — progression expectation
   `exposed_plating == 3` passed in focused harness.
2. Trigger exactly once per >0→0 transition — fresh harness pass; code path in
   `EnemyHealthController._consume_armor` gated on `before > 0.0 and armor <= 0.0`.
3. Multiplier applies for duration then expires cleanly — `exposed_multiplier`
   back to 1.0 and `[EXPOSED] expire on` logged in fresh run.
4. VFX via ExposedStatus/ExposedVFX lazily instantiated by EffectsManager —
   vfx harness passes (`exposed_vfx` expectation true during window).
5. Debug logging `[EXPOSED]` gated by `OS.is_debug_build()` — log-expectation
   assertions passed in both fresh runs.

Pending (manual-tester scope, unchanged from prior status):
- Windowed camera_focus close-up framing before screenshot checkpoints.
- Debug panel hidden/not covering enemy in captures.
- `record_frames` spanning the whole Exposed window (>0 real consecutive frames).
- Visible amber wash "during" vs "before"/"after" stills by eye.
- PNGs/GIF copied to `.gen/screenshots/` and embedded in `.gen/manual-report.md`
  ending with a `ui_feels_broken: yes|no` verdict line.

## Changed-file quality findings

Reviewed diff vs HEAD (8 modified files + ExposedStatus.gd, ExposedVFX.gd,
two scenario JSONs):
- No type-cast violations, enum-by-identifier respected, surgical scope,
  clean-code structure consistent with BurnStatus/BurnVFX pattern.
- Minor style only: `var _saved_camera_transform` declared mid-file between
  methods in `HarnessActions.gd`; GDScript-legal but unconventional placement.
  Advisory, not demoting.
- New scenario tests do not duplicate existing suite coverage (no other
  scenario exercises `exposed_plating`).
- Pre-existing editor parse error in `tools/reimport_buildings.gd` noted by the
  plan did not appear in this fresh import gate output; unrelated legacy file,
  not part of this diff.

## Quality notes

Re-checked `.gen/quality-notes.md`: entry `static-breach-bypass` (iteration 2)
is advisory and its condition is unchanged — Static Breach's separate armor-zero
path still bypasses `_consume_armor()`. Issue wording pins the trigger site to
`_consume_armor()`, so behavior appears intended; left open for leader
confirmation. No new entries appended.

## Blockers

None. Runner reachable and used for every project command; no host-shell Godot.
