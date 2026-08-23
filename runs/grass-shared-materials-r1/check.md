# Check Report: grass-mutates-shared-materials (issue #111) — iteration 2

classification: pass

## Verdict

Fresh verification in this worktree via run_project_cmd
(project=poke-defense-godot, workspace=poke-defense-godot/issue-grass-render-settings-mutate-the-shared-)
confirms all automated acceptance criteria. The only open criterion is the
windowed manual screenshot (manual_testing: required — owned by the manual-tester
profile; not executable headless), which stays Pending.

## Implementation evidence

- `scripts/game/NatureDecoration.gd` — `_apply_small_vegetation_render_settings`
  now delegates to recursive `_apply_small_vegetation_render_settings_recursive`.
  Per surface it duplicates the StandardMaterial3D before modification and writes
  it back via `set_surface_override_material` (TRANSPARENCY_ALPHA_SCISSOR,
  threshold 0.3, no_depth_test false, render_priority -1). Shadow-off,
  visibility_range_end 0.0, opaque sorting applied to every nested MeshInstance3D.
  Debug-build `[NatureDecoration] duplicated material for <node> surface <i>` log present.
- `tests/visuals/test_small_vegetation_render_settings.gd/.tscn` — new focused test
  asserting nested reachability, cached-material immutability, per-instance material
  independence, plus all alpha-scissor/shadow/cull settings.

## Verification commands (all via run_project_cmd)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `godot --version` | 0 | 4.4.1.stable.official |
| Build/import | `godot --headless --path . --import` | 0 | Clean import, no errors |
| Focused test | `godot --headless --path . res://tests/visuals/test_small_vegetation_render_settings.tscn` | 0 | `=== small_vegetation_render_settings: 16 ok, 0 failed ===`; debug duplication logs fired for each scenario (`[NatureDecoration] duplicated material for Blades surface 0`) |
| Full test | `godot --headless --path . res://tests/visuals/test_nature_visibility_range.tscn` | 0 | `=== nature_visibility_range: 5 ok, 0 failed ===`; dummy-renderer RID warnings at exit are baseline headless noise |

Runner gate: passed — every project command ran through run_project_cmd; no host Godot.

## Acceptance criteria status

Criteria 1–7 verified Done (see status.md). Criterion 8 (windowed manual
screenshot) remains Pending: it requires a windowed (never --headless) run and is
assigned to the manual-testing pass.

Test overlap check: searched the existing suite — no pre-existing test asserted
material duplication or nested-walk behavior; the new scenario is non-overlapping.
The existing nature_visibility_range test was rerun unchanged and passes.

Changed-code quality: diff vs HEAD touches only NatureDecoration.gd + the two new
test files. Typed variables throughout, debug-build tagged log line, mirrors the
existing tree-path pattern, surgical scope. No violations under
/opt/data/coding_rules.md or CLAUDE.md. quality-notes.md has no open entries.

## Blockers

None infra-related.

## Unverified items

- Criterion 8: windowed manual screenshot of a gameplay map showing grass/flowers
  with cutout foliage edges and no shadows — deferred to manual testing.
