# Issue #15 — Global Curse: Fragile Optics

Project: godot-td (`daniell0gda/poke-defense-godot`)
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/15
Workspace: `/workspace/git-workspaces/poke-defense-godot/issue-fragile-optics`
Branch: `issue/fragile-optics`
Runner workspace id: `poke-defense-godot/issue-fragile-optics`

## Goal
Implement Common curse perk `curse_fragile_optics` (L1–L2): +10%/+20% range, plus a miss-chance roll against fast-moving enemies that voids the hit (no damage, no on-hit status). Insert a miss-roll before `take_damage` for towers with this perk. A voided shot must show a visible lightweight Miss VFX via EffectsManager (not a silent no-op).

## Acceptance criteria
- New Common perk `curse_fragile_optics` L1–L2 exists in the progression system.
- Range bonus +10%/+20% while the perk is active.
- Miss chance only vs enemies faster than a documented speed threshold relative to map baseline; miss voids damage and on-hit status.
- Miss-roll happens before `take_damage`.
- Visible Miss VFX (fading label or spark-deflection) on voided hits, following EffectsManager dispatch.
- Focused Godot runner evidence (editor gate + harness). Visible UI/VFX requires windowed manual-tester screenshots (never headless-only).
- Do not push, merge, commit, or close the issue.

## Constraints
- Use only `.gen/` flat artifacts.
- Follow `/opt/data/coding_rules.md`.
- No legacy feature-check CLI.
- Revision budget: 2.
- Project commands only via runner `godot-td` + workspace `poke-defense-godot/issue-fragile-optics`.
