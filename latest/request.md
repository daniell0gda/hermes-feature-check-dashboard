# Request: grass-mutates-shared-materials (issue #111)

- Project: poke-defense-godot
- Runner key: `godot-td` (never use folder name)
- Workspace: `poke-defense-godot/issue-grass-render-settings-mutate-the-shared-`
- Branch: `issue/grass-render-settings-mutate-the-shared-` (cut from fresh origin/master)
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/111

## Problem

`NatureDecoration._apply_small_vegetation_render_settings()` edits the material it reads off
the imported mesh without duplicating it first (`NatureDecoration.gd:488-497`). `surface_get_material()`
returns the ResourceLoader-cached resource owned by the imported `.gltf`, shared with every instance of
that model and every later scene that loads it. The tree path directly below duplicates before writing
(`_apply_tree_render_settings_recursive`, `:740`) — the grass path must do the same.

Second latent issue in the same function: it only walks direct children (`:473-474`) where the tree
version recurses; a nested nature model would silently skip shadow/culling/alpha settings.

## Done when

1. The grass/flower path duplicates the material before writing to it and writes the duplicate back
   with `set_surface_override_material` (mirroring the tree path).
2. The mesh walk recurses so nested nature models get the same treatment.
3. Grass still renders with alpha-scissor cutout and no shadow casting — verified on a `-Windowed`
   screenshot (rendering change → manual_testing: required).

## Redo notes for resumed runs

- Runner workspace name is exactly `poke-defense-godot/issue-grass-render-settings-mutate-the-shared-`.
  Invented workspace names produce HTTP 422 chdir failures.
- Manual tester must never use `--headless`; windowed screenshots required.
