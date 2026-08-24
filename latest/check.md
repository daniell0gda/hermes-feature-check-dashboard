# Check report — warlords-doctrine r2 (iteration 4)

classification: pass

## Verdict

All acceptance criteria verified with fresh runner evidence. The r2 readability
leg (windowed, human-readable armor-bar PNGs) is satisfied by the fresh windowed
run and the 6 PNGs in `.gen/screenshots/`, which I inspected visually.

## Verification commands (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-warlords-doctrine)

- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0, clean import, no script errors.
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json` — exit 0, `[Harness] status=pass exit=0`. Log evidence: `[WARLORDS-DOCTRINE] applied L1/L2/L3 total_multiplier=1.05/1.09/1.14`, `spawn_bonus level=1 granted_armor=1.76 on Mushnub`, `spawn_bonus level=3 granted_armor=243.75 on Orc Enemy_boss` (60 innate + 15% × 1625), additive with tower_dmg (1.10 + 0.05 = 1.05 total shown after both applied), reset leg ran before map_7.
- Regression: `enemy_armor_ballista.json` — exit 0, status=pass. `enemy_armor_trap.json` — exit 0, status=pass.
- Windowed evidence: `godot --path . --rendering-method gl_compatibility --rendering-driver opengl3 --audio-driver Dummy res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_bar_visual.json` — exit 0, status=pass, 6/6 screenshots captured at 1920×1080, copied to `.gen/screenshots/` (timestamps match this run).

## Acceptance criteria evidence

1. Debug Panel hidden before any screenshot — `hide_debug_panel` action is the
   first timeline entry; result.json index 0: `{panel_present: true, hidden: true}`,
   ok=true. Visual inspection of all 4 PNGs I opened: no gray debug overlay.
   PASS.
2. Camera aims at live enemy position — `frame_enemy` action reads the live
   enemy's health-bar anchor (`anchor [-7.946, 0.85, -7.797]` for Mushnub, moving
   to `[-7.892, 0.85, -7.594]` between shots as the enemy walks), sets
   `camera_target`, then `_update_camera_for_layer("surface")`; zoom 4.0 for the
   shot only. Not a hardcoded coordinate. PASS.
3. Both rows individually readable — armor_full.png clearly shows an orange
   armor row above the green HP row over the (placeholder) Mushnub; both rows
   are tens of pixels tall at zoom 4.0. GLB missing in worktree (known, logged
   `Failed loading resource ... Mushnub.glb`); bars readable without faking
   armor — Mushnub config armor is 0 and the 1.76 comes from doctrine L1
   (8% × 22 HP). PASS.
4. Armor fill shrinks after one scripted hit — armor_partial.png shows the
   orange row shrunk to a small sliver (~43% → rendered ~5-10px) vs full width in
   armor_full.png, HP row still nearly full; harness asserted armor == 0.76.
   PASS.
5. Armor row hidden at 0 — armor_depleted.png shows only the green HP row, no
   orange row; harness asserted armor == 0. PASS.
6. Fresh windowed run passes with PNGs in `.gen/screenshots/` — result.json
   status=pass, 6 PNGs written this run (2026-08-24 08:47), replacing r1 crops.
   PASS.
7. No misplaced/clipped HUD, nothing covering bars — inspected PNGs: top bar,
   tower dock, and side buttons all clear of the enemy bars. PASS.
8. Headless warlords_doctrine still passes — see focused test above. PASS.
9. enemy_armor_ballista and enemy_armor_trap still pass — see regression above.
   PASS.

Previously-Done r1 criteria (perk catalog values, multiplier application,
additive stacking, reset, granted/innate armor stacking, armor-soak behavior,
[WARLORDS-DOCTRINE] log lines) are re-evidenced by this run's fresh headless
warlords_doctrine pass and its log lines; source files unchanged since r1
(`autoload/ProgressionManager.gd`, `scripts/game/actors/Enemy.gd`,
`scripts/progression/global.json`, `tests/scenarios/warlords_doctrine.json`).

## Changed-file quality

- `scripts/testing/HarnessActions.gd` (+47 lines): two new harness actions,
  typed GDScript, guard clauses, doc comments, no casts, single-purpose
  functions — clean per coding rules.
- `tests/scenarios/enemy_armor_bar_visual.json`: two-leg scenario rewrite,
  well-commented; no overlap with existing scenarios (doctrine leg is new; the
  innate leg reuses the pre-existing visual scenario's purpose with better
  framing).
- `scripts/progression/global.json`: pre-existing advisory whitespace noise on
  unrelated entries remains (open quality-notes entry, unchanged — not
  re-reported).
- No scope creep: diff touches only the feature files plus the harness actions
  the plan explicitly allowed.

## Blockers

None.

## Unverified items

None.
