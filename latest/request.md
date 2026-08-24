# Request: Exposed Plating perk (issue #89) — continuation r7

- **Repo:** daniell0gda/poke-defense-godot
- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/89
project: godot-td
workspace: poke-defense-godot/issue-exposed-plating
- **Workspace:** poke-defense-godot/issue-exposed-plating
- **Branch:** issue/exposed-plating (rebased onto origin/master @ 42e05d6 on 2026-08-24; implementation is uncommitted WIP — preserve it)
- **Request ID:** req-89-exposed-plating-r7
- **Runner key:** `godot-td` — workers MUST use `project=godot-td`, `workspace=poke-defense-godot/issue-exposed-plating`. Never invent workspace names. Never use `project=poke-defense-godot`.

## Feature

New progression perk `exposed_plating` (Common, global, 3 levels). When an enemy's armor transitions from >0 to 0 (same site as `_consume_armor()`), it gains an "Exposed" status:

- L1: +15% damage taken, 0.5s
- L2: +25% damage taken, 1s
- L3: +35% damage taken, 1.5s

## Historical runs (context only, not evidence)

- r1–r3: perk implemented; headless harnesses pass. Old checkers treated missing windowed shots as code-check `fixable`.
- r4: tester used the wrong runner key `poke-defense-godot` → no shots.
- r5: windowed `exposed_plating_vfx` DID capture 1920x1080 PNGs at 07:50 UTC, but the leader had already failed at 07:49 (`missing screenshots`). Those frames are **not** acceptable evidence: default far camera, enemy is a tiny blob at the spawn, Debug Panel covers the left third, and the amber cracked-shield wash is not inspectable. Archived under `.gen/harness/exposed_plating_vfx/r5-too-far/`. Do not copy them to `.gen/screenshots/` and do not treat them as pass.
- r6: planner wrote the close-up plan (`camera_focus` + hidden debug panel + 30fps GIF). Code worker died with empty model / exhausted API retries. Dashboard `req-89-exposed-plating-r6` is terminal `failed`. Do not reuse that run id.
- After r6: worktree rebased onto origin/master `42e05d6`. Stash-pop conflict in `HarnessActions.gd` resolved by keeping master's `mouse_pan` **and** this issue's `camera_focus` / `set_debug_panel`.

## Acceptance criteria

1. Perk definition registered like other progression perks; purchasable at 3 levels.
2. Trigger fires exactly once per shield instance: only on the >0 → 0 transition. Hits while armor is already 0 do NOT re-trigger; re-trigger requires the enemy regaining armor first.
3. Damage-taken multiplier applies for the debuff duration, per level values above, then expires cleanly.
4. New VFX: `ExposedStatus`/`ExposedVFX` following `BurnStatus`/`BurnVFX` — cracked-shield amber emissive overlay for the duration, lazily instantiated by `EffectsManager`.
5. Debug logging: `[EXPOSED]` prefix on trigger and expiry, gated by `OS.is_debug_build()`.
6. **Player-facing proof (this run's remaining work):** a human looking at the PNG must immediately see the overlay on the Orc King. Required:
   - After the boss exists, aim `camera_target` at the enemy and zoom in (`Game._zoom_camera` / closer camera distance / existing `camera_focus` harness action) so the enemy fills a large part of the frame.
   - Hide or ignore the Debug Panel; it must not cover the enemy.
   - Three windowed stills: before breach (no wash) / during Exposed (obvious amber wash on the body) / after expiry (wash gone).
   - Real 30fps GIF from harness `record_frames` of the zoomed enemy across the window (not a screenshot slideshow).
   - Copy proving PNGs + GIF into `.gen/screenshots/` and embed them in `.gen/manual-report.md`.
   - End with `ui_feels_broken: yes|no`. A yes fails.
   - If the overlay is still invisible at a close camera, that is a **code** failure — strengthen `ExposedVFX` (alpha/energy/shell size) until the before/during frames differ by eye. Do not pass on metadata `exposed_vfx == true` alone.

## Verification requirements

- Fresh focused headless after the rebase: `tests/scenarios/exposed_plating_once_per_shield.json` and `tests/scenarios/exposed_plating_vfx.json` via `run_project_cmd` (`godot-td`).
- If headless criteria 1–3 and 5 are green, checker writes `classification: pass` so the leader can dispatch manual-tester. Missing windowed PNGs/GIFs are a manual-tester job, not a code-check `fixable` — **unless** the close-up stills show no overlay, which is fixable code.
- Manual testing: REQUIRED. Never `--headless`. Windowed args when Vulkan fails: `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy`.
- Native Godot through `run_project_cmd`; explicit scene argument before user args.

## Preserve

Keep the existing uncommitted source diff and new files (`ExposedVFX.gd`, `ExposedStatus.gd`, both scenarios, `camera_focus` / `set_debug_panel` in `HarnessActions.gd`). Re-plan only if the r6 close-up plan is stale; otherwise implement/verify the unmet close-up visual work. Do not wipe the perk implementation.
