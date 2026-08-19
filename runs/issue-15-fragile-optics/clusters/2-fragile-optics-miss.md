# Cluster 2: fragile-optics-miss

- cluster_id: 2-fragile-optics-miss
- owned file scope: `scripts/game/actors/Tower.gd`, `scripts/game/actors/towers/PorterTower.gd`, `scripts/game/actors/Projectile.gd`, `scripts/game/actors/effects/EffectsManager.gd`, `scripts/game/actors/effects/MissVFX.gd`, `scripts/testing/HarnessValues.gd`, `tests/scenarios/curse_fragile_optics_miss.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- A placed targeting tower's effective range is 1.10 times its unowned range at level 1 and 1.20 times at level 2, including a tower placed before the perk was taken.
- A placed Porter's effective range includes the Fragile Optics range bonus on top of its own current range while the curse is owned.
- A tower projectile hit against an enemy whose current move speed is at or below the documented threshold never voids: the enemy loses HP.
- A tower projectile hit against an enemy faster than the documented threshold that fails the miss-roll deals no damage and applies no on-hit status.
- Damage-over-time ticks still reduce HP while `curse_fragile_optics` is owned.
- A voided hit dispatches a Miss VFX through EffectsManager on that enemy.
- Debug-build [FRAGILE_OPTICS] log line per voided hit

## Verification

- Focused test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/curse_fragile_optics_miss.json"]`
- Full test: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/curse_overheat_progression.json"]`
- Typecheck/build: `run_project_cmd` project=`godot-td` workspace=`poke-defense-godot/issue-fragile-optics` cmd=`["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
