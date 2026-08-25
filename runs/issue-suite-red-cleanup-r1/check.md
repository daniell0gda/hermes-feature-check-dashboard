# Check report — fix-preexisting-harness-reds (issue-130, iteration check-1)

classification: fixable

## Verdict

The 28-scenario harness-red cleanup was NOT implemented. The latest coder cycle
(revision-2 / r10) made **no product diff** — it delivered a root-cause
diagnosis only and handed back to the checker for a ruling (see
`.gen/coder-reports/implementation-rev4-code.md` and the r10 entry in
`.gen/changes.md`). All 25+ plan scenarios still fail on fresh evidence; the
plan's `status.md` currently contains criteria from a *different*, older issue
(issue-130 carve-camera pan) — rewritten below per checker contract.

Runner gate itself is healthy: `run_project_cmd` (project `godot-td`, workspace
`poke-defense-godot/issue-130`) works. This is a `fixable` implementation gap,
not a blocker.

## Verification commands (fresh, this run)

| Command | Exit | Result |
|---|---|---|
| `["godot","--version"]` via run_project_cmd | 0 | 4.4.1.stable.official.49a5bc7b6 — runner reachable |
| Typecheck `["godot","--headless","--path",".","--editor","--quit-after","3"]` via run_project_cmd | 0 | clean import/parse |
| Focused harness `carve_pan_no_flip.json` via run_project_cmd | 0 | status=pass (sanity probe: runner + harness pipeline healthy) |
| Attempted focused `scifi_overclock_progression.json` via run_project_cmd | 1 | status=timeout: observed 1.5 vs expected 1.4 — still red |
| Full suite `.gen/run_full_suite.sh` | n/a | not re-run this iteration (`bash` off runner allowlist); authoritative fresh per-scenario evidence audited from `.gen/harness/<name>/result.json` (Aug 24–25 runs) plus the Aug-24 full-suite sweep |

## Criterion evidence — all 12 clusters FAIL

Latest `.gen/harness/<scenario>/result.json` statuses:

- Cluster 1 cannon/balista: `cannon_bunker_buster` timeout (kills_by_type.cannon
  stays 0), `cannon_bunker_buster_progression` timeout (flagged-pool size 2 vs
  expected 1), `cannon_heavier_shells_blast` timeout (damage_by_type.cannon == 0),
  `curse_overheat_cycle` timeout (damage_by_type.balista == 0). Fresh rerun log:
  `.gen/harness/_logs/code_rerun_cannon_bunker_buster.log`.
- Cluster 2 burn/material: `fire_oil_slick` timeout (Mushnub_boss.materials_clean
  never false), `fire_oil_slick_progression` timeout (oil_slick perk absent from
  draw pool), `ice_burn_material_restore_stuck` hard fail (Mushnub_boss.burning
  True after expiry).
- Cluster 3 fire spread: `fire_flashover_spread` timeout (fire dmg 734 < 1000),
  all three `fire_wildfire_spread_*` timeout (enemies.Mushnub.count never 3).
- Cluster 4 elemental progression: `scifi_overclock` and
  `scifi_overclock_progression` timeout (1.5 vs 1.4 — scenario side stale per
  scifi_tower.json +50% description), `scifi_capacitor_bank` timeout,
  `scifi_piercing_beam_progression` timeout (perk missing from pool),
  `water_deep_soak_progression` timeout, `floodgate_cryobrine_progression`
  timeout. Freshly confirmed this run via run_project_cmd.
- Cluster 5 HUD call path: `hud_controls_state` timeout —
  `ui_call.get_armed_mode_buttons == "carve"` unmet; grep confirms NO
  `get_armed_mode_buttons` method exists anywhere in `scripts/ui/UI.gd`
  (checked both working tree and branch `issue-130`). The value source must be
  implemented or the scenario repointed.
- Cluster 6 timed hazards/victory: `issue_35_timed_hazards_map_change` timeout
  (scorch_patches 0), `issue_86_victory_underground_clear` timeout
  (victory_pending_full_clear false).
- Cluster 7 boss/targeting: `porter_boss_runner` timeout (log line
  `[PORTER_BOSS_RUNNER] miss` never emitted despite miss=0.65 applied),
  `static_breach_isolation` timeout (enemies.Mushnub.count never 3),
  `tower_targeting_armor_priority` timeout (balista damage 0).
- Cluster 8 long carve: `cave_discovery_long_carve` fail — carved_tiles 961 <
  1000; no notes[] justification added.
- Cluster 9 progression economy: `progression_chest_pool` fail (pool contains
  frozen_fracture/curse_overheat instead of curse_blood_money;
  tower_dmg.level 0 != 1), `progression_pick` fail (venom_miasma_bloom 0 != 1,
  enabled false). Stale pins after newer perks joined the pools (cf. commit
  ed2c29f pattern on master).
- Cluster 10 projectiles: `projectiles_10x_ballistic`,
  `projectiles_10x_beam_cone`, `projectiles_2x_roster`, `projectiles_5x_roster`
  — all PASS on fresh result.json (the full_suite.txt fail lines are stale).
  These four criteria are met by fresh rerun evidence, no code change needed.
- Cluster 11 roster/diversion: `smoke_tower_roster` fail (wave 2 < 3;
  balista/bazooka/cannon damage all 0), `underground_diversion_baseline` fail
  (egg_hp 15 != 25).
- Cluster 12 regression guard: not satisfied — the previously-red set remains
  red; several expectation changes would need notes[] justification that does
  not exist yet.

## Root causes established by the coder (accepted as diagnosis)

1. `EnemyHealthController.take_damage` applies `Balance.ARMOR_DAMAGE_REDUCTION =
   0.5` (Balance.gd:216) while armor > 0 even when attackers pass no
   armor_dmg — common ancestor of cannon/balista/smoke_tower_roster zeros.
   Needs a game-side decision: pass armor_dmg from cannon/bazooka explosions or
   re-pin expectations with notes[].
2. scifi_overclock DPS is 1.5 by design (+50% per its JSON description); the
   scenario's 1.4 pin is stale — needs scenario update WITH notes[].
3. `cannon_bunker_buster_progression`: flagged-pool draw now returns 2 eligible
   Uniques (scifi_overclock forceVisibility) — scenario pin of 1 is stale.
4. Several progression pins (chest_pool/pick/deep_soak/cryobrine/oil_slick/
   piercing_beam) are stale after new perks entered the pools.
5. `hud_controls_state`: probes a nonexistent `UI.get_armed_mode_buttons()`.

None of these fixes have been applied to game code or scenario JSON.

## Changed-file quality

No product diff exists for this task (`git diff HEAD` shows only the unrelated
pre-existing issue-130 camera work already committed/known). Nothing to review
for quality; no quality-notes entries appended (no new cross-cutting violations
introduced).

## Blockers

None infra-wise. The work simply has not been done; revisions.md budget was 0,
so the implementor correctly stopped rather than weaken expectations silently.

## Required next steps (for code role)

Apply the five root-cause fixes above: armor-damage path decision + fix,
stale-pinning scenario updates each with notes[] justification, and either
implement `UI.get_armed_mode_buttons()` or repoint the hud_controls_state
conditions to an existing UI value source. Then rerun the affected families and
a fresh full suite.
