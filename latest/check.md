# Check report: issue-ground-material-ignores-cache (issue #115)

classification: fixable

## Verdict

All six acceptance criteria verified Done by fresh runner-based verification.
Classification `fixable` solely because the implementation remains **uncommitted**
in the worktree (`git status`: modified `scripts/game/Game.gd`,
`scripts/utils/EnvironmentUtils.gd`, `scripts/utils/TextureAtlasUtils.gd`;
untracked `tests/scenarios/ground_material_map_switch.json`,
`tests/scenarios/manual_ground_material_map_switch.json`). No build, test,
quality, or evidence failures were found; committing the change is a mechanical
fix.

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-ground-material-ignores-cache)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `["godot","--version"]` | 0 | Godot 4.4.1.stable.official.49a5bc7b6 |
| Typecheck/build + full suite | `["godot","--headless","--path",".","--editor","--quit-after","300"]` | 0 | Import/editor pass; no script parse errors |
| Focused harness | `["godot","--headless","--path",".","res://scenes/Main.tscn","--quit-after","6000","--","--harness=res://tests/scenarios/ground_material_map_switch.json"]` | 0 | `[Harness] status=pass exit=0` |

Fresh result: `.gen/harness/ground_material_map_switch/result.json` — `status:
"pass"`, both expectations pass
(`grass=ok dirt=ok tint=0.309804,0.498039,0.309804`; log contains `[GROUND]
ground material created`). Timeline executed map_1→map_2→map_1→map_2 (double
switch), all four load_map actions ok. Run log shows five ground-material
creations, each `[GROUND] ground material created:
from_cache=map_grass.jpg,underground_floor.jpg fresh=none`.

## Criteria evidence

1. CACHE_MODE_REUSE, no CACHE_MODE_IGNORE in ground path — Verified.
   TextureAtlasUtils `_load_ground_texture` loads both textures with
   `ResourceLoader.CACHE_MODE_REUSE`; grep finds no remaining
   CACHE_MODE_IGNORE in TextureAtlasUtils.gd / EnvironmentUtils.gd.
2. Root cause + sampler reassignment fix — Verified. Comment documents the
   cause; `EnvironmentUtils._update_ground_plane_color` re-asserts
   grass_albedo/dirt_albedo when missing, using CACHE_MODE_REUSE.
3. Non-empty albedo samplers after double map switch — Verified via harness
   expectation 1 (probe asserts both textures are Texture2D after 4 map loads).
4. grass_tint follows newly loaded map's config — Verified by same probe
   assertion (tint equals loaded map color 5209935). Advisory only: all shipped
   maps share this color, so distinctness across maps is untestable today —
   tracked in quality-notes (open, advisory).
5. Fresh harness result.json pass — Verified (path above, fresh timestamp).
6. Debug `[GROUND]` log per creation naming cached vs fresh — Verified in run
   log and log expectation; gated on `OS.is_debug_build()`.
7. Fallback regression check — Code-inspected: fallback branches (Grass.png
   tile atlas / StandardMaterial3D) untouched in diff; probe explicitly handles
   "not shader material". No automated test exercises the fallback branch —
   accepted as unchanged-code criterion per plan wording ("existing fallbacks
   unchanged").

## Changed-file quality findings

No rule violations found in the new code (typed GDScript, guard clauses,
debug-gated logging, no casts violating rules, surgical scope). Test-overlap
check: no pre-existing scenario asserted ground-material sampler state;
`tests/scenarios/ground_material_map_switch.json` is novel coverage.

Note (advisory): `Game.gd.__ground_shader_probe` is harness instrumentation
living in production script — consistent with the established AgentHarness
"source: game, field" pattern used by other scenarios in this repo; not a
violation.

## Blockers

None. Runner healthy throughout; all commands returned exit 0 on first attempt.

## Unverified items

- Manual windowed screenshot evidence (manual_testing: required) is owned by
  the manual-tester profile; not assessed here.
