## ✅ Done
- A fresh headless run of `tests/scenarios/exposed_plating_once_per_shield.json` ends with status `pass` and every expectation met, confirming the trigger fires exactly once per shield instance (>0 to 0 transition only) at all three perk levels after the rebase.
- A fresh headless run of `tests/scenarios/exposed_plating_vfx.json` ends with status `pass` and every expectation met, including log lines containing `[EXPOSED] triggered on` and `[EXPOSED] expire on`.
- During the windowed VFX scenario, the active camera aims at the boss enemy and moves close enough that the enemy occupies a large part of the rendered frame before any screenshot checkpoint fires.
- In every screenshot and recorded frame captured by the scenario, the debug panel is hidden or positioned so it does not cover the enemy.
- The `record_frames` capture spans the whole Exposed window while zoomed on the enemy and saves more than zero real consecutive engine frames suitable for GIF export.
- In the windowed close-up run, the "during Exposed" still shows an obvious amber wash over the enemy body that differs from the "before breach" still when compared by eye.
- In the windowed close-up run, the "after expiry" still matches the "before breach" still by eye: the amber wash is gone.
- If the close-up "during" still shows no visible overlay, `ExposedVFX` is strengthened (alpha/emission energy/shell size) until the before/during frames visibly differ; metadata `exposed_vfx == true` alone never counts as passing this criterion.
- The proving PNGs and the exported GIF are copied into `.gen/screenshots/` and embedded in `.gen/manual-report.md`, which ends with a `ui_feels_broken: yes|no` verdict line.

## ⬜ Pending
- (none)

## ❌ Impossible
- (none)
