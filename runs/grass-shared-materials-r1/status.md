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

## ⬜ Pending
- The grass/flower path duplicates any StandardMaterial3D it modifies before writing to it, so the material resource cached by the imported model is left unchanged after decoration generation.
- After the grass/flower path runs, each affected MeshInstance3D surface carries its own modified copy via set_surface_override_material, with transparency alpha-scissor, alpha scissor threshold applied, no depth test disabled, and render priority -1.
- Two grass or flower decorations generated from the same model do not share one modified material instance between them.
- The small-vegetation mesh walk reaches MeshInstance3D nodes nested deeper than direct children of the passed node (e.g. inside a sub-node of a nature model), applying shadow-off, no distance cull, opaque sorting, and the alpha-scissor material settings to them.
- A MeshInstance3D under the grass path still has cast_shadow off and visibility_range_end 0 after the fix.
- Debug-build [NatureDecoration] log line per small-vegetation mesh whose material is duplicated, naming the node and surface index.
- The existing nature visibility-range regression test still passes unchanged after the rewrite.

## ❌ Impossible
