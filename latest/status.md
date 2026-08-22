## ✅ Done
(none — primary criterion failed fresh verification)

## ⬜ Pending
- Update tests/scenarios/progression_pick.json so it places a venom tower, or otherwise owns a compatible tower, before opening the chest and selecting venom_miasma_bloom; the scenario must pass. — fresh focused run via run_project_cmd: harness status "timeout", exit 1 (.gen/harness/progression_pick/result.json, finished 2026-08-22T08:40:39); venom tower placed ok, but the modal's 2-of-37 weighted draw did not offer venom_miasma_bloom (log: "progression modal could not be answered with policy {\"mode\":\"upgrade\",\"upgrade\":\"venom_miasma_bloom\"}"); expectations venom_miasma_bloom==0, enabled==false, tree.paused==true all failed; engine also logged a ProgressionModal exclusive-child conflict with CaveDangerConfirmDialog.
- Update the "Reaching a chest organically" section of .claude/skills/game-test/REFERENCE.md to match the fixed recipe and tower requirement. — section still says the scenario is "currently red" and still claims venom_miasma_bloom has forceVisibility: true, contradicting scripts/progression/venom_tower.json (forceVisibility: false).

## ❌ Impossible
(none)
