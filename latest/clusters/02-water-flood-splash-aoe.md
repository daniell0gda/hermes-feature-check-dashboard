# Cluster 2: water-flood-splash-aoe

owned file scope: `scripts/game/actors/Projectile.gd`, `scripts/game/actors/effects/EffectsManager.gd`

dependencies: 1

parallel: false

## Acceptance criteria

- With `water_conductive_flood` applied, a Water projectile hit applies Wet to every alive enemy within the perk's configured radius of the hit target; a scenario proves at least two enemies Wet from one hit, not only the direct target.
- Without the perk, a Water projectile hit applies Wet only to the direct target; nearby enemies within the would-be radius stay non-Wet (regression guard on existing behavior).
- Enemies outside the configured radius are not made Wet by the hit even when the perk is applied.
- On a Water hit with the perk applied, the spawned splash effect visually extends at least to the perk's configured radius so the covered area matches the Wet area (same small-radius visual approach the Ice cone uses).
- Wet applied by the flood perk renders per-enemy through the existing EnemyHealthBar status icon for each affected enemy, with no new asset required.
- Debug-build [WATER-FLOOD] log line per multi-enemy Wet application event, naming the hit target and the count of enemies Wetted in the radius.

## Verification commands

- Focused test: `["python3", "tests/run_all_shard.py", "0", "1", "water_conductive_flood"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--import", "--quit-after", "5"]`
