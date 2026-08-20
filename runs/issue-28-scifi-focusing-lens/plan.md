# Acceptance Plan: Sci-Fi Tower Focusing Lens

manual_testing: required

## Verification

- Focused test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/scifi_focusing_lens.json"]`
- Full test: `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_tower_roster.json"]`
- Typecheck/build: `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "300"]`

## Clusters

1. focusing-lens perk catalog — files: `scripts/progression/scifi_tower.json`, `scripts/progression/managers/ScifiTowerProgressionManager.gd`, `autoload/ProgressionManager.gd`, `tests/scenarios/scifi_focusing_lens_progression.json` — depends on: none
- At a fresh run start, `scifi_focusing_lens` is eligible at level 0.
- After `venom_miasma_bloom` is taken, a 100-choice chest draw includes `scifi_focusing_lens`.
- Applying `scifi_focusing_lens` three times reaches Common levels 1, 2, then 3 whose descriptions include +8%, +16%, and +25% respectively, with focusing-lens DPS bonuses of +8% / +16% / +25% at those levels; after level 3 the perk is no longer eligible and a 100-choice chest draw does not include it.
- After `reset_for_new_game`, `scifi_focusing_lens` is level 0 and the focusing-lens DPS bonus is inactive.
2. focusing-lens beam lock — files: `scripts/game/actors/towers/ScifiTower.gd`, `scripts/game/actors/projectiles/ScifiTowerProjectile.gd`, `tests/scenarios/scifi_focusing_lens.json` — depends on: 1
- Without owning `scifi_focusing_lens`, a sci-fi tower locked on the same target for more than 1.5s deals the same beam DPS as during the first 1.5s of that lock.
- With `scifi_focusing_lens` at level 3, once a sci-fi tower has stayed locked on the same living in-range target for more than 1.5s, that beam's DPS is 25% higher than during the first 1.5s of the same lock.
- When a sci-fi tower that owns `scifi_focusing_lens` switches to a different aim target, the focusing-lens DPS bonus is not applied until the new target has been locked for more than 1.5s.
- If lock time on the same target crosses 1.5s while a sci-fi beam is already firing, the focusing-lens DPS bonus applies to that live beam.
- While the focusing-lens DPS bonus is live, the sci-fi beam is intensified, brightened, or color-shifted versus the unfocused beam; when the bonus ends the beam returns to the unfocused appearance.
- Debug-build [FOCUSING_LENS] log line per lock-bonus activate and per lock-bonus deactivate.
- A sci-fi tower with no `scifi_focusing_lens` owned still deals scifi damage on a live wave.

## Criteria

- At a fresh run start, `scifi_focusing_lens` is eligible at level 0.
- After `venom_miasma_bloom` is taken, a 100-choice chest draw includes `scifi_focusing_lens`.
- Applying `scifi_focusing_lens` three times reaches Common levels 1, 2, then 3 whose descriptions include +8%, +16%, and +25% respectively, with focusing-lens DPS bonuses of +8% / +16% / +25% at those levels; after level 3 the perk is no longer eligible and a 100-choice chest draw does not include it.
- After `reset_for_new_game`, `scifi_focusing_lens` is level 0 and the focusing-lens DPS bonus is inactive.
- Without owning `scifi_focusing_lens`, a sci-fi tower locked on the same target for more than 1.5s deals the same beam DPS as during the first 1.5s of that lock.
- With `scifi_focusing_lens` at level 3, once a sci-fi tower has stayed locked on the same living in-range target for more than 1.5s, that beam's DPS is 25% higher than during the first 1.5s of the same lock.
- When a sci-fi tower that owns `scifi_focusing_lens` switches to a different aim target, the focusing-lens DPS bonus is not applied until the new target has been locked for more than 1.5s.
- If lock time on the same target crosses 1.5s while a sci-fi beam is already firing, the focusing-lens DPS bonus applies to that live beam.
- While the focusing-lens DPS bonus is live, the sci-fi beam is intensified, brightened, or color-shifted versus the unfocused beam; when the bonus ends the beam returns to the unfocused appearance.
- Debug-build [FOCUSING_LENS] log line per lock-bonus activate and per lock-bonus deactivate.
- A sci-fi tower with no `scifi_focusing_lens` owned still deals scifi damage on a live wave.
