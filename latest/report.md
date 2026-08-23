# Team-leader report

- **Result:** failed
- **Classification:** pass
- **Feature:** grass-mutates-shared-materials
- **Run:** grass-shared-materials-r1
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

# Acceptance Plan: grass-mutates-shared-materials

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://tests/visuals/test_small_vegetation_render_settings.tscn"]`
- Full test: `["godot", "--headless", "--path", ".", "res://tests/visuals/test_nature_visibility_range.tscn"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import"]`

## Clusters

1. grass-material-duplication-and-recursion — files: `scripts/game/NatureDecoration.gd`, `tests/visuals/test_small_vegetation_render_settings.gd`, `tests/visuals/test_small_vegetation_render_settings.tscn` — depends on: none
- The grass/flower path duplicates any StandardMaterial3D it modifies before writing to it, so the material resource cached by the imported model is left unchanged after decoration generation.
- After the grass/flower path runs, each affected MeshInstance3D surface carries its own modified copy via set_surface_override_material, with transparency alpha-scissor, alpha scissor threshold applied, no depth test disabled, and render priority -1.
- Two grass or flower decorations generated from the same model do not share one modified material instance between them.
- The small-vegetation mesh walk reaches MeshInstance3D nodes nested deeper than direct children of the passed node (e.g. inside a sub-node of a nature model), applying shadow-off, no distance cull, opaque sorting, and the alpha-scissor material settings to them.
- A MeshInstance3D under the grass path still has cast_shadow off and visibility_range_end 0 after the fix.
- Debug-build [NatureDecoration] log line per small-vegetation mesh whose material is duplicated, naming the node and surface index.
- The existing nature visibility-range regression test still passes unchanged after the rewrite.

## Criteria

## ✅ Done
- The grass/flower path duplicates any StandardMaterial3D it modifies before writing to it, so the material resource cached by the imported model is left unchanged after decoration generation.
- After the grass/flower path runs, each affected MeshInstance3D surface carries its own modified copy via set_surface_override_material, with transparency alpha-scissor, alpha scissor threshold applied, no depth test disabled, and render priority -1.
- Two grass or flower decorations generated from the same model do not share one modified material instance between them.
- The small-vegetation mesh walk reaches MeshInstance3D nodes nested deeper than direct children of the passed node (e.g. inside a sub-node of a nature model), applying shadow-off, no distance cull, opaque sorting, and the alpha-scissor material settings to them.
- A MeshInstance3D under the grass path still has cast_shadow off and visibility_range_end 0 after the fix.
- Debug-build [NatureDecoration] log line per small-vegetation mesh whose material is duplicated, naming the node and surface index.
- The existing nature visibility-range regression test still passes unchanged after the rewrite.

## ⬜ Pending
- Windowed (never --headless) manual screenshot of a gameplay map shows grass/flowers rendering correctly with cutout foliage edges and casting no shadows.

## ❌ Impossible

classification: pass

## Check

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
