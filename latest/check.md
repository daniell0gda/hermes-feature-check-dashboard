# Check report: revision-check-2 (iteration 4)

classification: fixable

## Verdict

All headless verification passes through `run_project_cmd`. The code-level
grounded-continent criteria are genuinely met and evidenced by the focused
harnesses. The two windowed/manual visual criteria remain Pending: the only
windowed screenshot on disk is stale (predates the current code) and shows the
map floating on plain sky with no globe — i.e. it does not evidence the fixed
behavior. No `.gen/manual-report.md` exists. This is missing manual evidence,
not a code or infra failure → `fixable`.

## Verification commands (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-earth-continent-map-integration)

1. Preflight `["git","status","--short"]` — exit 0.
2. Build/import gate `["godot","--headless","--path",".","--editor","--quit-after","300"]`
   — exit 0, import clean, no script errors.
3. Focused harness `backdrop_earth_visible.json` — exit 0, status=pass; all 8
   expectations green including present=true, grounded=true,
   center_y=-0.0000128 (== 0 with harness tolerance), horizon_in_view=true,
   centered_on_board=true, rotation_invariant=true. Log line:
   `[BACKDROP EARTH] grounded continent=Continent_Africa pos=(-21.2, -83.7824, -49.904) scale=41.5999984741211 rot_deg=(15.38803, 21.66841, 83.15345)`.
4. Focused harness `backdrop_earth_glint.json` — exit 0, status=pass;
   present/grounded/center_y==0/horizon_in_view all green.
5. Bounded spot-check set (`backdrop_earth_visible`, `backdrop_earth_glint`,
   `menu_backdrop_map`, `smoke_placement`, `removed_tower_kinds_no_crash`)
   via the plan's python3 loop — exit 0, all five PASS.

## Criterion-by-criterion evidence

- Continent naming + debug warning — Done. `BackdropEarth.gd` line 20:
  `GROUNDED_CONTINENT = "Continent_Africa"`; `_verify_continent_mesh()`
  emits debug-build `push_warning("[BACKDROP EARTH] grounded continent mesh not found: ...")`
  when absent from the instantiated GLB. Harness log confirms the mesh exists.
- Applied scale formula — Done. `_place_earth_grounded()`:
  `scale_factor = world_radius / NATIVE_EARTH_RADIUS * grounded_scale`,
  baked into basis as `yaw_basis.scaled(Vector3.ONE * scale_factor)`;
  grounding log shows scale=41.6 (not unit).
- Flush placement (center_y == 0) — Done. Harness expectation green in both
  scenarios; actual -0.0000128 is float noise around 0; sink depth derived
  from measured GLB apex GROUNDED_CONTINENT_APEX = 2.014.
- Horizon inside gameplay camera frustum — Done at harness level
  (`horizon_in_view == true`, `centered_on_board == true` in
  backdrop_earth_visible). Rig center (-21.2, -83.7824, -49.904), body radius
  ~83.2, limb crosses y=0 just past the far field edge per code comments.
  Windowed visual confirmation still pending (see Pending).
- Spin never starts in grounded config — Done. Grounded path calls
  `_place_earth_grounded()` which never calls `_start_earth_spin()`
  (only `_place_earth_floating()` does). Harness asserts
  `rotation_invariant == true` sampling the body transform twice ~3 s apart.
- Debug `[BACKDROP EARTH]` grounding log line — Done. Observed live in every
  run this iteration with continent name, pos, scale, rot_deg.
- Both focused harnesses pass headless with expectations green — Done (commands
  3–4 above).

## Pending items (why)

- Windowed backdrop look (other continents/ocean/clouds/atmosphere rim visible)
  — Pending on evidence: only screenshot on disk is
  `.gen/harness/backdrop_earth_visible/shots/surface_earth_backdrop.png`
  dated 2026-08-22 19:43, BEFORE BackdropEarth.gd (08-23 01:21) and Game.gd
  (01:52); image inspection shows map on plain sky, no globe — stale evidence
  of the pre-fix state.
- Windowed screenshot showing map sitting on the chosen continent blending into
  the map field — same reason; requires a fresh windowed run of
  `backdrop_earth_visible` from current code plus `.gen/manual-report.md`.

## Changed-file quality findings

No new rule violations found in the feature diff for this iteration's files
(`scripts/game/visuals/BackdropEarth.gd`, scenario JSONs, `Game.gd`,
`HarnessValues.gd`). Typed variables used throughout changed functions; debug
logging follows the CLAUDE.md `[TAG]` convention; no leaked nodes observed.
Carried-over advisory notes remain open in quality-notes.md (glb replacement,
balance CSV regeneration — unrelated shared files, do not demote criteria).

## Blockers

None. Runner worked normally throughout; no host-shell Godot was used.

## Unverified items

The two windowed/manual criteria above. Full-suite timeout remains a known
limitation per request.md check-scope bound (recorded in quality-notes.md,
iteration 3 entry).
