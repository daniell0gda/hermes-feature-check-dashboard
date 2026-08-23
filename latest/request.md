# Request: Sundering Bolts perk (issue #88)

- Issue: https://github.com/daniell0gda/poke-defense-godot/issues/88
- Project: godot-td (runner key `godot-td`, workspace `poke-defense-godot/issue-perk-sundering-bolts`)
- Branch: issue/perk-sundering-bolts (cut fresh from origin/master @ 97ed515)
- Slug: perk-sundering-bolts

## Feature

New progression perk `sundering_bolts`, 3 levels (L1 10%, L2 20%, L3 35%). Each level applies a % of the hit's **final** damage (after all other damage-modifying perks) as additional armor-damage on the same hit, computed at the shared `EnemyHealthController.take_damage()` call site. Applies to every tower except Porter (damage=0). Ballista's flat `armor_dmg=20` still stacks on top. No new VFX — reuses existing armor bar.

## Acceptance criteria

1. Perk `sundering_bolts` exists with 3 levels: 10% / 20% / 35% of final hit damage converted to armor-damage.
2. Conversion uses the hit's final post-perk damage, not static base value from towers.xml.
3. Applied in the shared take_damage path so all damaging towers benefit; Porter unaffected (0 damage → 0 armor-damage).
4. Ballista flat armor_dmg stacks additively with the perk bonus.
5. Existing armor bar reflects the drain (no new VFX needed).
6. Editor/import gate passes; focused gameplay harness through the runner (`godot-td` / `poke-defense-godot/issue-perk-sundering-bolts`) proves the armor-strip math for a representative tower (Ballista) at each level, plus a no-perk baseline.

## Notes / redo guidance

- Runner key is `godot-td`; workspace is `poke-defense-godot/issue-perk-sundering-bolts`. Do not invent other workspace names (HTTP 422 chdir otherwise).
- Use explicit scene argument before user args in harness commands; never rely on project.godot main scene.
- Manual testing: visible armor-bar drain — set `manual_testing: required` if a still-captureable user story exists (armor bar draining after hits). Windowed evidence via runner, screenshots to `.gen/screenshots/`.
