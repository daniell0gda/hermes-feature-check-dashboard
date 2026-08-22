# Issue #63 continuation request

## Goal

Reloading a failed map must start clean: no Porter shots, beams, timers, or leftover VFX from the previous map. Re-verify on current master after cherry-picking the existing teardown/regression work.

## Acceptance criteria

1. A scenario places and activates a Porter on map A, then reloads a failed map onto map B.
2. Reload teardown cancels previous-map tower activity (projectiles, timers, Porter dissolve/target, beams, callbacks).
3. After reload, only new-map towers may act. Porter on the old map must stay quiet.
4. Deterministic AgentHarness scenario with pre-reload activity and post-reload zero residual action.
5. Fresh headless focused harness plus raw Godot diagnostic scan.
6. Windowed OpenGL-compatible screenshot after reload; inspect the PNG (Map B / new tower activity, no stale Porter VFX).

## Constraints

- Workspace: `/workspace/git-workspaces/poke-defense-godot/issue-clear-previous-map-tower-effects`
- Branch: `issue/clear-previous-map-tower-effects` (based on current origin/master + cherry-pick `40808d9` with conflict merge).
- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/63
- Flat `.gen/` artifacts. New request id. Do not close/merge/push.
- Project commands only via runner `godot-td` / workspace `poke-defense-godot/issue-clear-previous-map-tower-effects`.
- Visible reload/VFX: `manual_testing: required`.
- Follow `/opt/data/coding_rules.md`.
