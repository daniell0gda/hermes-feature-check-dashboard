# Acceptance Plan: issue-124-cave-carved-path-torches (follow-up: sealing regression + visible corridor lighting)

## Verification

All project commands run through Hermes `run_project_cmd` (project `godot-td`, workspace `poke-defense-godot/issue-cave-carved-path-torches`). Token arrays verbatim.

- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"] then ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json"] then repeat ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json"] a second consecutive time
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Raw-output rule: scan fresh runner stdout/stderr independently from harness `status` for `Parse Error`, `SCRIPT ERROR`, `Failed loading resource`, `Invalid call`; pre-existing HudTheme missing-texture noise and exit-time dummy-renderer leak warnings are not failures. A harness pass alone is not acceptance evidence. Windowed PNGs must be fresh (timestamps match the run) and pixel-inspected; stale or headless screenshots are not proof. The visual gate is numeric only (`uv run --with pillow python .gen/measure_warm_pixels.py <png>`); vision-model summaries are never acceptance evidence for lighting.

manual_testing: required

## Clusters

1. visible-corridor-lighting — files: `scripts/game/underground/Torch.gd`, `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd` — depends on: none
- After carving the 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius (headless count_near assertions unchanged in threshold and coverage).
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- In a fresh windowed gl_compatibility (llvmpipe) top-down run of `cave_carved_path_torches.json`, a scripted brightness/warm-pixel measurement over fresh PNGs reports measurable warm-light presence in every sampled segment along all four carved cross arms (no arm segment with zero warm pixels).
- The rendered floor glow reads as small, warm, localized light pools at each torch rather than a floodlight: the numeric measurement reports non-uniform warmth along each arm (per-segment warm fraction varies measurably between pool centers and pool edges) and no arm segment saturates toward uniform white.
2. dark-pending-and-declined-caves — files: `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`, `scripts/game/CaveSystem.gd` — depends on: none
- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.
3. harness-scenario-and-evidence — files: `tests/scenarios/cave_carved_path_torches.json` — depends on: 1, 2
- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and a scripted numeric warm-pixel/brightness measurement has been run over them reporting warm-light presence in every arm segment (manual tester runs the measurement; vision-model summaries are not acceptance evidence).
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.
4. cave-sealing-regression-determinism — files: `scripts/testing/HarnessActions.gd`, `scripts/testing/HarnessValues.gd`, `scripts/testing/HarnessScenario.gd`, `tests/scenarios/cave_pending_seals_entrance_instantly.json`, `scripts/game/CaveSystem.gd` — depends on: none
- When `cave_pending_seals_entrance_instantly.json` runs, no cave other than the scenario's own fixture (id 9003) is discovered, carved, locked, or sealed before the scenario's first route assertion completes, regardless of RNG rolls (harness suppression of RNG cave discovery is honored for every carve event).
- The scenario's initial hole-to-exit route assertion (`has_route_from == true`) succeeds deterministically on repeated runs from the same fixed scenario seed.
- At the moment the dangerous-cave confirmation question is presented (before any player decision), `has_route_from` for the scenario's hole returns false while the cave is marked pending, proving the entrance is physically sealed rather than logically locked.
- One second after sealing, the pending dangerous cave's interior contains zero active torches.
- Confirming "yes" restores the exact hole-to-exit route (`has_route_from == true`) and the cave interior again reports valid lighting (nonzero torch coverage within the confirmed cave).
- The scenario keeps its original assertions unchanged: no expectation, wait_for_condition, or threshold may be removed or loosened by the determinism or lighting fixes.

## Criteria

- After carving the 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius (headless count_near assertions unchanged in threshold and coverage).
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- In a fresh windowed gl_compatibility (llvmpipe) top-down run of `cave_carved_path_torches.json`, a scripted brightness/warm-pixel measurement over fresh PNGs reports measurable warm-light presence in every sampled segment along all four carved cross arms (no arm segment with zero warm pixels).
- The rendered floor glow reads as small, warm, localized light pools at each torch rather than a floodlight: the numeric measurement reports non-uniform warmth along each arm (per-segment warm fraction varies measurably between pool centers and pool edges) and no arm segment saturates toward uniform white.
- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.
- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and a scripted numeric warm-pixel/brightness measurement has been run over them reporting warm-light presence in every arm segment (manual tester runs the measurement; vision-model summaries are not acceptance evidence).
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.
- When `cave_pending_seals_entrance_instantly.json` runs, no cave other than the scenario's own fixture (id 9003) is discovered, carved, locked, or sealed before the scenario's first route assertion completes, regardless of RNG rolls (harness suppression of RNG cave discovery is honored for every carve event).
- The scenario's initial hole-to-exit route assertion (`has_route_from == true`) succeeds deterministically on repeated runs from the same fixed scenario seed.
- At the moment the dangerous-cave confirmation question is presented (before any player decision), `has_route_from` for the scenario's hole returns false while the cave is marked pending, proving the entrance is physically sealed rather than logically locked.
- One second after sealing, the pending dangerous cave's interior contains zero active torches.
- Confirming "yes" restores the exact hole-to-exit route (`has_route_from == true`) and the cave interior again reports valid lighting (nonzero torch coverage within the confirmed cave).
- The scenario keeps its original assertions unchanged: no expectation, wait_for_condition, or threshold may be removed or loosened by the determinism or lighting fixes.

## Planning-gate observations (current HEAD evidence)

- Prior windowed screenshot `.gen/screenshots/dungeon_cross_carve_lit.png` was pixel-inspected during planning: only one warm-lit region is visible; carved cross arms read fully dark. Cluster 1 exists to close exactly this gap; headless counts already pass, so the fix must raise *visible* coverage without weakening headless assertions.
- Revision 3 additionally requires the glow to stay localized: distinct warm pools per torch, no washed-white corridors. Cluster 1's pool-shape criterion encodes this; reduce glow radius/intensity/additive strength if needed while keeping every arm segment above zero warm pixels.
- `cave_pending_seals_entrance_instantly.json` already carries `suppress_rng_cave_discovery: true`; cluster 4 verifies that suppression actually holds on every carve event and that the seal → dark → confirm-yes sequence is deterministic across consecutive runs.

## Verification status (fresh run_project_cmd evidence, this planning run)

- Probe `godot --version`: 4.4.1.stable.official.49a5bc7b6, exit 0.
- Editor/import gate (`--editor --quit-after 300`): exit 0.
- `cave_carved_path_torches`: `status=pass exit=0`; `[TORCH] incremental-carve update active=154` full-cross coverage observed; `[CAVE] discovery suppressed` honored in regression scenarios; screenshots written fresh by windowed runs.
- `declined_cave_torches_extinguish`: `status=pass exit=0`.
- `cave_pending_seals_entrance_instantly`: fresh run `status=pass exit=0`, all actions ok; `[CAVE] discovery suppressed: harness scenario forbids RNG cave discovery` logged on every carve event; initial route found → sealed (`No valid path found to exit`) while pending → zero-torch wait satisfied → confirm yes → route restored (17-waypoint path). Raw output scanned independently: only pre-existing HudTheme missing-texture noise and exit-time dummy-renderer leak warnings.
- Numeric warm-pixel gate on the latest windowed screenshot `.gen/screenshots/dungeon_cross_carve_lit.png`: RESULT FAIL previously — south[0..2], west[0..1], west[6], and east[1..7] arm segments had 0.00% warm pixels. This remains the gap cluster 1 must close before cluster 3's pixel-evidence criterion can pass; do not accept vision-model summaries as a substitute for this measurement.
