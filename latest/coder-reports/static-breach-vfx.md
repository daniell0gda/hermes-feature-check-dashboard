# Coder report: static-breach-vfx

## Changed files
- `scripts/game/actors/effects/StaticBreachVFX.gd` — new; OverheatGlowVFX-pattern Node3D with own-mesh ChargeShell (HighlightShaderUtils fallback material, STATIC_CHARGE_STACKING preset) and a one-shot ShatterFlash (swelling white-electric sphere, 0.25s)
- `scripts/utils/HighlightShaderUtils.gd` — new `STATIC_CHARGE_STACKING` HighlightType preset (electric-blue, fast pulse)
- `scripts/game/actors/effects/EffectsManager.gd` — `show_static_charge()` / `clear_static_charge()` / `play_static_breach_flash()` + `static_breach_flashes` counter
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — wires VFX calls on first charge, on breach, and on reset
- `scripts/testing/HarnessValues.gd` — enemy report fields `static_charge_vfx` (visible ChargeShell count) and `static_breach_flash` (flash counter)

## Criteria
- Stacking-charge highlight while >=1 charge, built via HighlightShaderUtils preset factory, clears on reset — Done
- Distinct shatter flash on the breaching hit — Done. No shield-crack asset exists in the repo (searched: only icon_shield.png HUD icon), so the flash is a distinct one-shot emissive mesh rather than an asset reuse.

## Commands and results
- `--harness=res://tests/scenarios/static_breach_vfx.json` — exit 0; pass headless (screenshots skipped headless per harness rule; run windowed for pixels)

## Notes
- The wash lives on the VFX node's OWN mesh via material_override (same trap/sidestep as OverheatGlowVFX/BloodMoneyAura) so it never collides with enemy effect material restores; `materials_clean` stays true.
