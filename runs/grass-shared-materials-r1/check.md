# Check Report: grass-mutates-shared-materials (issue #111) — iteration 1

classification: fixable

## Verdict

The feature is NOT implemented. The worktree (`issue/grass-render-settings-mutate-the-shared-`)
is clean and identical to `origin/master`; no commit modifies `scripts/game/NatureDecoration.gd`
for this issue and the required test files do not exist. The bug described in the request is
still present verbatim in the source:

- `scripts/game/NatureDecoration.gd` `_apply_small_vegetation_render_settings()` (~lines 566-587):
  writes `m.transparency`, `m.alpha_scissor_threshold`, `m.no_depth_test`,
  `m.render_priority` directly on the material obtained from
  `mi.get_surface_override_material(s)` / `mi.mesh.surface_get_material(s)` with **no
  `.duplicate()` and no `set_surface_override_material()` write-back** — it still
  mutates the ResourceLoader-cached resource shared across instances.
- The mesh walk is still `for c in node.get_children():` — direct children only, no recursion
  (contrast `_apply_tree_render_settings_recursive` at :817 which recurses correctly).
- No debug-build `[NatureDecoration]` log line for material duplication.
- `tests/visuals/test_small_vegetation_render_settings.gd/.tscn` are missing.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-grass-render-settings-mutate-the-shared-)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Build/import | `godot --headless --path . --import` | 0 | Import completed (86s); pre-existing icon/gltf load errors during first scan are baseline noise, reimport finished cleanly |
| Full test | `godot --headless --path . res://tests/visuals/test_nature_visibility_range.tscn` | 0 | `=== nature_visibility_range: 5 ok, 0 failed ===` (existing regression suite green) |
| Focused test | `godot --headless --path . res://tests/visuals/test_small_vegetation_render_settings.tscn` | 1 | `Cannot open file 'res://tests/visuals/test_small_vegetation_render_settings.tscn'` — scenario never created |

Runner gate: passed (run_project_cmd used for every command; no host Godot).

## Acceptance criteria status

All 8 criteria moved to Pending (see status.md):

- Criteria 1-6 (duplication, override write-back, per-instance materials, recursive walk,
  shadow/cull retention, debug log): no implementing code exists.
- Criterion 7 (visibility-range regression passes unchanged): the test itself passes today,
  but "after the rewrite" cannot hold because there is no rewrite; kept Pending.
- Criterion 8 (windowed manual screenshot): manual_testing required; nothing to screenshot —
  no change exists. Not performed.

## Changed-file quality findings

None assessable — diff vs origin/master is empty. No scope creep, no quality notes to append.

## Blockers

None infra-related. Runner, workspace, and image all healthy. Sole blocker is missing
implementation work (fixable).

## Unverified items

- Windowed manual screenshot of gameplay map (criterion 8) — requires implementation first.
