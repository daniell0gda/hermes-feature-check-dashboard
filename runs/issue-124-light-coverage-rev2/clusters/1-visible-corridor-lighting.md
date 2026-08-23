# Cluster 1: visible-corridor-lighting

files: `scripts/game/underground/Torch.gd`, `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`
dependencies: none
parallel: true

## Acceptance criteria
- After carving the 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius (headless count_near assertions unchanged in threshold and coverage).
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- In a fresh windowed gl_compatibility (llvmpipe) top-down run of `cave_carved_path_torches.json`, a scripted brightness/warm-pixel measurement over fresh PNGs reports measurable warm-light presence in every sampled segment along all four carved cross arms (no arm segment with zero warm pixels).

## Verification commands
- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"] plus ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"]
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Notes for implementor/checker: headless counts already pass at LIGHT_RADIUS 2.5; the failure is rendered light not reaching corridor floor (radius clipped by range/attenuation or torch inside wall geometry). Raise visible coverage (larger omni_range/energy, lights positioned into open corridor space, or additional floor lights) without weakening any headless count_near/unlit assertion. Prove with fresh windowed PNGs inspected per-arm.
