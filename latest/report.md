# Feature Check — In Progress

Status: running
Phase: checker
Iteration: —
Last update: 2026-08-09T18:41:24.178Z

## Progress

## ✅ Done

- Editor parse gate (`--editor --quit-after 300`) exits 0

## ⬜ Pending

- Spawner drains every spawn whose scheduled game time has already passed within a single frame (catch-up), not only one enemy per frame
- Scheduled spawn timing preserves overshoot (accumulates delay) instead of resetting the countdown and dropping owed spawns when `delta > delay`
- Catch-up is queue-bounded (and safety-bounded) so a pathological long frame cannot hang or over-drain beyond the schedule
- Over a fixed game-time window, enemy spawn count is independent of frame length / high `GameState.time_scale` when the wave schedule would deliver those enemies
- `immediate_spawn` keeps its existing ~0.2 s burst behaviour and is not converted into the scheduled catch-up cadence
- Heavy-log `ice_rate_matched_speeds` (ice at `[7.43, 0.0, 7.46]` all phases) yields equal `[SPAWNER DEBUG] Spawning enemy` counts across 1x/2x/5x/10x phases (27/27/27/27 for map_10 wave 1)
- Focused harness `ice_rate_matched_speeds` still passes headlessly on a fresh run
- `projectiles_10x_beam_cone`, `projectiles_2x_roster`, and `projectiles_5x_roster` still pass every expectation on fresh runs
- Post-fix 5x/10x egg headroom is re-checked; any expectation retune is evidence-based difficulty restore, not reintroducing under-spawn

## ❌ Impossible


## 📝 Notes

- Iteration 0: empty changes; SpawnerSystem unfixed at ~467–501
- Heavy summary: `.gen/feature-check/.../heavy_full.summary.json`
- Next: implementor must apply IceTower-style while + `timer += delay` in `_process_spawner`


## Last node

checker
