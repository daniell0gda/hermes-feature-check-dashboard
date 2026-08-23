# Check Report: grass-mutates-shared-materials (issue #111) — iteration 1 (revision-check)

classification: pass

## Verdict

The fix is implemented and verified fresh in this worktree via run_project_cmd
(project=poke-defense-godot, workspace=poke-defense-godot/issue-grass-render-settings-mutate-the-shared-).
All automated criteria pass; only the manual windowed screenshot criterion remains open (manual_testing: required — owned by the manual-tester profile).

## Implementation evidence

- `scripts/game/NatureDecoration.gd` — `_apply_small_vegetation_render_settings` now delegates to a recursive `_apply_small_vegetation_render_settings_recursive`. Per surface it duplicates the StandardMaterial3D before modification and writes the copy back with `set_surface_override_material` (alpha-scissor transparency, threshold 0.3, no_depth_test false, render_priority -1). Shadow-off, visibility_range_end 0.0, opaque sorting applied to every nested MeshInstance3D. Debug-build `[NatureDecoration] duplicated material for <node> surface <i>` log present.
- `tests/visuals/test_small_vegetation_render_settings.gd/.tscn` — new focused test asserting all of the above plus cached-material untouched and per-instance independence.

## Verification commands (all via run_project_cmd)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `godot --version` | 0 | 4.4.1.stable.official |
| Build/import | `godot --headless --path . --import` | 0 | Clean import |
| Focused test | `godot --headless --path . res://tests/visuals/test_small_vegetation_render_settings.tscn` | 0 | `=== small_vegetation_render_settings: 16 ok, 0 failed ===`; debug log lines fired (`[NatureDecoration] duplicated material for Blades surface 0`) |
| Full test | `godot --headless --path . res://tests/visuals/test_nature_visibility_range.tscn` | 0 | `=== nature_visibility_range: 5 ok, 0 failed ===`; exit-time dummy-renderer RID leak warnings are baseline headless noise, not failures |

Runner gate: passed (every project command through run_project_cmd; no host Godot).

## Acceptance criteria status

Criteria 1–7 verified Done (see status.md). Criterion 8 (windowed manual screenshot) left Pending — manual_testing required; not executable headless.

Test overlap check: no pre-existing test asserted these behaviors (the scenario is new; the existing nature_visibility_range test covers culling only and was rerun unchanged, passing).

Changed-code quality: diff vs HEAD touches only NatureDecoration.gd + the two new test files; mirrors the existing tree-path pattern, matches existing style, no scope creep. No violations under /opt/data/coding_rules.md or CLAUDE.md. quality-notes.md: created with no open entries.

## Blockers

None infra-related.

## Unverified items

- Criterion 8: windowed (never --headless) manual screenshot of a gameplay map showing grass/flowers with cutout foliage edges and no shadows — deferred to the manual-testing pass.
