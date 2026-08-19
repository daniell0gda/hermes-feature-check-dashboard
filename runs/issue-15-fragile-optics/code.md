# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/progression/global.json` — modified
- `scripts/progression/managers/CurseProgressionManager.gd` — modified
- `autoload/ProgressionManager.gd` — modified
- `docs/progression.md` — modified
- `docs/progression-system.md` — modified
- `tests/scenarios/curse_fragile_optics_progression.json` — new
- `scripts/game/actors/Tower.gd` — modified
- `scripts/game/actors/towers/PorterTower.gd` — modified
- `scripts/game/actors/Projectile.gd` — modified
- `scripts/game/actors/projectiles/GenericTowerProjectile.gd` — modified
- `scripts/game/actors/effects/EffectsManager.gd` — modified
- `scripts/game/actors/effects/MissVFX.gd` — new
- `scripts/testing/HarnessValues.gd` — modified
- `tests/scenarios/curse_fragile_optics_miss.json` — new
- `.gen/changes.md` — new

## Criteria
- `curse_fragile_optics` is a Common perk that starts at level 0 and eligible, appears in a full chest draw while eligible, becomes level 1 after it is applied, becomes level 2 after it is applied again, and is absent from a full chest draw after that second level is taken. — Done
- While unowned, Fragile Optics reports disabled with no range bonus and no miss chance. At level 1 it reports a +10% range bonus and a documented L1 miss chance. At level 2 it reports a +20% range bonus and a documented L2 miss chance higher than L1. Both owned levels report the same documented speed threshold relative to the map baseline enemy speed. — Done
- An owned `curse_fragile_optics` remains owned after progression save and reload, and `reset_for_new_game` returns it to unowned with the disabled config. — Done
- `curse_fragile_optics` and `curse_overheat` keep independent state: taking either one does not own or change the other's level or config. — Done
- Debug-build [FRAGILE_OPTICS] log line per perk apply — Done
- A placed targeting tower's effective range is 1.10 times its unowned range at level 1 and 1.20 times at level 2, including a tower placed before the perk was taken. — Done
- A placed Porter's effective range includes the Fragile Optics range bonus on top of its own current range while the curse is owned. — Done
- A tower projectile hit against an enemy whose current move speed is at or below the documented threshold never voids: the enemy loses HP. — Done
- A tower projectile hit against an enemy faster than the documented threshold that fails the miss-roll deals no damage and applies no on-hit status. — Done
- Damage-over-time ticks still reduce HP while `curse_fragile_optics` is owned. — Done
- A voided hit dispatches a Miss VFX through EffectsManager on that enemy. — Done
- Debug-build [FRAGILE_OPTICS] log line per voided hit — Done

## Commands and results
- `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot","--version"]` — exit code 0; Godot 4.4.1.stable.official
- `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot","--headless","--path",".","--editor","--quit-after","300"]` — exit code 0; import/class cache completed
- `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/curse_fragile_optics_progression.json"]` — exit code 0; `.gen/harness/curse_fragile_optics_progression/result.json` status=pass
- `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/curse_fragile_optics_miss.json"]` — exit code 0; `.gen/harness/curse_fragile_optics_miss/result.json` status=pass

## Notes
- Runner profile key is `godot-td` (not `poke-defense-godot`).
- Documented figures: L1 +10% range / 10% miss; L2 +20% range / 25% miss; `speed_threshold_ratio` 1.0 vs map baseline speed 1.0.
- `set_fragile_optics_next_roll(0.0)` is a sticky harness override so voided hits are deterministic.
- Porter placement needs a hole first. Mid-timeline log waits freeze a stale `.out.log`; log checks belong in trailing expectations.
- Headless cannot prove Miss VFX pixels; logic/dispatch is harness-checked. Windowed screenshots remain a manual-tester item.
- Did not push, merge, commit, or close the issue.
\n