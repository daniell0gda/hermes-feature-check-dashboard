# Check report: issue-ground-material-ignores-cache (revision-check-1)

classification: pass

## Verdict

All six acceptance criteria verified Done by fresh runner-based verification
(iteration 2, revision-check-1). The implementation is unchanged from the r1
redo; all gates re-run green in this session via run_project_cmd.

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-ground-material-ignores-cache)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `["godot","--version"]` | 0 | Godot 4.4.1.stable.official.49a5bc7b6 |
| Typecheck/build + full suite | `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | Import/editor pass, no script parse errors |
| Focused harness | `["godot","--headless","--path",".","res://scenes/Main.tscn","--quit-after","6000","--","--harness=res://tests/scenarios/ground_material_map_switch.json"]` | 0 | `[Harness] status=pass exit=0` |

Fresh result `.gen/harness/ground_material_map_switch/result.json`: `status:
"pass"`, both expectations pass (`grass=ok dirt=ok tint=0.309804,0.498039,0.309804`
and log contains `[GROUND] ground material created`), timeline
map_1→map_2→map_1→map_2 with all four load_map actions ok. Run log shows five
ground-material creations, each `[GROUND] ground material created:
from_cache=map_grass.jpg,underground_floor.jpg fresh=none`. Pre-existing
invalid-UID theme warnings and dummy-renderer exit leak noise are unrelated to
this change.

## Criteria evidence

1. CACHE_MODE_REUSE, no CACHE_MODE_IGNORE — Verified. Fresh grep: the only
   remaining `CACHE_MODE_IGNORE` occurrence in TextureAtlasUtils.gd /
   EnvironmentUtils.gd is inside an explanatory comment (line 142); both ground
   textures load through `_load_ground_texture` with `CACHE_MODE_REUSE`.
2. Root cause + sampler reassignment fix — Verified. Diff shows
   `EnvironmentUtils._update_ground_plane_color` now re-asserts grass_albedo /
   dirt_albedo (with CACHE_MODE_REUSE loads) when a sampler is missing.
3. Non-empty albedo samplers after double map switch — Verified by harness
   expectation 1 (`grass=ok dirt=ok` after four map loads).
4. grass_tint follows newly loaded map's config — Verified by same expectation
   (tint = loaded map color 5209935). Advisory: all shipped maps share this
   color, so distinctness across maps is untestable today — tracked as open
   advisory note `ground-map-tint-distinctness`.
5. Fresh harness result.json pass — Verified (status "pass", finished_at
   2026-08-24T05:12:54).
6. Debug `[GROUND]` log naming cached vs fresh — Verified in run log; gated on
   `OS.is_debug_build()`; five creation events logged.
7. Fallback regression — Code-inspected: fallback branches (Grass.png tile
   atlas / StandardMaterial3D) untouched in diff; probe handles non-shader
   materials. No dedicated automated test exercises the fallback branch;
   accepted per plan wording ("existing fallbacks unchanged").

## Changed-file quality findings

No rule violations in the new code against /opt/data/coding_rules.md or
worktree CLAUDE.md: typed GDScript, guard clauses, debug-gated `[GROUND]`
logging per CLAUDE.md logging rule, surgical scope. Test-overlap check: no
pre-existing scenario asserted ground-material sampler state; the new scenario
is novel coverage. Note (advisory): `Game.gd.__ground_shader_probe` is harness
instrumentation living in a production script, consistent with the repo's
established AgentHarness pattern; not a violation. Worktree diff remains
uncommitted (3 modified files, 2 untracked scenarios) — commit is the leader's
merge step, not a quality failure.

## Blockers

None. All commands exited 0 on first attempt.

## Unverified items

- Manual windowed screenshot evidence (manual_testing: required) is owned by
  the manual-tester profile; not assessed here.
