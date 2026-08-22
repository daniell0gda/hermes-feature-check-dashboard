# Acceptance Plan: perk-siege-breaker

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cannon_siege_breaker_progression.json"]`
- Full test: `["bash", "-c", "for f in $(find tests/scenarios -name '*.json' | sort); do godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://${f%.json}\" || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

manual_testing: required

## Clusters

1. siege-breaker-perk-definition — files: `scripts/progression/cannon_tower.json`, `scripts/progression/managers/CannonTowerProgressionManager.gd`, `autoload/ProgressionManager.gd` — depends on: none
- The progression pool defines a perk named `siege_breaker` in the Cannon progression file, typed Unique, restricted to the cannon tower only, with exactly 3 levels.
- A ProgressionManager query returns the armor-damage-reduction multiplier for Cannon hits as 0.5 while the perk is unowned, 0.35 at level 1, 0.20 at level 2, and 0.0 at level 3.
- Re-applying or replaying saved perk levels lands on the level's exact multiplier instead of compounding (level values are absolute per level).
- Debug-build [CannonProgression] log line per siege_breaker level application carrying the perk level and resulting armor-reduction multiplier.
2. cannon-armor-reduction-combat — files: `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `tests/scenarios/cannon_siege_breaker_progression.json` — depends on: 1
- A direct hit attributed to a cannon tower against an enemy whose armor survives the hit deals HP damage reduced by the siege_breaker-scaled multiplier rather than the fixed 0.5 penalty (e.g. 10 base damage vs surviving armor yields 3.5/2.0/0.0 reduction at levels 1–3, exact arithmetic via the harness armor_hit path with tower_type_id "cannon").
- With siege_breaker unowned, an armored enemy hit by a cannon takes HP damage under the unchanged 0.5 armor penalty (existing armor behaviour regression).
- With siege_breaker owned at any level, a hit attributed to a non-cannon tower against surviving armor still takes the unchanged 0.5 penalty.
- Owning siege_breaker does not change any enemy's armor value directly: armor drains at its normal rate from non-cannon hits, and cannon hits consume the same armor-damage amount they did before the perk.
- Once the enemy's armor is depleted (or the enemy has none), cannon damage is applied at full effectiveness regardless of siege_breaker level.

## Criteria

- The progression pool defines a perk named `siege_breaker` in the Cannon progression file, typed Unique, restricted to the cannon tower only, with exactly 3 levels.
- A ProgressionManager query returns the armor-damage-reduction multiplier for Cannon hits as 0.5 while the perk is unowned, 0.35 at level 1, 0.20 at level 2, and 0.0 at level 3.
- Re-applying or replaying saved perk levels lands on the level's exact multiplier instead of compounding (level values are absolute per level).
- Debug-build [CannonProgression] log line per siege_breaker level application carrying the perk level and resulting armor-reduction multiplier.
- A direct hit attributed to a cannon tower against an enemy whose armor survives the hit deals HP damage reduced by the siege_breaker-scaled multiplier rather than the fixed 0.5 penalty (e.g. 10 base damage vs surviving armor yields 3.5/2.0/0.0 reduction at levels 1–3, exact arithmetic via the harness armor_hit path with tower_type_id "cannon").
- With siege_breaker unowned, an armored enemy hit by a cannon takes HP damage under the unchanged 0.5 armor penalty (existing armour behaviour regression).
- With siege_breaker owned at any level, a hit attributed to a non-cannon tower against surviving armor still takes the unchanged 0.5 penalty.
- Owning siege_breaker does not change any enemy's armor value directly: armor drains at its normal rate from non-cannon hits, and cannon hits consume the same armor-damage amount they did before the perk.
- Once the enemy's armor is depleted (or the enemy has none), cannon damage is applied at full effectiveness regardless of siege_breaker level.

## Manual testing note

Player-visible story: a Cannon with siege_breaker hitting an armored enemy must show the existing
shield-crack/shatter flash pattern (the Exposed Plating #87-series effect reused via the enemy VFX
path); no second, new effect may appear. Capture windowed PNG/GIF evidence per team-work rules —
headless-only verification is not sufficient.
