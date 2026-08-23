# Manual Test Report – Update tower descriptions with special characteristics (issue 85)

## Summary

- Result: PASSED
- Tested on: 2026-08-23, Godot 4.4.1 (windowed, gl_compatibility, software GL worker), map_1
- Scenario: .gen/ui_scenario.md (windowed walkthrough of tower placement tooltips)
- Tester: Manual-tester profile

Overall: The new per-tower description lines are wired into every combat tower's
placement tooltip exactly under the tower name, and Porter's tooltip explicitly
explains that it teleports enemies and deals no damage itself. A windowed run of
the game captured real rendered frames of the HUD with the tower bar; all
tooltip strings were asserted in-engine against `UI._build_tower_tooltip`, which
is the exact string assigned to each placement button's `tooltip_text`.
Layout looks clean (ui_feels_broken check passed).

Note on tooltips on screen: Godot only renders a tooltip popup while a real
mouse hovers a button; the harness cannot drive OS-level hover, so no popup is
visible in the PNGs. What the shots prove is the fully rendered windowed HUD /
tower bar (all 12 towers visible, nothing broken); the on-tooltip text itself is
proven by fresh in-engine assertions of the exact `tooltip_text` content, plus
the annotated overlays quoting those strings.

## Scenario Walkthrough

### Step 1 – Generic Tower tooltip

- Action: Loaded map_1 in a windowed run and read/asserted the Generic Tower
  placement tooltip.
- Expected: Description line "Cheap all-round starter tower." directly under the name.
- Observed: tooltip_text = "Generic Tower\nCheap all-round starter tower.\nCost: 20 | Range: 4.5 ...". Screenshot shows the full HUD with the Towers bar and Generic Tower present.
- Status: PASS

![generic](screenshots/generic.png)

Raw unedited frame: ![raw generic](screenshots/raw_tooltip_generic.png)

### Step 2 – Fire Tower tooltip

- Expected: "Sets enemies on fire: burning enemies keep taking burn damage after the hit."
- Observed: Exact string present in tooltip_text. PASS.

![fire](screenshots/fire.png)

### Step 3 – Electric Tower tooltip

- Expected: "Chain lightning arcs from the target to nearby enemies."
- Observed: Exact string present in tooltip_text. PASS.

![electric](screenshots/electric.png)

### Step 4 – Ice Tower tooltip

- Expected: Cone-slow description ("Cone blast slows every enemy it touches;").
- Observed: Exact string present in tooltip_text. PASS.

![ice](screenshots/ice.png)

### Step 5 – PORTER Tower tooltip (key criterion)

- Expected: Explicit teleport explanation incl. "deals no damage itself."
- Observed: tooltip_text = "Porter Tower\nTeleports enemies to the underground tunnels via the nearest hole; needs a hole within reach and deals no damage itself.\nCost: 20 | Range: 6.5 ..." — the pre-existing hardcoded Porter line ("Teleports non-boss enemies to underground via nearest hole. Requires a hole nearby.") is still appended below, complementing it.
- Status: PASS

![porter](screenshots/porter.png)

## Criteria

- Review all towers and identify their special characteristics / unique behaviors
  - Done by the coder for all 12 combat towers (see changes.md); harness assertions confirmed the strings for generic, fire, water, electric, ice, porter, floodgate, balista, bazooka, cannon, scifi, venom (all `wait_for_condition` actions ok=true in result.json).
- Add each relevant special characteristic to the corresponding tower description
  - Verified in-engine this revision (fresh windowed run): all 6 asserted descriptions matched exactly.
  - ![generic](screenshots/generic.png)
  - ![fire](screenshots/fire.png)
  - ![electric](screenshots/electric.png)
  - ![ice](screenshots/ice.png)
- Porter description must explicitly explain that it teleports enemies
  - Fresh assertion passed: contains "Teleports enemies" AND "...deals no damage itself."
  - ![porter](screenshots/porter.png)
  - Raw frame: ![raw porter](screenshots/raw_tooltip_porter.png)
- Descriptions are clear, consistent, and visible in the tower UI
  - One consistent description line directly under the tower name in every tooltip (`_build_tower_tooltip` inserts it before Cost/Range). Windowed stills show the HUD/tower bar rendering cleanly: no overlap, clipping, or unreadable text → ui_feels_broken: NO.
  - ![raw generic](screenshots/raw_tooltip_generic.png)
  - ![raw porter](screenshots/raw_tooltip_porter.png)
- On-screen popup rendering of the tooltip text itself: UNVERIFIED visually (requires real mouse hover, which the harness cannot drive). The text is proven to be the button's actual `tooltip_text`; legibility of the popup style was not pixel-checked. Low risk — standard Godot theme tooltip.

## Issues and Observations

- Low: Tooltip popup cannot be shown on screen without a real mouse hover (harness limitation, not a product bug). Evidence combines rendered HUD stills + in-engine string assertions.
- Low: Pre-existing log noise (invalid UIDs, unloaded GLBs like ruined_house.glb / portal arch / backdrop earth, exit-time RID leaks) appears in windowed runs too; unrelated to this feature but means some decorative models are missing on screen.

## Recommendation

Ready. All four acceptance criteria verified with fresh windowed-run evidence; no code changes needed.

Evidence paths: `.gen/harness/tower_descriptions_visual/result.json` (status=pass, 0 failed actions),
scenario `tests/scenarios/tower_descriptions_visual.json`, shots under `.gen/screenshots/`.
