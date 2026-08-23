# Cluster 1: visible-corridor-lighting

files: `scripts/game/underground/Torch.gd`, `scripts/game/underground/TorchPlacer.gd`, `scripts/game/underground/TorchManager.gd`
dependencies: none
parallel: true

## Acceptance criteria
- After carving the 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius (headless count_near assertions unchanged in threshold and coverage).
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- In a fresh windowed gl_compatibility (llvmpipe) top-down run of `cave_carved_path_torches.json`, a scripted brightness/warm-pixel measurement over fresh PNGs reports measurable warm-light presence in every sampled segment along all four carved cross arms (no arm segment with zero warm pixels).
- The rendered floor glow reads as small, warm, localized light pools at each torch rather than a floodlight: the numeric measurement reports non-uniform warmth along each arm (per-segment warm fraction varies measurably between pool centers and pool edges) and no arm segment saturates toward uniform white.

## Verification commands
- Focused test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_carved_path_torches.json"]
- Full test: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/declined_cave_torches_extinguish.json"] then ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_pending_seals_entrance_instantly.json"]
- Typecheck/build: ["godot","--headless","--path",".","--editor","--quit-after","300"]

Notes for implementor/checker: headless counts already pass; the failure is *rendered* light — only one warm region shows in windowed PNGs (numeric gate FAIL on multiple arm segments). Raise visible coverage (larger effective radius/energy, lights positioned into open corridor space, additional floor lights) without weakening headless assertions. Revision 3 additionally caps the look: distinct small warm pools per torch, not uniform whiteness — reduce glow radius/intensity/additive strength as needed while keeping every arm segment above zero warm pixels. The gate is numeric only (`uv run --with pillow python .gen/measure_warm_pixels.py <png>`); vision-model summaries are never acceptance evidence.
