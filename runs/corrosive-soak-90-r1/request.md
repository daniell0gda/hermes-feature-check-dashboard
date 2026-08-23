# Request: Implement issue #90 — Progression: Corrosive Soak perk (Floodgate)

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/90
Workspace: /workspace/git-workspaces/poke-defense-godot/issue-progression-corrosive-soak-perk-floodgat
Branch: issue/progression-corrosive-soak-perk-floodgat (cut from origin/master @ 97ed515)
Runner: project `godot-td`, workspace `poke-defense-godot/issue-progression-corrosive-soak-perk-floodgat`. Do NOT invent other runner/workspace names.

## Feature

New Unique progression perk `corrosive_soak` for Floodgate Tower only, 3 levels:
- Enemies hit by Floodgate's discharge gain a "Corroded" status.
- While Corroded, armor-damage taken from **all other towers'** hits is increased by +25% / +45% / +70% by level.
- Floodgate's own hits are unaffected by its own Corroded status (it has no armor_dmg; setup effect, not self-buff).
- Implemented in `FloodgateTowerProgressionManager.gd` alongside the existing `floodgate_saltwater_purge` pattern.
- Visual: extend Floodgate's existing wet/soak shader (`WaterSubmersionSystem`) with a distinct rust tint on corroded enemies — no new VFX class.

Reference files: `data/towers.xml` (Floodgate entry lines ~13-15), existing perk patterns in other tower progression managers (e.g. siege-breaker/static-breach/sundering-bolts/undermining perks) for armor_dmg mechanics and tests.

## Acceptance criteria
1. Perk defined with 3 levels and correct amplification values (25/45/70%), Floodgate-only, type Unique.
2. Corroded status applied on Floodgate discharge hits; amplifies armor-dmg from all towers EXCEPT Floodgate itself.
3. Editor gate passes (`godot --headless --path . --editor --quit-after 300`).
4. Focused headless gameplay harness proves: enemy hit by Floodgate → subsequent armor-dmg hit from another tower is amplified per level; Floodgate-own follow-up is not amplified.
5. Manual testing: required if any player-facing visual (rust tint) is implemented; windowed screenshots via runner, never --headless for manual test evidence.

## Notes
- Use exact scene argument before user args in harness commands.
- Inspect raw Godot stdout for Parse Error / Failed loading resource, not just harness status=pass.
