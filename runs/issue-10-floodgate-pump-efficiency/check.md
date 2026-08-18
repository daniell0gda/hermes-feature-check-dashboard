# Check Report: floodgate-pump-efficiency (iteration check)
classification: fixable

## Verdict
fixable — all harness scenarios and editor build pass (exit 0), but new/modified code contains forbidden type casts (float(), int(), as) violating /opt/data/coding_rules.md "Type casts are forbidden." and dynamic .call() without static typing. No runner/infra blocker.

## Commands run (via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-floodgate-pump-efficiency)
- build/typecheck: ["godot","--headless","--path",".","--editor","--quit-after","300"] → exitCode=0 (9.2s)
- focused: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/floodgate_pump_efficiency_progression.json"] → exitCode=0, status=pass
- cycle: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/floodgate_pump_efficiency_cycle.json"] → exitCode=0, status=pass
- chest: ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/progression_chest_pool.json"] → exitCode=0, status=pass

## Acceptance criteria evidence/status
All 12 criteria have passing test evidence from the three harness runs (logs show L0=2.0s, L1=1.8s, L2=1.6s, L3=1.4s, [FloodgateProgression] and [FLOODGATE] logs, chest draws, save/load, sibling isolation, damage preserved). However, each is marked Pending due to quality violations in their implementing files (see status.md).

## Changed-file quality findings
- scripts/progression/managers/FloodgateTowerProgressionManager.gd: multiple float() casts in _apply_* and get_*; long comment block but functions <60 lines; follows most CLAUDE.md (typed, debug logs, short funcs)
- autoload/ProgressionManager.gd: float() casts in get_floodgate_*_multiplier and get_floodgate_cycle_cooldown; uses max() on floats (should be maxf?)
- scripts/game/actors/towers/FloodgateTower.gd: float()/int() casts in _effective_* and _log_*; dynamic manager.call(); legacy long _update_floodgate_water (929-line file) untouched per rules
- No scope creep, no duplicated bad patterns in feature diff. quality-notes.md not present, no open entries to re-check.
- Cross-cutting: type-cast pattern introduced in feature code — fix by using typed returns and avoiding casts.

## Blockers / unverified items
none — runner available, workspace present, all scenarios exist and pass, no design_failure. Fixable by removing casts (e.g. use proper typed getters, avoid .call() or use typed interfaces). No missing tests or evidence.

## Classification rationale
blocked only for runner/docker/auth failures (none here). Host godot not used. Missing tests would be fixable. Quality violations make it fixable, not pass.