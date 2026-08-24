# Check report — issue-exposed-plating (revision-check-1, iteration 2)

classification: pass

## Verdict

All nine acceptance criteria verified Done with fresh evidence through
`run_project_cmd` (project=godot-td,
workspace=poke-defense-godot/issue-exposed-plating). No host-shell Godot was
used. The revision fixed the previously missing player-facing proof: the
worktree's `models/glb/Orc Enemy.glb` was a 131-byte git-LFS pointer (git-lfs
absent in the runner image), so the boss rendered invisible and no wash could
be seen. The real 146,420-byte blob is restored, the `.import` remap repaired,
and fresh windowed close-up captures show the amber wash clearly.

## Verification commands (all via run_project_cmd, exit codes from the runner)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `["godot","--version"]` | 0 | Godot 4.4.1.stable.official.49a5bc7b6 |
| Typecheck/build | `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | Import/scan OK; pre-existing invalid-UID warnings in legacy `.tres/.tscn` only |
| Focused test | `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_once_per_shield.json"]` | 0 | `[Harness] status=pass exit=0`; fresh `.gen/harness/exposed_plating_once_per_shield/result.json`, 6/6 expectations pass |
| Full test | `["godot","--headless","--path",".","res://scenes/Main.tscn","--audio-driver","Dummy","--","--harness=res://tests/scenarios/exposed_plating_vfx.json"]` | 0 | `[Harness] status=pass exit=0`; fresh `.gen/harness/exposed_plating_vfx/result.json`, 7/7 expectations pass incl. log `contains [EXPOSED] triggered on` and `[EXPOSED] expire on` |

Fresh focused log shows the full behavior chain live at all three levels:
`[Armor] Orc Enemy_boss depleted: 60.0 armor removed by 60.0 armor damage` →
`[EXPOSED] trigger on Orc Enemy_boss dur=0.5/1.0/1.5` →
`[EXPOSED] triggered on … level=1/2/3 bonus=15%/25%/35%` → exactly one trigger
per shield instance → `[EXPOSED] expire on Orc Enemy_boss` →
`exposed_multiplier` back to 1.0 (`exposed == false`). The log also shows
`[ENEMY DEBUG] Loaded GLB model: res://models/glb/Orc Enemy.glb` (previously
"GLB not found"), confirming the model fix is live.

Windowed evidence (from the revision's windowed run, exit 0, status=pass,
`record_frames captured=6 saved=6`): `.gen/screenshots/` contains
`before_breach_no_wash.png`, `exposed_wash_on_breach.png`,
`wash_cleared_after_expiry.png`, `exposed_window.gif` (ffmpeg 6 fps from the 6
real consecutive record frames in `.gen/harness/exposed_plating_vfx/record/`).
Checker eye-verification of the fresh PNGs: before = natural green body,
during = unmistakable bright amber/orange wash over the whole body, after =
green again matching before; enemy fills a large part of the frame
(camera_focus distance 3.0), debug panel not covering the enemy.
`.gen/manual-report.md` exists, embeds the evidence, and ends with
`ui_feels_broken: no`.

## Acceptance criteria status

All nine plan criteria Done:
1–2. Fresh focused harness pass — perk registered (`exposed_plating == 3`
progression expectation), trigger exactly once per >0→0 transition
(`EnemyHealthController._consume_armor` gated on `before > 0.0 and
armor <= 0.0`).
3. Multiplier applies for the per-level duration then expires cleanly
(`exposed_multiplier == 1.0`, `exposed == false` after `[EXPOSED] expire on`).
4. `ExposedStatus`/`ExposedVFX` following the BurnStatus/BurnVFX pattern,
lazily instantiated by `EffectsManager.show_exposed()`; `exposed_vfx`
expectation true during the window.
5. `[EXPOSED]` logging gated by `OS.is_debug_build()` — log-expectation
assertions pass in both fresh headless runs.
6. Player-facing proof — close-up framing, hidden debug panel, three stills
(green / amber / green, eye-verified by this checker on the fresh PNGs),
6-frame real `record_frames` GIF, PNGs+GIF in `.gen/screenshots/`, embedded in
`.gen/manual-report.md` ending `ui_feels_broken: no`. The strengthen clause was
not needed: root cause was the missing GLB, not weak VFX.

## Changed-file quality findings

Diff vs HEAD: 9 modified files + new `ExposedVFX.gd`, `ExposedStatus.gd`, two
scenario JSONs, restored GLB.
- No type-cast violations; enums referenced by identifier; surgical scope;
  ExposedVFX follows the established StaticBreachVFX/BurnVFX material-override
  pattern; config access mirrors the existing `get_frozen_fracture_config`
  shape.
- New scenario tests do not overlap existing coverage — no other scenario
  exercises `exposed_plating`.
- Minor advisory only: `var _saved_camera_transform` declared mid-file between
  methods in `HarnessActions.gd` (GDScript-legal, unconventional placement).
- Pre-existing invalid-UID warnings in legacy `.tres/.tscn` files are unrelated
  legacy state, not part of this diff.

## Quality notes

Re-checked `.gen/quality-notes.md`: entry `static-breach-bypass` (iteration 2)
is advisory and still open — Static Breach's separate armor-zero path still
bypasses `_consume_armor()`, but issue wording pins the trigger site to
`_consume_armor()`, so behavior appears intended. Left open for leader
confirmation; no new entries appended. No scope creep found in the diff; the
GLB restore and `.import` repair were necessary setup fixes for this issue's
own evidence, not unrelated changes.

## Blockers

None. Runner reachable and used for every project command; no host-shell Godot.
