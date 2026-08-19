# Acceptance Plan: Fragile Optics

manual_testing: required

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/curse_fragile_optics_miss.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/curse_overheat_progression.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. fragile-optics-perk — files: `scripts/progression/global.json`, `scripts/progression/managers/CurseProgressionManager.gd`, `autoload/ProgressionManager.gd`, `docs/progression.md`, `docs/progression-system.md`, `tests/scenarios/curse_fragile_optics_progression.json` — depends on: none
- `curse_fragile_optics` is a Common perk that starts at level 0 and eligible, appears in a full chest draw while eligible, becomes level 1 after it is applied, becomes level 2 after it is applied again, and is absent from a full chest draw after that second level is taken.
- While unowned, Fragile Optics reports disabled with no range bonus and no miss chance. At level 1 it reports a +10% range bonus and a documented L1 miss chance. At level 2 it reports a +20% range bonus and a documented L2 miss chance higher than L1. Both owned levels report the same documented speed threshold relative to the map baseline enemy speed.
- An owned `curse_fragile_optics` remains owned after progression save and reload, and `reset_for_new_game` returns it to unowned with the disabled config.
- `curse_fragile_optics` and `curse_overheat` keep independent state: taking either one does not own or change the other's level or config.
- Debug-build [FRAGILE_OPTICS] log line per perk apply
2. fragile-optics-miss — files: `scripts/game/actors/Tower.gd`, `scripts/game/actors/towers/PorterTower.gd`, `scripts/game/actors/Projectile.gd`, `scripts/game/actors/effects/EffectsManager.gd`, `scripts/game/actors/effects/MissVFX.gd`, `scripts/testing/HarnessValues.gd`, `tests/scenarios/curse_fragile_optics_miss.json` — depends on: 1
- A placed targeting tower's effective range is 1.10 times its unowned range at level 1 and 1.20 times at level 2, including a tower placed before the perk was taken.
- A placed Porter's effective range includes the Fragile Optics range bonus on top of its own current range while the curse is owned.
- A tower projectile hit against an enemy whose current move speed is at or below the documented threshold never voids: the enemy loses HP.
- A tower projectile hit against an enemy faster than the documented threshold that fails the miss-roll deals no damage and applies no on-hit status.
- Damage-over-time ticks still reduce HP while `curse_fragile_optics` is owned.
- A voided hit dispatches a Miss VFX through EffectsManager on that enemy.
- Debug-build [FRAGILE_OPTICS] log line per voided hit

## Criteria

- `curse_fragile_optics` is a Common perk that starts at level 0 and eligible, appears in a full chest draw while eligible, becomes level 1 after it is applied, becomes level 2 after it is applied again, and is absent from a full chest draw after that second level is taken.
- While unowned, Fragile Optics reports disabled with no range bonus and no miss chance. At level 1 it reports a +10% range bonus and a documented L1 miss chance. At level 2 it reports a +20% range bonus and a documented L2 miss chance higher than L1. Both owned levels report the same documented speed threshold relative to the map baseline enemy speed.
- An owned `curse_fragile_optics` remains owned after progression save and reload, and `reset_for_new_game` returns it to unowned with the disabled config.
- `curse_fragile_optics` and `curse_overheat` keep independent state: taking either one does not own or change the other's level or config.
- Debug-build [FRAGILE_OPTICS] log line per perk apply
- A placed targeting tower's effective range is 1.10 times its unowned range at level 1 and 1.20 times at level 2, including a tower placed before the perk was taken.
- A placed Porter's effective range includes the Fragile Optics range bonus on top of its own current range while the curse is owned.
- A tower projectile hit against an enemy whose current move speed is at or below the documented threshold never voids: the enemy loses HP.
- A tower projectile hit against an enemy faster than the documented threshold that fails the miss-roll deals no damage and applies no on-hit status.
- Damage-over-time ticks still reduce HP while `curse_fragile_optics` is owned.
- A voided hit dispatches a Miss VFX through EffectsManager on that enemy.
- Debug-build [FRAGILE_OPTICS] log line per voided hit
