# Issue #63 planning request

## Goal

Fix failed-map reload so no tower activity, projectile, timer, status/effect, or Porter-specific visual/teleport callback from the previous map can fire or apply an effect after the new map is loaded. Reproduce the failure with a Porter, add an AgentHarness regression scenario/test, and verify both gameplay state and visible effects.

## Acceptance criteria

1. A reproducible scenario places and activates a Porter on map A, starts its targeting/teleport dissolve activity, then performs the failed-map reload path and loads map B.
2. Reload teardown cancels/disposes every previous-map tower and related transient activity, including Porter target state, dissolve tween/rings/beam, projectiles, delayed callbacks/timers, status/effect nodes, and any signal/tween connections that can outlive the old map.
3. After reload, no previous-map Porter or other tower fires, damages, teleports, applies status, or spawns visual effects in map B. Only towers newly created for map B may act.
4. The regression is represented by a deterministic AgentHarness scenario with pre-reload checkpoints proving activity existed and post-reload checkpoints proving exact cleanup/no residual action. Counts alone are insufficient; use action-level telemetry/logs or a typed observable state where needed.
5. Run focused headless gameplay verification plus relevant baseline smoke/regression scenarios. Inspect raw Godot stdout/stderr separately for parse/resource/script diagnostics.
6. Run a windowed OpenGL-compatible checkpoint for the Porter/effect transition and inspect the captured PNG; file creation alone is not visual evidence.

## Constraints

- Flat artifacts only under `.gen/`; preserve existing dashboard artifacts.
- Planning phase must not edit production source or tests.
- Keep gameplay ownership separate from AgentHarness/scenario ownership and verification.
- Follow `/opt/data/coding_rules.md`, `CLAUDE.md`, and the Linux runner command boundary in the team-work/godot planning guidance.

## Starting context

- Worktree: `/workspace/git-workspaces/godot-td/issue-63`
- Branch: `issue/63`
- Starting HEAD: `c0e4b457ec1bef4e583b8d08ce2774b41e2eb2a7`
- Issue URL: `https://github.com/daniell0gda/poke-defense-godot/issues/63` (web page was unavailable/404 in this environment; acceptance scope above is authoritative).
- Existing related scenario: `tests/scenarios/map_swap_leaks_underground_enemies.json`; it exercises map swapping and Porter dissolve cleanup for enemies, but does not prove tower activity/effects are gone.
- Existing dashboard state under `.gen/team-work-dashboard/` is preserved and is not part of this planning edit.
