# Cluster 2: projectile-flood-wet-splash

- owned files: `scripts/game/actors/Projectile.gd`, `scripts/game/actors/effects/EffectsManager.gd`
- dependencies: 1
- parallel: false

## Acceptance criteria

- With `water_conductive_flood` enabled, a Water projectile hit applies Wet to every enemy within the perk's small radius of the hit target, not only the direct target (at least two enemies Wet from one hit).
- With `water_conductive_flood` enabled, an enemy outside the perk's small radius of the hit target is not Wetted by that hit.
- Without `water_conductive_flood`, a Water projectile hit applies Wet only to the direct target and leaves nearby enemies un-Wetted (unchanged pre-perk behavior).
- When `water_conductive_flood` is enabled, the hit's existing splash effect visually covers at least the perk's small radius on impact, using the same small-radius splash visual approach the Ice tower cone effects use; the effect reads clearly at normal game speed.
- Wet applied by the flood renders per-enemy through the existing EnemyHealthBar status icons, so each affected enemy visibly shows its Wet status without any new asset.
- Debug-build `[WATER-FLOOD]` log lines exist for both key events: perk application recording the configured radius, and a flood hit recording the hit target, the number of enemies Wetted, and the radius used.

## Verification

All commands run via the approved runner (`run_project_cmd`, project `godot-td`,
workspace `godot-td/issue-water-conductive-flood-wet-splash`), from repo root.

- Focused test: `["python3", "tests/run_all_shard.py", "0", "1", "water_conductive_flood"]`
- Full test: `["python3", "tests/run_all_shard.py", "0", "1"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

Note: the splash-radius visual criterion requires a windowed screenshot run; headless
screenshot checkpoints are skipped by design (see game-test skill).
