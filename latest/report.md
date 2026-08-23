# Team-leader report

- **Result:** failed
- **Classification:** **fixable**
- **Feature:** cave-carved-path-torches-fresh
- **Run:** issue-124-cave-carved-path-torches-fresh
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

# Acceptance Plan: issue-124-cave-carved-path-torches

## Verification

All project commands run through Hermes `run_project_cmd` (project `godot-td`, workspace `poke-defense-godot/issue-cave-carved-path-torches`).

- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"] then repeat the same token form with `cave_pending_seals_entrance_instantly.json` (the focused scenario above plus these two regression scenarios constitute the full torch/cave harness pass)
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Raw-output rule: scan fresh runner stdout/stderr separately from harness status for `Parse Error`, `SCRIPT ERROR`, `Failed loading resource`, `Invalid call`; a harness `status=pass` alone is not acceptance evidence.

## Clusters

1. torch-coverage-along-carved-corridors — files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd` — depends on: none
- After carving a 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius.
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
2. dark-pending-and-declined-caves — files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`, `scripts/game/CaveSystem.gd` — depends on: none
- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.
3. harness-scenario-and-evidence — files: `tests/scenarios/cave_carved_path_torches.json` — depends on: 1, 2
- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and their pixels have been inspected (manual tester).
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.

## Criteria

## ✅ Done
- After carving a 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius.
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.
- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.

## ⬜ Pending
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and their pixels have been inspected (manual tester).

## ❌ Impossible

## Check

# Check report: issue-124-cave-carved-path-torches (iteration: check)

classification: **fixable**

## Verification commands (all fresh via run_project_cmd, project godot-td, workspace poke-defense-godot/issue-cave-carved-path-torches)

- Preflight `["godot","--version"]` — exit 0, Godot 4.4.1.stable.
- Typecheck/build `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit 0, clean (no script errors).
- Focused `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]` — exit 0, `status=pass`, 0 failed actions. Evidence: `.gen/harness/cave_carved_path_torches/result.json` (fresh this check), log `.gen/harness/_logs/cave_carved_path_torches.out.log`.
- Full-suite regression `declined_cave_torches_extinguish.json` (same token form) — exit 0, `status=pass`.
- Full-suite regression `cave_pending_seals_entrance_instantly.json` (same token form) — **exit 1, FAILS**: timeout at wait_for_condition index 4 (`underground.has_route_from == true`).

## Pre-existing failure proof

`cave_pending_seals_entrance_instantly` failure was reproduced on clean HEAD this check: `git stash push -u` → identical exit 1 / timeout at action 4 (fresh `.gen/harness/cave_pending_seals_entrance_instantly/result.json`, status=timeout) → `git stash pop` (feature diff restored, verified via `git status`). Root cause per implementor: map_9 discovery chance 0.8 + seed 1 lets RNG-discovered caves carve/lock cells over the hole↔exit corridor before the first route assertion. Not caused by this cluster's files (TorchPlacer/TorchManager/Torch/Game/HarnessValues/scenario).

## Raw-output scan

- `SCRIPT ERROR` / GDScript `Parse Error` from game scripts: none.
- Pre-existing noise (not this diff): `HudTheme.tres` references nonexistent `res://textures/ui/hud/wood_panel.png` → repeated `Failed loading resource` / `Parse Error` spam and cascading scene parse errors (TowerShopSlot, PauseMenu, UI.tscn, etc.). Committed asset is `wood_panel_wide.png`. Flagged for maintainer; not introduced by this feature.
- Benign dummy-renderer exit leaks (PagedAllocator/RID/ObjectDB) — engine noise on headless quit.

## Criterion-by-criterion

1. Cross-arm coverage every ~2 units — **Done**. Scenario has 21 count_near expectations at ±2..±8 on both axes (every 2 units) plus connected-corridor samples; all pass (result.json all actions ok).
2. New connected corridor lit along entire length — **Done**. count_near at [1.5/3.5/5.5, -3, -8] pass; [TORCH] recompute active=148→154 after incremental carve.
3. No unlit carved cells in connected network — **Done**. `unlit_carved_in_cave == 0` expectations pass; verified implementation `count_unlit_carved_cells` checks every carved unlocked cell against LIGHT_RADIUS.
4. Pending dangerous cave interior zero torches — **Done**. `torch.count_in_cave == 0` passes before confirm (action index 3).
5. Declined cave interior zero active torches incl. carved overlap — **Done** (focused scenario decline-lock 9102/9103 + regression `declined_cave_torches_extinguish` pass).
6. Headless scenario passes with full-arm sampling — **Done** (exit 0, status=pass).
7. Fresh windowed screenshot PNGs, pixels inspected (manual tester) — **Pending**. Headless run marks all three screenshots `skipped (reason: headless)`; no PNGs exist under `.gen/harness/`. Manual tester (owns `.gen/manual-report.md`) has not produced visual evidence. Cannot be verified by checker.
8. `[TORCH]` debug log per recompute naming trigger and count — **Done with caveat**. `[TORCH] cave-path update active=N` appears per recompute (log lines 462+); trigger distinguished by preceding `[TorchManager] Carving detected` marker rather than inside the line itself. Acceptable per implementor note; count present.

## Quality findings (changed code)

- No violations of /opt/data/coding_rules.md or CLAUDE.md found in the diff: typed variables used throughout, guard clauses / early returns, functions short, no casts, debug logging per CLAUDE.md debug-log rule, surgical scope (5 files + 1 scenario). Minor: `sqrt(dx*dx+dz*dz)` duplicated in three places (TorchManager, TorchPlacer, HarnessValues) — advisory only.
- Pre-existing repo issue (not this diff): HudTheme.tres missing texture — record in quality notes, do not demote.

## Blockers / unverified

- Manual visual evidence (criterion 7) outstanding — owned by manual-tester profile.
- `cave_pending_seals_entrance_instantly` regression fails pre-existing on clean HEAD; needs a CaveSystem/RNG-scoped fix outside this cluster's owned files.
