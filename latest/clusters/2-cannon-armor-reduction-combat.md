# Cluster 2: cannon-armor-reduction-combat

- owned files: `scripts/game/actors/enemy/parts/EnemyHealthController.gd`, `tests/scenarios/cannon_siege_breaker_progression.json`
- dependencies: 1
- parallel: false

## Acceptance criteria

- A direct hit attributed to a cannon tower against an enemy whose armor survives the hit deals HP damage reduced by the siege_breaker-scaled multiplier rather than the fixed 0.5 penalty (e.g. 10 base damage vs surviving armor yields 3.5/2.0/0.0 reduction at levels 1–3, exact arithmetic via the harness armor_hit path with tower_type_id "cannon").
- With siege_breaker unowned, an armored enemy hit by a cannon takes HP damage under the unchanged 0.5 armor penalty (existing armour behaviour regression).
- With siege_breaker owned at any level, a hit attributed to a non-cannon tower against surviving armor still takes the unchanged 0.5 penalty.
- Owning siege_breaker does not change any enemy's armor value directly: armor drains at its normal rate from non-cannon hits, and cannon hits consume the same armor-damage amount they did before the perk.
- Once the enemy's armor is depleted (or the enemy has none), cannon damage is applied at full effectiveness regardless of siege_breaker level.

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/cannon_siege_breaker_progression.json"]`
- Full test: `["bash", "-c", "for f in $(find tests/scenarios -name '*.json' | sort); do godot --headless --path . res://scenes/Main.tscn -- \"--harness=res://${f%.json}\" || exit 1; done"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`
