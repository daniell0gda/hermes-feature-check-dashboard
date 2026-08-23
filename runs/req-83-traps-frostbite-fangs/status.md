## ✅ Done
- Add Unique `traps_frostbite_fangs` (L1-3).
- Trap hits apply chill/slow via `EffectsManager.apply_frozen`.
- Duration or magnitude scales per level.
- Reuse existing frost overlay VFX from `apply_frozen`.
- Preserve existing frozen-effect ownership and stacking semantics.
- Focused coverage for level scaling and trap-triggered visual/effect behavior.
- Verify the relevant trap gameplay path, not only generic parsing.

## ⬜ Pending
- Confirm frost overlay renders when the effect is triggered from a trap — headless state proof is green (`ice_slow_fx >= 1`, `slow_magnitude == 0.6` on live trap hit, `.gen/harness/traps_frostbite_fangs_progression/result.json` status=pass); pixel-visible confirmation awaits the windowed manual-tester screenshot (checkpoint `frostbite_fangs_chilled_hit` wired in scenario; skipped under --headless).

## ❌ Impossible
