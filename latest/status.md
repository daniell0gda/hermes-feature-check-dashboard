## ✅ Done
- Unique `scifi_piercing_beam` is eligible at level 0, can be applied through three levels as a Sci-Fi Unique, and is ineligible after level 3.
- A full chest draw includes `scifi_piercing_beam` only after a Sci-Fi tower is placed.
- After `scifi_piercing_beam` exists, unowned Capacitor Bank still reports yaw tolerance 8.0 for base 8.0 and level 3 still reports 94.0.
- The `progression_chest_pool` scenario still passes after `scifi_piercing_beam` is added.

## ⬜ Pending
- Without `scifi_piercing_beam`, a Sci-Fi beam damages only the primary target even when a second living enemy is roughly behind it on the beam line. — quality: /workspace/git-workspaces/poke-defense-godot/issue-piercing-beam/scripts/game/actors/projectiles/ScifiTowerProjectile.gd: type cast using 'as' forbidden by /opt/data/coding_rules.md (also in ScifiTower.gd)
- After applying `scifi_piercing_beam` once, a Sci-Fi beam that has a second living enemy roughly behind the primary on the beam line damages that second enemy at reduced damage while still damaging the primary. — quality: /workspace/git-workspaces/poke-defense-godot/issue-piercing-beam/scripts/game/actors/projectiles/ScifiTowerProjectile.gd: type cast using 'as' forbidden by /opt/data/coding_rules.md (also in ScifiTower.gd)
- After applying `scifi_piercing_beam` to a higher tier, a third living enemy roughly behind the second on the same beam line also takes reduced Sci-Fi beam damage. — quality: /workspace/git-workspaces/poke-defense-godot/issue-piercing-beam/scripts/game/actors/projectiles/ScifiTowerProjectile.gd: type cast using 'as' forbidden by /opt/data/coding_rules.md (also in ScifiTower.gd)
- Extra pierce hits go to the next living enemies along the beam direction in projected-distance order, not to an off-line neighbor or to a farther on-line enemy ahead of a closer one. — quality: /workspace/git-workspaces/poke-defense-godot/issue-piercing-beam/scripts/game/actors/projectiles/ScifiTowerProjectile.gd: type cast using 'as' forbidden by /opt/data/coding_rules.md (also in ScifiTower.gd)
- When no second living enemy lies on the beam line, owning `scifi_piercing_beam` does not add extra Sci-Fi damage beyond the primary target. — quality: /workspace/git-workspaces/poke-defense-godot/issue-piercing-beam/scripts/game/actors/projectiles/ScifiTowerProjectile.gd: type cast using 'as' forbidden by /opt/data/coding_rules.md (also in ScifiTower.gd)
- The Sci-Fi beam visual extends through the pierce targets instead of ending at the primary. — quality: /workspace/git-workspaces/poke-defense-godot/issue-piercing-beam/scripts/game/actors/projectiles/ScifiTowerProjectile.gd: type cast using 'as' forbidden by /opt/data/coding_rules.md (also in ScifiTower.gd); screenshots skipped in headless
- Debug-build [PIERCING_BEAM] log line per pierce event with hit count and target identity — quality: /workspace/git-workspaces/poke-defense-godot/issue-piercing-beam/scripts/game/actors/projectiles/ScifiTowerProjectile.gd: type cast using 'as' forbidden by /opt/data/coding_rules.md (also in ScifiTower.gd)

## ❌ Impossible
