# Coder report: implementation (revision 2 — visible corridor lighting fix)

## Changed files
- `scripts/game/underground/Torch.gd` — modified

## Criteria targeted
- "In a fresh windowed gl_compatibility (llvmpipe) top-down run of `cave_carved_path_torches.json`, each of the four carved cross arms shows visibly lit floor pixels along its entire length" — code fix landed; final numeric pixel proof remains the manual tester's item.
- "Fresh windowed-run screenshot PNGs exist with current timestamps ... pixels inspected (manual tester)" — unchanged ownership; manual tester must re-run the windowed capture + numeric warm-pixel measurement.

## What changed
Torch.gd now creates a `FloorGlow` MeshInstance3D per torch: an unshaded,
alpha-additive PlaneMesh (size 5x5 = 2*GLOW_RADIUS, matching LIGHT_RADIUS=2.5)
with a soft radial-falloff GradientTexture2D, positioned just above the floor.
This is the rendering-layer fix for the compatibility-renderer per-mesh
omni-light budget: emissive/unshaded materials are not subject to the omni
light-per-mesh cap that dropped most of the ~150 OmniLight3D nodes over merged
MultiMesh block geometry. The glow:
- flickers subtly with the light (`albedo_color.a` follows the flicker value),
- is hidden by `extinguish()` and shown by `ignite()`, so pending/declined
  sealed caves stay fully dark (glow only exists on active torches),
- adds no placement logic change; headless count_near/unlit assertions are
  untouched.

## Commands and results (all via run_project_cmd, project godot-td)
- `godot --headless --path . --editor --quit-after 300` — exit 0; clean import.
  First attempt caught a parse error (flicker var scoped inside `if light:`),
  fixed by hoisting flicker computation out of the if-block; second run clean.
- Focused `cave_carved_path_torches.json` — exit 0, status=pass;
  [TORCH] incremental-carve active=29 → 148 → 154 → 142; all arm count_near
  green; declined 9102/9103 zero interior torches.
- `declined_cave_torches_extinguish.json` — exit 0, status=pass;
  decline-lock 9001 then active=10.
- `cave_pending_seals_entrance_instantly.json` run 1 — exit 0 status=pass;
  suppression logged on every carve event; only fixture cave 9003; route found
  (8.0 / 17 waypoints) → sealed pending ("No valid path") → 1s dark
  (active=31) → confirm yes restores exact route + lighting (active=50).
- Same scenario consecutive run 2 — exit 0 status=pass, identical sequence,
  distances, torch counts (deterministic).

## Notes for checker/manual tester
- Raw-output scan per plan rule: only pre-existing HudTheme.tres
  missing-texture noise and exit-time dummy-renderer leak warnings; no feature
  SCRIPT ERROR / Parse Error / Invalid call.
- The glow renders in every renderer (unshaded additive), so it should show up
  under llvmpipe windowed runs regardless of light budget. If any arm segment
  still reads dark numerically, raise GLOW_ENERGY (1.4) before touching
  placement.
- No scenario JSON or assertion was modified this revision.
