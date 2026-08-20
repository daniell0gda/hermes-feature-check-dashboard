# Request: #29 Sci-Fi Overclock perk

Project: poke-defense-godot (runner `godot-td`)
Workspace: poke-defense-godot/issue-scifi-overclock
Issue: https://github.com/daniell0gda/poke-defense-godot/issues/29
Slug: scifi-overclock

## Problem

Sci-Fi beam is always-on with no risk/reward Unique.

## Done when

- New Unique `scifi_overclock`: +40% beam DPS, but 6s of continuous fire forces a 2s cooldown with no beam.
- Visual tell during the 2s lockout: dim/desaturate the beam or tint the tower via `HighlightShaderUtils.gd` using the existing beam-rendering hook, so it reads as recharging not broken.
- Windowed screenshots required (visible perk/VFX). Manual-tester must be windowed, not headless-only.

Do not commit, push, merge, or close the issue.
