# Request: Water Tower — Water Pressure perk (#46)

Source issue: https://github.com/daniell0gda/poke-defense-godot/issues/46

## Summary
Add a new Common progression perk `water_pressure` (levels 1–3) for the Water tower:
Water's own damage against already-Wet targets is increased by +20% / +35% / +50%.

## Context
- Only Electric currently benefits from Wet status (`electric_wet_conduction`); Water gets nothing.
- Follow existing Common perk patterns (e.g. `electric_wet_conduction`) for data, unlock, UI listing, and tests.

## Done when
- New Common `water_pressure` (L1–3): Water's own damage vs already-Wet targets +20%/+35%/+50%.
- No new visual required — pure conditional damage% modifier against the existing Wet status.

manual_testing: none (pure stat modifier; no new visible mechanic). Headless harness verification is sufficient unless implementation adds visible UI beyond standard perk listing.
