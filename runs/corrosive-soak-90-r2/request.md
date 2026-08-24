# Request: Continue issue #90 — Progression: Corrosive Soak perk (Floodgate)

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/90
Workspace: /workspace/git-workspaces/poke-defense-godot/issue-progression-corrosive-soak-perk-floodgat
Branch: issue/progression-corrosive-soak-perk-floodgat (reset to origin/master @ e7910d0; prior r1 source restored and 3-way merged)
Runner: project `godot-td`, workspace `poke-defense-godot/issue-progression-corrosive-soak-perk-floodgat`. Do NOT invent other runner/workspace names.

Request id: corrosive-soak-90-r2

## Continuation (do not start from scratch)

r1 (`corrosive-soak-90-r1`) planned, then the code worker died (~2026-08-23T18:32Z) before check. Source was left uncommitted. Historical r1 artifacts (plan, clusters, harness timeout) are context only — re-verify on this tree.

Already on the worktree (keep and finish; do not rewrite from scratch):
- Perk `floodgate_corrosive_soak` Unique 3 levels 25/45/70% in `scripts/progression/floodgate_tower.json`
- `FloodgateTowerProgressionManager.gd` apply/reset/config + `ProgressionManager.get_floodgate_corrosive_soak_config()`
- Corroded status on Enemy / EnemyStatusController; rust tint via `WaterSubmersionSystem.set_enemy_corroded`
- Amplification in `EnemyHealthController._apply_corrosive_soak_if_needed` excluding `tower_type_id == floodgate`
- Floodgate discharge marks via `_mark_corroded_if_needed`
- Harness helpers + `tests/scenarios/floodgate_corrosive_soak.json`

Master moved 14 commits; `ProgressionManager.gd`, `HarnessActions.gd`, `HarnessValues.gd` were 3-way merged with undermining + press_button + reward_popups. Preserve those master behaviors.

## r1 harness failure (must fix)

`.gen/harness/floodgate_corrosive_soak/result.json` status=timeout:
- Perk apply and Corroded mark worked (`corroded_count==1`, `amplification==0.25`, `corroded_marked:true`)
- Then `armor_hit` (balista, armor_damage 40) followed by `enemies.Alien.armor == 950.0` saw **actual 0.0**
- Likely observation of an unarmored spawn instead of the staged Alien (index-0 vs max-armor-by-id). `HarnessValues._enemy_report` now takes max armor per id — assert via `enemies.Alien.armor` (report) not `enemy.armor` (index 0). Stage armor immediately before the hit and wait 2–3 poll ticks after.
- Expected L1 math: staged 1000 − 40×1.25 = 950. Floodgate follow-up must match unowned control. HP unchanged.

Treat r1 result as stale. Re-run the focused harness fresh after any harness/scenario fix.

## Feature

New Unique progression perk `corrosive_soak` for Floodgate Tower only, 3 levels:
- Enemies hit by Floodgate's discharge gain a "Corroded" status.
- While Corroded, armor-damage taken from **all other towers'** hits is increased by +25% / +45% / +70% by level.
- Floodgate's own hits are unaffected by its own Corroded status (setup effect, not self-buff).
- Implemented in `FloodgateTowerProgressionManager.gd` alongside `floodgate_saltwater_purge`.
- Visual: extend wet/soak shader (`WaterSubmersionSystem`) with a distinct rust tint — no new VFX class.

## Acceptance criteria
1. Perk defined with 3 levels and correct amplification values (25/45/70%), Floodgate-only, type Unique.
2. Corroded status applied on Floodgate discharge hits; amplifies armor-dmg from all towers EXCEPT Floodgate itself.
3. Editor gate passes (`godot --headless --path . --editor --quit-after 300`).
4. Focused headless gameplay harness proves: enemy hit by Floodgate → subsequent armor-dmg hit from another tower is amplified per level; Floodgate-own follow-up is not amplified.
5. Manual testing: required (rust tint is player-facing). Windowed screenshots/GIF via runner, never --headless for manual evidence. Include `ui_feels_broken: yes|no`.

## Notes
- Use exact scene argument before user args in harness commands.
- Inspect raw Godot stdout for Parse Error / Failed loading resource, not just harness status=pass.
- Do not commit/push/close.
- Do not revert unrelated master files. Ignore `.glb` LFS noise; do not commit models.
