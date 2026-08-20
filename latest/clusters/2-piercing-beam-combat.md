# Cluster 2: piercing-beam-combat

- cluster ID: 2
- owned file scope: `scripts/game/actors/projectiles/ScifiTowerProjectile.gd`, `scripts/game/actors/towers/ScifiTower.gd`, `tests/scenarios/scifi_piercing_beam.json`
- dependencies: 1
- parallel: false

## Criteria

- Without `scifi_piercing_beam`, a Sci-Fi beam damages only the primary target even when a second living enemy is roughly behind it on the beam line.
- After applying `scifi_piercing_beam` once, a Sci-Fi beam that has a second living enemy roughly behind the primary on the beam line damages that second enemy at reduced damage while still damaging the primary.
- After applying `scifi_piercing_beam` to a higher tier, a third living enemy roughly behind the second on the same beam line also takes reduced Sci-Fi beam damage.
- Extra pierce hits go to the next living enemies along the beam direction in projected-distance order, not to an off-line neighbor or to a farther on-line enemy ahead of a closer one.
- When no second living enemy lies on the beam line, owning `scifi_piercing_beam` does not add extra Sci-Fi damage beyond the primary target.
- The Sci-Fi beam visual extends through the pierce targets instead of ending at the primary.
- Debug-build [PIERCING_BEAM] log line per pierce event with hit count and target identity

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_piercing_beam.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
