## ✅ Done
- Update tests/scenarios/progression_pick.json so it places a venom tower, or otherwise owns a compatible tower, before opening the chest and selecting venom_miasma_bloom; the scenario must pass. — fresh focused run via run_project_cmd: runner exit 0, harness status "pass" (.gen/harness/progression_pick/result.json, rerun 2026-08-22 after fix). Chest draw now force_mode=false, flagged=0, normal=2, chosen=2 (pool pinned to the two venom perks); modal answered with card 2 = venom_miasma_bloom. All 4 expectations PASS: venom_miasma_bloom == 1, get_venom_miasma_config.enabled == true, tree.paused == false, current_layer == underground.
- Update the "Reaching a chest organically" section of .claude/skills/game-test/REFERENCE.md to match the fixed recipe and tower requirement. — section rewritten: scenario described as passing, forceVisibility claim corrected to false for all venom perks, full deterministic-offer recipe documented.

## ⬜ Pending
(none)

## ❌ Impossible
(none)
