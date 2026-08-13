# Cluster 2 — native verification and preservation

- `parallel: false`
- `depends_on: [focused-regression-evidence]`
- `owns:` fresh `.gen/harness` evidence and verification notes only; no source/scenario/dashboard edits.
- `forbidden overlap:` do not rename or modify the #61 harness actions or either flush API. Git inspection stays Hermes-side, not `run_project_cmd`.

## Steps and acceptance
1. Run Godot version and editor/import gate; record exit codes and diagnostics.
2. Run the focused scenario with explicit `scenes/Main.tscn` and verify the exact `.4/.6`, clear/idempotence, HP, kill, tube, surface, and attribution evidence listed in Cluster 1.
3. Run unchanged `ice_focus_cone_cadence`, `smoke_tower_roster`, and `projectiles_10x_beam_cone` with explicit Main scene; require exit 0 and fresh result JSON. Existing prior results without matching current revision/run are stale.
4. Do not claim a balance multiplier: normal gameplay totals may exceed one because the scenario also records ordinary fire damage. Report residual delta separately from aggregate totals. No visual run is required.

## Exact commands
```json
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--version"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","--editor","--quit-after","300"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_32_fractional_damage_retirement.json"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/ice_focus_cone_cadence.json"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/smoke_tower_roster.json"]}
{"project":"godot-td","workspace":"godot-td/issue-32","cmd":["godot","--headless","--path",".","scenes/Main.tscn","--","--harness=res://tests/scenarios/projectiles_10x_beam_cone.json"]}
```

## Evidence
`.gen/harness/issue_32_fractional_damage_retirement/result.json`, preservation result JSONs, `.gen/harness/_logs/`, and a concise verification report. Only explicit-Main fresh outputs count.
