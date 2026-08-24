# Check report: req-85-tooltip-polish-r2 (shop preview card)

classification: fixable

## Verdict

Implementation is real and focused; editor gate and focused harness pass fresh through the
approved runner. One criterion is demoted for missing evidence, and the full-suite shard
command cannot complete inside the runner's 420 s cap (pre-existing infra limitation,
verified per-scenario instead). The windowed manual screenshot pass has not been performed
yet — no `.gen/manual-report.md` exists.

## Verification commands (all via run_project_cmd project godot-td, workspace
poke-defense-godot/issue-update-tower-descriptions-with-special-c)

| Command | Result |
|---|---|
| `["godot","--version"]` | exit 0, Godot 4.4.1.stable.official.49a5bc7b6 — runner healthy |
| `["godot","--headless","--path",".","--editor","--quit-after","300"]` | exit 0. Only pre-existing invalid-UID warnings in `themes/hud/HudTheme.tres` / `scenes/UI.tscn` (untouched files). No parse/script/resource diagnostics on changed files. |
| `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/tower_shop_preview_card.json"]` | exit 0, `[Harness] status=pass exit=0`; result `.gen/harness/tower_shop_preview_card/result.json` status=pass, 21/21 actions ok, all 4 expectations pass. Log shows `[SHOP-PREVIEW] show tower_id=generic/fire/porter`, `hide`. |
| `["godot",...,"--harness=res://tests/scenarios/tower_descriptions_tooltip.json"]` | exit 0, `status=pass`, 15/15 actions ok (12-tower XML description coverage). |
| `["python3","tests/run_all_shard.py","0","1"]` | run_project_cmd tool timeout at 420 s (twice: checker + implementor). ~76 scenarios × ~30 s boot each cannot fit one call. Not a project failure and not a runner-blocker; per-scenario runner runs are the substitute evidence. Implementor ran all scenarios individually via the same runner: 16 non-pass, all reproduced pre-existing on base commit 42e05d6 in a sibling worktree (LFS-smugged glTF time dilation, two random flakes). None touch this change's surface. |

## Acceptance criteria evidence

1. Public API shows wood HUD card (`SidePanel` frame + `ModalWell` + `ModalTitle`) — PASS.
   `scenes/ui/widgets/TowerShopPreviewCard.tscn` uses those theme variations; harness asserts
   visibility via `is_shop_preview_card_visible`.
2. Title = display name, wrapping description with readable contrast, distinct stat rows
   (Cost/Range/Fire Rate/Damage glyphs), no jammed "Cost: X | Range: Y" line — PASS by code
   review (`TowerShopPreviewCard.gd`: one HBox row per stat, caption/value labels, autowrap
   description) + harness assertion `!contains "|"` on card text. Pixel legibility still needs
   the manual screenshot pass (see Pending/unverified).
3. Card above hovered slot, not covering bar, no overlap with right details panel — PENDING
   (demoted). `_position_shop_preview_card()` implements clamping (hardcoded `-400.0`
   right-margin guard), but no headless assertion exists and no manual report verifies pixels.
4. `hide_tower_shop_preview()` hides completely — PASS (harness: visible==false after hide;
   event log shows hide).
5. Slot `mouse_entered`/`mouse_exited` wired in `_populate_tower_button` to the same public
   methods — PASS by code review (signals bound once with is_connected guard); public-API path
   exercised by harness.
6. Unlocked slots' default tooltip empty; locked keep lock reason — PASS
   (`_update_single_tower_button_state` sets `tooltip_text = reason`; expectation
   `shop_slot_tooltip_texts_are_empty == true` passes in harness).
7. One content source; Porter teleport explanation exactly once; Floodgate one flood
   explanation — PASS. Both render from `_tower_tooltip_facts`; hardcoded duplicates removed
   from UI.gd; harness asserts `count_occurrences_in_shop_preview_text("Teleports enemies")==1`.
8. All 12 combat towers keep special descriptions; Porter's states tunnels + no damage — PASS.
   Verified in `data/towers.xml` (12 descriptions present, block is decoration not combat);
   `tower_descriptions_tooltip` scenario passes 15/15 actions covering the roster.
9. Debug-build `[SHOP-PREVIEW]` show/hide logs naming tower id — PASS. Observed live in the
   runner stdout this check (`[SHOP-PREVIEW] show tower_id=generic` etc.);
   `OS.is_debug_build()` guards present.
10. Focused harness scenario ends `status=pass` — PASS (fresh run this check).
11. Fresh editor/import gate exits 0 with no changed-file diagnostics — PASS (fresh run).

## Changed-file quality findings

- `scripts/ui/UI.gd`, `scripts/ui/hud/TowerShopPreviewCard.gd`, `systems/TowersConfig.gd`,
  `data/towers.xml`: clean; typed declarations throughout, small focused functions, guard
  clauses, signal connect guarded, debug-only logging per CLAUDE.md. No quality demotions.
- Scope creep note (advisory only): `logs/balance/map_difficulty.csv` is modified but is a
  generated artifact rewritten by every harness run (balance_guard writes it); not agent
  scope creep. Untracked `.gen-r1-pass-20260823/` scratch dir is prior-round residue,
  uncommitted and harmless; should not be committed with the feature.
- No test-overlap violations: `tower_shop_preview_card.json` is new coverage (preview card
  API); `tower_descriptions_tooltip.json` covers XML descriptions — complementary, no
  duplicated assertions found on the same code path.

## Blockers

- Full-suite single-shard invocation exceeds run_project_cmd's 420 s cap — tooling
  limitation, worked around with per-scenario runner runs; fixable if the shard script gains
  a scenario-subset mode or the runner timeout is raised.

## Unverified items

- Manual windowed screenshots of Generic / Fire / Ice / Porter cards per
  `.gen/ui_scenario.md`: NOT yet performed; no `.gen/manual-report.md`. Criterion 3 stays
  Pending until the manual tester supplies vision-readable PNGs showing the card placement.
