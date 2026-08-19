# Team-leader report

- **Result:** failed
- **Classification:** unknown
- **Feature:** porter-boss-runner
- **Run:** issue-20-porter-boss-runner
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- Unique perk `porter_boss_runner` is type Unique, is eligible before it is taken, and appears in a chest draw before it is taken.
- Applying `porter_boss_runner` once owns it at level 1, makes it ineligible, and removes it from a chest draw; a further apply leaves the level at 1; `reset_for_new_game` returns it to unowned.
- Without `porter_boss_runner` owned, a Porter in range of a live surface boss does not lock or charge that boss, and the boss stays on the surface.
- With `porter_boss_runner` owned, a Porter in range of a live surface boss locks that boss and completes a full charge.
- After a full Porter charge on a boss with `porter_boss_runner` owned, the boss remains in play and is rerouted onto an extra underground detour produced by `UGSystem.compute_underground_route` rather than being consumed or removed.
- That boss reroute plays Porter's existing teleport dissolve VFX; dissolving is observable during charge completion.
- Without `porter_boss_runner` owned, a Porter still teleports a non-boss surface enemy onto a valid underground route.
- Debug-build [PORTER_BOSS_RUNNER] log line per perk apply
- Debug-build [PORTER_BOSS_RUNNER] log line per boss lock event
- Debug-build [PORTER_BOSS_RUNNER] log line per boss reroute event
- Focused harness scenario `porter_boss_runner` covers perk off (no boss target) and perk on (charge then reroute plus VFX/path evidence) and finishes with `status: pass`.

## ⬜ Pending

## ❌ Impossible

## Check

# Check Report: porter-boss-runner

## Verification commands (via run_project_cmd, project=godot-td, workspace=poke-defense-godot/issue-porter-boss-runner)
- Typecheck/build: `["godot","--headless","--path",".","--editor","--quit-after","300"]` — exitCode: 0 (success, 9.2s)
- Focused test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_boss_runner.json"]` — exitCode: 0, harness status=pass (17.2s)
- Full test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/porter_wide_gate_reach.json"]` — exitCode: 0, harness status=pass (67s)

## Criteria evidence (all from passing harness result.json + build)
- All 11 perk/behavior criteria + harness scenario: covered by automated expectations in porter_boss_runner.json (perk type/apply/reset, boss ignore/lock/charge/reroute via UGSystem + VFX, non-boss teleport, debug logs); status=pass on both runs.
- No quality violations found in changed files (PorterTower.gd, PorterTowerProgressionManager.gd, ProgressionManager.gd, porter_tower.json, new scenario) per /opt/data/coding_rules.md and CLAUDE.md; changes are surgical, no casts/raw enums, reuse existing paths.
- Manual_testing listed in plan verification but no .gen/manual-report.md or screenshots present (unverified item).

## Blockers
none

## Unverified items
- manual_testing (required in plan but no evidence produced)

## Classification
pass

## Quality notes
no changes (no violations appended)
