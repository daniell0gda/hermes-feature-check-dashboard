## ✅ Done
- After the UI opens its Options screen at runtime, a scenario `call` action can reach that live modal instance as a call/expression target (the reference is kept, not dropped), and the action returns ok:true with the modal reachable.
- A scenario can switch the opened Options modal to its Sound tab through a UI-callable method, and a subsequent screenshot shows the Sound tab active with its themed sliders rendered.
- A scenario can open one of the Options screen's WoodDropdown pickers through a UI-callable method so its floating row list, name plate and rows are visible for a screenshot.
- A scenario can assert which resolution/UI-scale dropdown row is currently selected on the opened Options modal via an expectation or call detail, and the assertion passes against the actual selection.
- Opening the Options screen twice does not leave two live modal instances; only one instance exists afterwards.
- Debug-build [HARNESS] log line per runtime-modal resolution, naming the requested target/path and whether a live node was found.
- The extended `hud_other_panels` scenario passes end-to-end (harness status=pass) with added checkpoints covering the Options Sound tab and one open WoodDropdown picker.
- The scenario's result.json records screenshots of the Sound tab and the open dropdown, each captured while the panel is actually visible (outcome not skipped/headless).

## ⬜ Pending
- Resolving an unknown target name or a node-path target that matches nothing returns ok:false with a descriptive detail instead of crashing the harness or aborting the scenario timeline.

## ❌ Impossible
