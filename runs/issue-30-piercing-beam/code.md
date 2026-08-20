# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/progression/scifi_tower.json` — modified
- `scripts/progression/managers/ScifiTowerProgressionManager.gd` — modified
- `autoload/ProgressionManager.gd` — modified
- `tests/scenarios/scifi_piercing_beam_progression.json` — new
- `scripts/game/actors/projectiles/ScifiTowerProjectile.gd` — modified
- `scripts/game/actors/towers/ScifiTower.gd` — modified
- `scripts/game/TowerManager.gd` — modified
- `tests/scenarios/scifi_piercing_beam.json` — new
- `.gen/changes.md` — new

## Criteria
- Unique `scifi_piercing_beam` is eligible at level 0, can be applied through three levels as a Sci-Fi Unique, and is ineligible after level 3. — Done
- A full chest draw includes `scifi_piercing_beam` only after a Sci-Fi tower is placed. — Done
- After `scifi_piercing_beam` exists, unowned Capacitor Bank still reports yaw tolerance 8.0 for base 8.0 and level 3 still reports 94.0. — Done
- The `progression_chest_pool` scenario still passes after `scifi_piercing_beam` is added. — Done
- Without `scifi_piercing_beam`, a Sci-Fi beam damages only the primary target even when a second living enemy is roughly behind it on the beam line. — Done
- After applying `scifi_piercing_beam` once, a Sci-Fi beam that has a second living enemy roughly behind the primary on the beam line damages that second enemy at reduced damage while still damaging the primary. — Done
- After applying `scifi_piercing_beam` to a higher tier, a third living enemy roughly behind the second on the same beam line also takes reduced Sci-Fi beam damage. — Done
- Extra pierce hits go to the next living enemies along the beam direction in projected-distance order, not to an off-line neighbor or to a farther on-line enemy ahead of a closer one. — Done
- When no second living enemy lies on the beam line, owning `scifi_piercing_beam` does not add extra Sci-Fi damage beyond the primary target. — Done
- The Sci-Fi beam visual extends through the pierce targets instead of ending at the primary. — Done (headless via `get_beam_extends_past_primary`; windowed pixels not inspected)
- Debug-build [PIERCING_BEAM] log line per pierce event with hit count and target identity — Done (prints in debug; harness out.log is often empty)

## Commands and results
- `["godot", "--version"]` — exit code 0; `4.4.1.stable.official.49a5bc7b6`
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]` — exit code 0; import/parse gate
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_piercing_beam_progression.json"]` — exit code 0; `.gen/harness/scifi_piercing_beam_progression/result.json` status pass
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_piercing_beam.json"]` — exit code 0; `.gen/harness/scifi_piercing_beam/result.json` status pass
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/progression_chest_pool.json"]` — exit code 0; status pass (Unique skipped until Sci-Fi is placed)

## Notes
- Runner: project `godot-td`, workspace `poke-defense-godot/issue-piercing-beam`.
- L1 extra_hits=1 ratio=0.5; L2 extra_hits=2 ratio=0.5; L3 extra_hits=2 ratio=0.75; line_width=0.45.
- Windowed screenshots still needed for visible 2–3-target beam; headless skipped `pierce_line2` / `pierce_line3`.
- Full suite `smoke_tower_roster` was not run (checker owns full-suite).
\n