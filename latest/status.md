## ✅ Done
- Add Unique `traps_frostbite_fangs` (L1-3).
- Trap hits apply chill/slow via `EffectsManager.apply_frozen`.
- Duration or magnitude scales per level.
- Reuse existing frost overlay VFX from `apply_frozen`.
- Preserve existing frozen-effect ownership and stacking semantics.
- Focused coverage for level scaling and trap-triggered visual/effect behavior.
- Verify the relevant trap gameplay path, not only generic parsing.

## ⬜ Pending
- Confirm frost overlay renders when the effect is triggered from a trap — headless state proof is green (fresh run: `frozen_count >= 1`, `slow_magnitude == 0.6`, `ice_slow_fx >= 1` on live trap hit; `.gen/harness/traps_frostbite_fangs_progression/result.json` status=pass, exit 0); pixel-visible confirmation awaits the windowed manual-tester screenshot (`manual_testing: required`; checkpoints `frostbite_fangs_chilled_hit` / `frostbite_fangs_aftermath` wired in scenario, skipped under --headless).

## ❌ Impossible
