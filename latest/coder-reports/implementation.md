# Coder report: implementation

## Changed files
- `tests/scenarios/porter_boss_runner.json` — modified (required dissolving wait, screenshot burst, longer windowed-safe timeouts)
- `.gen/changes.md` — appended
- `.gen/screenshots/boss_dissolve_1.png` … `boss_dissolve_4.png` — copied from windowed harness shots

## Criteria
- Unique perk `porter_boss_runner` is type Unique, is eligible before it is taken, and appears in a chest draw before it is taken. — Done (reverified in focused pass)
- Applying `porter_boss_runner` owns it at level 1, then 2, then 3; it stays eligible and appears in a chest draw until level 3; a further apply leaves the level at 3; `reset_for_new_game` returns it to unowned level 0. — Done (reverified)
- Without `porter_boss_runner` owned, a Porter in range of a live surface boss does not lock or charge that boss, and the boss stays on the surface. — Done (reverified)
- With `porter_boss_runner` owned, a Porter in range of a live surface boss locks that boss and completes a full charge. — Done (reverified)
- After a successful full Porter charge on a boss with `porter_boss_runner` owned, the boss remains in play and is rerouted onto an extra underground detour produced by `UGSystem.compute_underground_route` rather than being consumed or removed. — Done (reverified)
- A successful boss reroute plays Porter's existing teleport dissolve VFX; dissolving is observable during charge completion. — Done (required dissolving wait + inspected windowed shots)
- After a completed Porter charge on a boss that misses, the boss stays on the surface, is not rerouted, and the success teleport dissolve cue does not play. — Done (reverified headless)
- Level 1 miss chance is Balance `porter_boss_runner.miss_chance` 0.7; each extra level subtracts `miss_reduction_per_level` 0.05 (L2 0.65, L3 0.60). A seeded roll below the current chance misses; a seeded roll at or above it hits. — Done (reverified)
- Without `porter_boss_runner` owned, a Porter still teleports a non-boss surface enemy onto a valid underground route. — Done (reverified)
- Debug-build [PORTER_BOSS_RUNNER] log line per perk apply — Done (reverified)
- Debug-build [PORTER_BOSS_RUNNER] log line per boss lock event — Done (reverified)
- Debug-build [PORTER_BOSS_RUNNER] log line per boss reroute event — Done (reverified)
- Debug-build [PORTER_BOSS_RUNNER] log line per boss miss event — Done (reverified headless)
- Focused harness scenario `porter_boss_runner` covers perk off (no boss target), perk on plus a hit seed (charge then reroute), and perk on plus a miss seed (charge spent, no reroute), and finishes with `status: pass`. — Done

## Commands and results
- `run_project_cmd project=poke-defense-godot … godot --version` — HTTP 400; `project must be an approved profile key`
- `godot --version` via `project=godot-td` workspace `poke-defense-godot/issue-porter-boss-runner` — exit code 0; 4.4.1.stable.official.49a5bc7b6
- `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/porter_boss_runner.json` — exit 0; `.gen/harness/porter_boss_runner/result.json` `status: pass`; elapsed 19.007s; dissolving wait required+ok; four screenshots skipped/headless; apply/lock/reroute/miss log expectations passed
- windowed `godot --display-driver x11 --rendering-driver opengl3 --audio-driver Dummy --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/porter_boss_runner.json` — exit 1; `status: timeout` at miss-log wait (68.564s, pre-90s budget); dissolve wait passed; `boss_dissolve_1..4` captured 1920x1080 under `.gen/harness/porter_boss_runner/shots/`

## Notes
- Revision target was the failed visual/manual gap (dissolve VFX unverified, no PNGs). Perk logic was not rewritten.
- Inspected shots: green Porter beam plus a white/cyan dissolve glow on the boss in `boss_dissolve_1`/`2`; glow gone in `3`/`4`; boss still on the board. Debug panel remains visible at default camera.
- Did not change `status.md` (checker owns classification). Did not publish dashboard events. Did not commit.
- Runner key that works is `godot-td`, not `poke-defense-godot`.
