# Acceptance Plan: Sci-Fi Piercing Beam

## Verification

manual_testing: required

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_piercing_beam.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. unique-perk — files: `scripts/progression/scifi_tower.json`, `scripts/progression/managers/ScifiTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `tests/scenarios/scifi_piercing_beam_progression.json`, `tests/scenarios/progression_chest_pool.json` — depends on: none
- Unique `scifi_piercing_beam` is eligible at level 0, can be applied through three levels as a Sci-Fi Unique, and is ineligible after level 3.
- A full chest draw includes `scifi_piercing_beam` only after a Sci-Fi tower is placed.
- After `scifi_piercing_beam` exists, unowned Capacitor Bank still reports yaw tolerance 8.0 for base 8.0 and level 3 still reports 94.0.
- The `progression_chest_pool` scenario still passes after `scifi_piercing_beam` is added.
2. piercing-beam-combat — files: `scripts/game/actors/projectiles/ScifiTowerProjectile.gd`, `scripts/game/actors/towers/ScifiTower.gd`, `tests/scenarios/scifi_piercing_beam.json` — depends on: 1
- Without `scifi_piercing_beam`, a Sci-Fi beam damages only the primary target even when a second living enemy is roughly behind it on the beam line.
- After applying `scifi_piercing_beam` once, a Sci-Fi beam that has a second living enemy roughly behind the primary on the beam line damages that second enemy at reduced damage while still damaging the primary.
- After applying `scifi_piercing_beam` to a higher tier, a third living enemy roughly behind the second on the same beam line also takes reduced Sci-Fi beam damage.
- Extra pierce hits go to the next living enemies along the beam direction in projected-distance order, not to an off-line neighbor or to a farther on-line enemy ahead of a closer one.
- When no second living enemy lies on the beam line, owning `scifi_piercing_beam` does not add extra Sci-Fi damage beyond the primary target.
- The Sci-Fi beam visual extends through the pierce targets instead of ending at the primary.
- Debug-build [PIERCING_BEAM] log line per pierce event with hit count and target identity

## Criteria

- Unique `scifi_piercing_beam` is eligible at level 0, can be applied through three levels as a Sci-Fi Unique, and is ineligible after level 3.
- A full chest draw includes `scifi_piercing_beam` only after a Sci-Fi tower is placed.
- After `scifi_piercing_beam` exists, unowned Capacitor Bank still reports yaw tolerance 8.0 for base 8.0 and level 3 still reports 94.0.
- The `progression_chest_pool` scenario still passes after `scifi_piercing_beam` is added.
- Without `scifi_piercing_beam`, a Sci-Fi beam damages only the primary target even when a second living enemy is roughly behind it on the beam line.
- After applying `scifi_piercing_beam` once, a Sci-Fi beam that has a second living enemy roughly behind the primary on the beam line damages that second enemy at reduced damage while still damaging the primary.
- After applying `scifi_piercing_beam` to a higher tier, a third living enemy roughly behind the second on the same beam line also takes reduced Sci-Fi beam damage.
- Extra pierce hits go to the next living enemies along the beam direction in projected-distance order, not to an off-line neighbor or to a farther on-line enemy ahead of a closer one.
- When no second living enemy lies on the beam line, owning `scifi_piercing_beam` does not add extra Sci-Fi damage beyond the primary target.
- The Sci-Fi beam visual extends through the pierce targets instead of ending at the primary.
- Debug-build [PIERCING_BEAM] log line per pierce event with hit count and target identity
