# Issue #32 verification status

## Classification

**blocked**

## ✅ Verified

- Production change is limited to the four intended Enemy/health/movement/suction files.
- New focused scenario is the only scenario source added; existing scenario JSON files remain unchanged.
- `Enemy.hp` remains `int`.
- Production contains typed attribution fields, `int(round(...))`, clear-before-iterate idempotence, and calls on death, surface arrival, and suction retirement.
- Godot version and editor/import gate passed.
- Explicit-scene `ice_focus_cone_cadence`, `smoke_tower_roster`, and `projectiles_10x_beam_cone` passed fresh.

## ⬜ Pending

- Direct proof of exact residual attribution and tower instance attribution at retirement.
- Direct `.4` versus `.6` rounding proof.
- Direct repeated-flush/exact-once proof.
- Direct proof that death flush does not subtract HP or double-count kills.
- Direct proof of exactly-once egg damage and cave/tube consumption across all retirement paths.
- Confirmed pipe-consumption run: focused explicit-scene result timed out at the underground-entry wait.
- A clean focused scenario pass: current result is `status=timeout` after aggregate actions.

## ❌ Impossible with current evidence schema

- The declarative scenario cannot inject exact fractional values with selected stored type/instance, expose the private pending record, label retirement-path events, or checkpoint each map arm before the next `load_map` resets StatsManager. A harness/test seam is required before the pending criteria can be proven.

## Notes

Fresh artifacts and command details are in `.gen/check.md` and `.gen/report.md`. Dashboard publication is not confirmed: the declared GitHub Pages URL currently returns 404.
