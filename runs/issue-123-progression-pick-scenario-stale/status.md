## ✅ Done
(none — primary criterion failed fresh verification again)

## ⬜ Pending
- Update tests/scenarios/progression_pick.json so it places a venom tower, or otherwise owns a compatible tower, before opening the chest and selecting venom_miasma_bloom; the scenario must pass. — fresh focused run via run_project_cmd: runner exit 1, harness status "timeout" (.gen/harness/progression_pick/result.json, rerun 2026-08-22). Venom availability set (_set_tower_availability(8) ok), wait_for_condition on draw_choices_for_chest(100) ok, place_tower venom ok — but the real chest draw is still `force_mode=false, flagged=0, normal=37, chosen=2` and venom_miasma_bloom was not offered: action index 71 blocked with "progression modal could not be answered with policy {\"mode\":\"upgrade\",\"upgrade\":\"venom_miasma_bloom\"}". Expectations FAILED: venom_miasma_bloom == 0 (want 1), get_venom_miasma_config.enabled == false (want true), tree.paused == true (want false); only current_layer passed. Engine also logged ProgressionModal exclusive-child conflict with CaveDangerConfirmDialog (window.cpp:992).
- Update the "Reaching a chest organically" section of .claude/skills/game-test/REFERENCE.md to match the fixed recipe and tower requirement. — section still says the scenario is "currently red" and still claims venom_miasma_bloom has forceVisibility: true, contradicting scripts/progression/venom_tower.json (forceVisibility: false at lines 14/28/41).

## ❌ Impossible
(none)
