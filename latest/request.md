# Request: Exposed Plating perk (issue #89) — continuation r2

- **Repo:** daniell0gda/poke-defense-godot
- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/89
project: godot-td
workspace: poke-defense-godot/issue-exposed-plating
- **Workspace:** poke-defense-godot/issue-exposed-plating
- **Branch:** issue/exposed-plating (rebased onto origin/master @ e7910d0 on 2026-08-24; implementation is uncommitted WIP, preserve it)
- **Request ID:** req-89-exposed-plating-r5 (r4 tester got the wrong runner key `poke-defense-godot` and produced no shots. request.md now has literal `project: godot-td` / `workspace: poke-defense-godot/issue-exposed-plating` so the dispatcher prompt is correct. Resume at manual-tester only.)
- **Runner key:** `godot-td` — workers MUST use `project=godot-td`, `workspace=poke-defense-godot/issue-exposed-plating`. Never invent workspace names. Never use `project=poke-defense-godot`.

## Feature

New progression perk `exposed_plating` (Common, global, 3 levels). When an enemy's armor transitions from >0 to 0 (same site as `_consume_armor()`), it gains an "Exposed" status:

- L1: +15% damage taken, 0.5s
- L2: +25% damage taken, 1s
- L3: +35% damage taken, 1.5s

## Historical run (context only, not evidence)

Run `issue-89-exposed-plating` (2026-08-23) implemented the perk and passed headless harnesses, then ended `classification: fixable` / dashboard `failed` because the checker treated missing windowed VFX screenshots as a code-check failure. That skipped the manual-tester gate. Re-verify everything after the rebase. Do not assume old `.gen/harness` results are still valid.

## Acceptance criteria

1. Perk definition registered like other progression perks; purchasable at 3 levels.
2. Trigger fires exactly once per shield instance: only on the >0 → 0 transition. Hits while armor is already 0 do NOT re-trigger; re-trigger requires the enemy regaining armor first.
3. Damage-taken multiplier applies for the debuff duration, per level values above, then expires cleanly.
4. New VFX: `ExposedStatus`/`ExposedVFX` following the existing `BurnStatus`/`BurnVFX` pattern — cracked-shield emissive overlay for the duration, lazily instantiated by `EffectsManager` like `BurnVFX`/`OilVFX`.
5. Debug logging: `[EXPOSED]` prefix lines on trigger and expiry, gated by `OS.is_debug_build()`.

## Verification requirements

- Fresh focused headless harness after the rebase: `tests/scenarios/exposed_plating_once_per_shield.json` and `tests/scenarios/exposed_plating_vfx.json` via `run_project_cmd` (`godot-td`).
- If headless criteria 1–3 and 5 are green, checker must write `classification: pass` so the leader can dispatch manual-tester. Missing windowed PNGs/GIFs are a manual-tester job, not a code-check `fixable`.
- Manual testing: REQUIRED (player-facing VFX). Windowed screenshots plus a real 30fps GIF from harness `record_frames` of the Exposed VFX on a real enemy. No headless-only closeout. End with `ui_feels_broken: yes|no`.
- Windowed Godot args when Vulkan fails: `--rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy`.
- Native Godot through `run_project_cmd`; explicit scene argument before user args.

## Preserve

Keep the existing uncommitted source diff and new files (`ExposedVFX.gd`, `ExposedStatus.gd`, both scenarios). Re-plan only unmet work (windowed evidence + any rebase breakage). Do not wipe the implementation.
