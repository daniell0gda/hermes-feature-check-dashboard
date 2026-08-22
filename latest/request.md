# Request: Systemic: Overcharge Capacitors

- **Issue:** #34 — https://github.com/daniell0gda/poke-defense-godot/issues/34
- **Project:** poke-defense-godot
- **Git workspace:** /workspace/git-workspaces/poke-defense-godot/issue-overcharge-capacitors
- **Branch:** issue/overcharge-capacitors
- **Requested by:** Daniel ("find new issue" — claim top eligible ready issue, implement via team-work)
- **Claimed:** 2026-08-22T12:45:00Z

## Issue summary

New Common progression perk `overcharge_capacitors`: for every 3 towers of the same type owned
simultaneously, all towers of that type gain a damage bonus (see issue body for exact tiers).
Purely numeric, composes with existing per-tower Unique trees; reinforces specialization.

## Acceptance criteria

Per issue "Done when": new Common `overcharge_capacitors` perk registered in the progression/perk
system, applied to all towers of a type per every 3 same-type towers owned, with tests proving the
per-type stacking math (including boundary cases: fewer than 3, exactly 3, 6+ towers) through the
project's standard harness scenarios. Follow /opt/data/coding_rules.md and project conventions.
