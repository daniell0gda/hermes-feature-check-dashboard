# Issue #123 — progression_pick scenario is red since chest rewards require a compatible tower

Implement the issue requirements from https://github.com/daniell0gda/poke-defense-godot/issues/123.

Project: poke-defense-godot
Workspace: poke-defense-godot/issue-progression-pick-scenario-stale

Acceptance criteria:
- Update tests/scenarios/progression_pick.json so it places a venom tower, or otherwise owns a compatible tower, before opening the chest and selecting venom_miasma_bloom; the scenario must pass.
- Preserve and verify chest_duplication is granted before carving, including the cave_chest_duplicate RNG site behavior.
- Update the “Reaching a chest organically” section of .claude/skills/game-test/REFERENCE.md to match the fixed recipe and tower requirement.
- Use native Linux Godot/project-runner verification, fresh focused scenario evidence, and inspect raw diagnostics separately from harness status.
- Do not close, merge, or push the issue. Commit is not requested.

Visible UI is not the primary change, but run any required scenario evidence according to the team-work manual-testing gate.
