# Manual Test Report – Porter Boss Runner

## Summary

- Result: FAILED
- Tested on: 2026-08-19, Linux CLI environment (no Godot, no display)
- Scenario: /workspace/git-workspaces/poke-defense-godot/issue-porter-boss-runner/.gen/ui_scenario.md
- Tester: Manual-tester profile

Overall: Attempted to execute the UI scenario for porter_boss_runner perk visual behavior (boss charge, teleport dissolve VFX, underground reroute while boss remains in play). The harness result.json exists from prior headless run showing state snapshots for perk off/on cases. However, no windowed Godot execution was possible, so no PNG screenshots could be captured or embedded. Per rules, report with no PNG is FAILED. Visual criteria remain unverified.

## Scenario Walkthrough

### Step 1 – Perk not owned: Porter ignores boss

- Action: Load map with Porter placed near hole, trigger wave with Ninja_boss on surface (perk not applied).
- Expected: Porter does not lock/charge boss; boss stays on surface path.
- Observed: Harness snapshot "perk_off_no_boss_target" shows enemies.Ninja_boss.count >=1 and no underground/dissolving at that point. Logic verified in harness.
- Status: PASS (logic only, no visual)

### Step 2 – Perk owned: Porter locks and charges boss

- Action: Apply porter_boss_runner perk, reload map, place Porter, trigger boss wave.
- Expected: Porter locks boss and completes charge.
- Observed: Harness shows perk application, then "perk_on_boss_reroute" snapshot with boss still counted.
- Status: PASS (logic only, no visual)

### Step 3 – Charge completes with VFX and reroute

- Action: Observe full charge completion.
- Expected: Teleport dissolve VFX plays on boss, boss takes underground detour via UGSystem, boss remains in play (not consumed).
- Observed: Harness waits for dissolving >0 then underground >=1, with Ninja_boss still present. No rendered frame available.
- Status: FAIL (visual evidence missing)

## Issues and Observations

- No Godot binary or display available in CLI container; cannot launch windowed `godot --path . res://scenes/Main.tscn` to capture screenshots.
- Harness provides state assertions and log evidence for logic, but ui_scenario explicitly requires still PNG proving VFX + reroute state.
- Perk definition, apply/reset, boss ignore/lock/reroute logic covered by existing harness result (status pass in headless).
- Visual-only gap: teleport dissolve effect visibility and boss on underground path cannot be screenshot-verified here.

## Recommendation

Not ready for release on visual criteria. Headless logic passes; create visual follow-up or re-run in desktop environment with windowed Godot to capture required PNGs of the charge-complete state (VFX + underground boss). Report stored at .gen/manual-report.md. No dashboard events published.

## Criteria

- Unique perk `porter_boss_runner` is type Unique, is eligible before it is taken, and appears in a chest draw before it is taken.
  - Unverified (no visual; harness covers logic)
- Applying `porter_boss_runner` once owns it at level 1, makes it ineligible, and removes it from a chest draw; a further apply leaves the level at 1; `reset_for_new_game` returns it to unowned.
  - Unverified visually
- Without `porter_boss_runner` owned, a Porter in range of a live surface boss does not lock or charge that boss, and the boss stays on the surface.
  - Unverified visually (harness snapshot exists)
- With `porter_boss_runner` owned, a Porter in range of a live surface boss locks that boss and completes a full charge.
  - Unverified visually
- After a full Porter charge on a boss with `porter_boss_runner` owned, the boss remains in play and is rerouted onto an extra underground detour produced by `UGSystem.compute_underground_route` rather than being consumed or removed.
  - Unverified visually (requires PNG of boss on underground path post-charge)
- That boss reroute plays Porter's existing teleport dissolve VFX; dissolving is observable during charge completion.
  - Unverified visually (requires PNG showing VFX on boss)
- Without `porter_boss_runner` owned, a Porter still teleports a non-boss surface enemy onto a valid underground route.
  - Unverified visually
- Debug-build [PORTER_BOSS_RUNNER] log line per perk apply
  - Unverified visually
- Debug-build [PORTER_BOSS_RUNNER] log line per boss lock event
  - Unverified visually
- Debug-build [PORTER_BOSS_RUNNER] log line per boss reroute event
  - Unverified visually
- Focused harness scenario `porter_boss_runner` covers perk off (no boss target) and perk on (charge then reroute plus VFX/path evidence) and finishes with `status: pass`.
  - Logic PASS (result.json exists); visual FAIL (no PNGs)

No PNG files under .gen/screenshots/ — report FAILED per mandatory rules.