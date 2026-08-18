## ✅ Done

## ⬜ Pending
- After a porter port into the underground section completes, the ported enemy reports as underground.
- Ground-level combat towers do not acquire or attack an enemy that reports as underground.
- Ground-level projectiles already in flight do not damage a target after that target reports as underground.
- When an enemy is thrown or launched from the underground exit, that enemy reports as not underground.
- After that launch, a ground-level combat tower can acquire and damage the same enemy again.
- Underground-placed attackers still damage enemies that report as underground.
- Surface enemies that were never ported remain acquirable and damageable by ground-level combat towers.
- Debug-build [UNDERGROUND] log line per underground-flag set on port
- Debug-build [UNDERGROUND] log line per underground-flag clear on exit launch

## ❌ Impossible
- A focused harness scenario at tests/scenarios/underground_ground_tower_exclusion.json proves the underground-flag set after porter port, ground-tower targeting exclusion including in-flight projectiles, flag clear after exit launch, post-launch reacquisition, underground-attacker damage, and never-ported surface targetability. — reason: godot command unavailable in verification environment (exit 127); focused harness reports path_blocked for Floodgate placement (concrete technical blocker for green run)
