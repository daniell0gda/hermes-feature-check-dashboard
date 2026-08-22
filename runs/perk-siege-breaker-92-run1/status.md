## ✅ Done
(none — global build/test gate failed)

## ⬜ Pending
- A new progression entry named `siege_breaker` exists in the Cannon progression file with type Unique, exactly 3 levels, and tower compatibility limited to `cannon`; the progression system reports it eligible while unowned and its level never exceeds 3.
- Each of the three `siege_breaker` levels carries a player-readable description stating its armor-penalty figure (35% / 20% / 0% remaining penalty), so the progression modal shows a meaningful label at every level.
- With `siege_breaker` unowned, the progression config exposes the baseline armor penalty of 0.5; at levels 1, 2 and 3 it exposes 0.35, 0.20 and 0.0 respectively, matching the level data in the JSON.
- Taking `siege_breaker` levels then starting a fresh game resets its level to 0 and restores the baseline 0.5 penalty (save/load and reset behave like every other progression).
- With `siege_breaker` unowned, a scripted hit attributed to tower type `cannon` against an enemy with armor remaining deals HP damage reduced by the baseline factor 0.5 (existing behavior unchanged).
- With `siege_breaker` at level 1, the same Cannon-attributed hit against an armored enemy deals HP damage reduced by 0.35 instead of 0.5.
- With `siege_breaker` at level 2, the same Cannon-attributed hit deals HP damage reduced by 0.20 instead of 0.5.
- With `siege_breaker` at level 3, a Cannon-attributed hit against an enemy with armor remaining deals its full HP damage (no armor penalty applied).
- At every `siege_breaker` level, a hit attributed to any non-Cannon tower type against an enemy with armor remaining still deals HP damage reduced by the baseline 0.5 factor.
- `siege_breaker` never changes the enemy's armor value: after equal Cannon hits with the perk owned vs unowned, the armor remaining is identical and other towers' hits drain armor at the normal rate (the existing armor-regression scenario `enemy_armor_ballista` still passes unchanged).
- Debug-build `[SiegeBreaker]` log line per reduced-penalty application on a Cannon hit, naming the enemy id and current perk level.
- A Cannon hit that benefits from the `siege_breaker` armor penalty reduction triggers the existing one-shot armor-crack shatter flash (the StaticBreach breaching-hit flash path, observable as the harness's `static_breach_flash` counter advancing); no new second VFX effect is introduced.

## ❌ Impossible
(none)
