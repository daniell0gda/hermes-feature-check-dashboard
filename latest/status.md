## ✅ Done
- Update tests/scenarios/progression_pick.json so it places a venom tower, or otherwise owns a compatible tower, before opening the chest and selecting venom_miasma_bloom; the scenario must pass. — verified fresh this revision: run_project_cmd focused harness exit 0, "[Harness] status=pass exit=0", result.json status "pass" with all 4 expectations PASS (venom_miasma_bloom==1, get_venom_miasma_config.enabled==true, tree.paused==false, current_layer=="underground"). Log shows real chest draw force_mode=false, flagged=0, normal=2, chosen=2 (pool exhausted to the two venom perks), modal answered card 2 = venom_miasma_bloom. Timeline: _set_tower_availability(8) at action 117, place_tower kind:"venom" at 119 before chest open.
- Preserve and verify chest_duplication is granted before carving, including the cave_chest_duplicate RNG site behavior. — verified: apply_progression(global.json, chest_duplication) at timeline actions 1–2, first carve_rectangle at action 121; carving proceeds to completion and the chest reward fires deterministically under seed 20260726.
- Update the “Reaching a chest organically” section of .claude/skills/game-test/REFERENCE.md to match the fixed recipe and tower requirement. — verified: section rewritten (~line 350) describing the passing scenario, tower ownership/compatibility filter, pool-exhaustion recipe, and correct forceVisibility:false claim matching scripts/progression/venom_tower.json.
- Use native Linux Godot/project-runner verification, fresh focused scenario evidence, and inspect raw diagnostics separately from harness status. — done by checker: preflight godot --version (exit 0, Godot 4.4.1.stable) then focused harness via run_project_cmd; raw log inspected separately from harness status. Pre-existing noise noted (missing wood_panel.png texture warnings, ProgressionModal/CaveDangerConfirmDialog exclusive-child window conflict) — neither blocks harness answering or the pass.
- Do not close, merge, or push the issue. Commit is not requested. — OK: HEAD unchanged (996f282); working tree has only the two modified files.

## ⬜ Pending
(none)

## ❌ Impossible
(none)
