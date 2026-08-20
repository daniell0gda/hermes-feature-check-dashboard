# Request: perk-capacitor-bank (#27)

https://github.com/daniell0gda/poke-defense-godot/issues/27

## Goal

Sci-Fi Tower currently waits on yaw alignment after every retarget (`ScifiTower._manage_beam` / `_is_yaw_aligned_with_target()`). Add Common perk `scifi_capacitor_bank` L1–3 that cuts that re-engage delay.

## Done when

- New Common `scifi_capacitor_bank` (L1-3): reduces the effective yaw-tolerance / re-engage delay after switching targets by a meaningful, tunable margin.
- No new visual — existing beam rendering, just resumes sooner.

## Notes

- Runner: project `godot-td`, workspace `poke-defense-godot/issue-perk-capacitor-bank`.
- Do not commit, push, merge, or close the issue.
