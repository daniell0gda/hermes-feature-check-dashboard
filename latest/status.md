## ✅ Done
- After Underground re-init, Underground has no leftover live child whose name starts with `Chest_Cave` from the previous init.
- After Underground re-init, Underground has no leftover live child whose name starts with `Cave_` from the previous init.
- After Underground re-init, Underground has no leftover live child whose name starts with `CaveDarkness_` from the previous init.
- After Underground re-init, Underground still has a live `DamageGroup` child.
- Debug-build [UNDERGROUND] log line per cleanup sweep naming freed children
- The harness can read the live `Chest_Cave*` child count under Underground.
- A focused harness scenario creates a cave chest fixture, forces map re-init (load_map twice or equivalent Underground re-init), and ends with exactly one live `Chest_Cave*` child.
- After the chest-fixture re-init, a tower can still be placed on the loaded map.

## ⬜ Pending

## ❌ Impossible
