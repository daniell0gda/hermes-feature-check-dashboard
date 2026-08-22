# Cluster 1: runtime-modal-harness-access

- Files: `scripts/ui/UI.gd`, `scripts/testing/HarnessActions.gd`, `scripts/testing/AgentHarness.gd`
- Dependencies: none
- parallel: false

## Acceptance criteria

- After the UI opens its Options screen at runtime, a scenario `call` action can reach that live modal instance as a call/expression target (the reference is kept, not dropped), and the action returns ok:true with the modal reachable.
- A scenario can switch the opened Options modal to its Sound tab through a UI-callable method, and a subsequent screenshot shows the Sound tab active with its themed sliders rendered.
- A scenario can open one of the Options screen's WoodDropdown pickers through a UI-callable method so its floating row list, name plate and rows are visible for a screenshot.
- A scenario can assert which resolution/UI-scale dropdown row is currently selected on the opened Options modal via an expectation or call detail, and the assertion passes against the actual selection.
- Opening the Options screen twice does not leave two live modal instances; only one instance exists afterwards.
- Resolving an unknown target name or a node-path target that matches nothing returns ok:false with a descriptive detail instead of crashing the harness or aborting the scenario timeline.
- Debug-build [HARNESS] log line per runtime-modal resolution, naming the requested target/path and whether a live node was found.

## Verification commands

- Focused test (windowed; screenshots need real pixels): `["godot","--rendering-method","gl_compatibility","--rendering-driver","opengl3","--audio-driver","Dummy","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/hud_other_panels.json"]`
- Full test (headless logic pass): `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/hud_other_panels.json"]`
- Typecheck/build (also regenerates the class_name cache required before scenario runs): `["godot","--headless","--editor","--quit-after","100"]`
