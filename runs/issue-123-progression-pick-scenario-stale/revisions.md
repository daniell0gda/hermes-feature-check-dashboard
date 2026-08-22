classification: fixable
next_role: code
reason: fresh focused run still fails — chest draw does not offer venom_miasma_bloom (forceVisibility false, 2-of-37 weighted draw) and REFERENCE.md section not updated
revision: 2
budget_remaining: 0
Make the scenario actually receive venom_miasma_bloom in the modal (fix forceVisibility/eligibility or pin the offer deterministically for an owned-venom run), resolve the CaveDangerConfirmDialog exclusive-child conflict if it blocks answering, update the "Reaching a chest organically" section of .claude/skills/game-test/REFERENCE.md (remove "currently red", correct the forceVisibility claim), then rerun the focused progression_pick harness to green via run_project_cmd.
