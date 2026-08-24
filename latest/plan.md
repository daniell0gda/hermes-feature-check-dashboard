# Plan: traps_frostbite_fangs visual proof

manual_testing: required

## Goal

Keep the existing Frostbite Fangs perk. Make windowed shots prove the frost overlay: close top-down view of trap + chilled enemy, frost tint readable vs green Cactoro.

## Context

Perk code is already in the worktree. Yesterday's shots fail human review (tiny distant pad; zoom is empty floor). `_update_camera_for_layer` after `camera_target` set puts the default far underground camera back — that is why the proving shot is unreadable.

## Clusters

One sequential cluster: `visual-camera` (`parallel: false`).

## Verification (run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-traps-frostbite-fangs)

1. `["godot","--version"]`
2. `["godot","--headless","--path",".","--editor","--quit-after","300"]`
3. `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/traps_frostbite_fangs_progression.json"]`
4. Windowed (manual tester, never --headless): same harness plus `--rendering-method gl_compatibility --audio-driver Dummy` if Vulkan fails.

Checker fails (`classification: fixable`) unless a fresh PNG clearly shows a frost-tinted enemy next to the trap. Tiny specks fail. Empty-floor crop fails.
