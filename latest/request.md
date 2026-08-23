# Request — Issue #91: Progression perk "Undermining"

- **Project:** poke-defense-godot (runner key `godot-td`, workspace `poke-defense-godot/issue-perk-undermining`)
- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/91
- **Branch:** issue/perk-undermining (cut fresh from origin/master @ d241462)
- **Labels:** status:in-progress, priority:medium, type:common, type:progression-perk, type:traps, type:armor

## Problem
None of the underground traps (`trap_01`/Jaw, `trap_03`/Spike, `trap_02`/Saw, `trap_05`/Blender, `data/towers.xml:35-38`) have an `armor_dmg` stat, so armored enemies routed underground are just as shielded there as on the surface.

## Done when (exact acceptance criteria from the issue)
1. New perk `undermining`, type Common, traps only, 3 levels.
2. All 4 traps gain flat armor-damage per hit — L1 8, L2 15, L3 25 — added the same way Ballista's `armor_dmg` is read via `TowersConfig.get_armor_damage()` (`systems/TowersConfig.gd:184-186`).
3. Does not affect any surface tower.
4. Visual: tint traps' existing hit-impact effect to flag the armor-strip portion; if a given trap currently has no impact VFX at all, note that as a pre-existing gap rather than scope creep for this issue.

## Notes / redo guidance for workers
- Runner workspace naming pitfall: use exactly project key `godot-td` and workspace `poke-defense-godot/issue-perk-undermining`. Invented names cause HTTP 422 chdir failures that look like infra blockers.
- Native Linux Godot verification through the runner only. Editor gate: `godot --headless --path . --editor --quit-after 300`. Gameplay harnesses need explicit scene arg before user args.
- Visible player-facing perk → manual_testing should be `required`; manual-tester must never use `--headless`; windowed evidence via `--rendering-method gl_compatibility --audio-driver Dummy` when Vulkan fails; screenshots/GIFs into `.gen/screenshots/`.
- Check profile must emit the literal line `classification: <pass|fixable|design_failure|blocked>` (lowercase value, no bold) so leader routing parses it.
- Historical reference only (previous abandoned claim): commit 84c8e60 "bird view centers..." was on this branch before reset — unrelated to this issue; branch was reset to origin/master d241462.
