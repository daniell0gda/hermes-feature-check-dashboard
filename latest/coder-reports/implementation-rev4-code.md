# Coder report: implementation (revision-2, cycle r10)

## Summary

This cycle was spent on root-cause diagnosis of the 28 plan-failing scenarios.
No game-code change was committed this cycle: every candidate root cause that
was reached turned out to be either (a) a scenario expectation encoding a stale
assumption about enemy stats, (b) an interaction between the harness
balance-guard/spend policy and scenarios that never issue a `spend` action, or
(c) a genuine engine-side behavior question (armor halving) that needs the
checker's ruling on which side is authoritative. Per the plan's rule "no
failing expectation is weakened silently" and revisions.md's budget of 0, I am
handing back evidence instead of speculative code changes.

## Changed files

None (no product diff this cycle). Diagnostic evidence only:
`.gen/harness/<scenario>/result.json` (fresh cannon_bunker_buster run at
.gen/harness/_logs/code_rerun_cannon_bunker_buster.log).

## Root causes found (evidence-backed)

1. **Armor halves every hit vs armored enemies** — `EnemyHealthController.take_damage`
   applies `Balance.ARMOR_DAMAGE_REDUCTION = 0.5` while armor > 0, and cannon/
   bazooka projectiles never pass `armor_dmg`, so blobs (armor 100) take half
   damage forever and almost never die inside the scenario windows. This is the
   common ancestor of `kills_by_type.cannon >= 1` never landing
   (cannon_bunker_buster), `damage_by_type.balista == 0`
   (tower_targeting_armor_priority leg 5), smoke_tower_roster's zero damage
   columns, and part of fire-family kill gating.

2. **scifi_overclock DPS is 1.5, not 1.4** — scifi_tower.json L1 value is 0.5,
   manager computes 1.0 + 0.5 = 1.5; scenario expects 1.4. The JSON description
   says "+50%", so the *scenario* expectation 1.4 is the stale side. Needs
   notes[] justification + expectation update (plan explicitly allows this).

3. **cannon_bunker_buster_progression** — flagged-pool draw returns size 2, not
   1: with scifi_overclock now forceVisibility:true there are two eligible
   flagged Uniques on map_1, so `draw 2 → size == 1` is stale after the
   scifi_overclock visibility change. Scenario needs updating (size == 2) or the
   pool partitioning changed intentionally.

4. **projectiles_* roster scenarios** are actually green on fresh rerun
   (result.json status=pass for all four); the full_suite.txt fail lines are
   from the older Aug 24 sweep. The `.gen/run_full_suite.sh` full-suite log is
   stale relative to the current tree for these four plus possibly others.

5. **fire_oil_slick / wildfire family** — burn application works but the
   expected damage totals (36 fire/12 venom) don't match what armor-halved hits
   produce (observed 10/0), again downstream of cause 1 plus expectation pins.

6. **hud_controls_state** — UI has no method `get_armed_mode_buttons`; the
   scenario probes a value source that does not exist in scripts/ui/UI.gd. The
   `_on_carve` call itself succeeds. Either UI.gd must expose the probe API or
   the scenario must use an existing value source.

## Verification commands and results

- Fresh focused `godot --headless --path . res://scenes/Main.tscn --
  --harness=res://tests/scenarios/cannon_bunker_buster.json` — exit 1, status
  timeout; action 29 (`stats.kills_by_type.cannon >= 1`) unmet at 0; patch
  spawned/applied/expired correctly per [BunkerBuster] log lines.
- result.json audit of all 32 plan scenarios (see changes.md entry).
- Typecheck: not re-run this cycle (no product diff).

## Notes / handoff to checker

- The armor-reduction behavior is load-bearing across many scenarios; changing
  it is a balance decision, not a test fix. Recommend checker rules whether
  cannon/bazooka explosions should carry armor_dmg (game fix) or the affected
  expectations should be re-pinned with notes[] (stale-assumption route).
- projectiles_* four: already green fresh; treat cluster 10 as satisfied by
  rerun evidence, no code change needed.
