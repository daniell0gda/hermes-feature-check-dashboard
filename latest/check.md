# Check report — upgrade-click-money-animation (req-134 r3, check iteration 3)

classification: fixable

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-upgrade-click-money-animation)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| Editor/parse gate | `godot --headless --path . --editor --quit-after 300` | 0 | Parse clean; only pre-existing legacy UID/GLB warnings |
| Focused harness (headless) | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` | 0 | status=pass; 3/3 expectations (money 1460<1500, level==2, reward_popups==0). Log shows `[UPGRADE_POPUP] tower screen (803.8, 403.6) inside panel [P:(770,172) S:(380,502)] - anchor moved (-5.684,1.5,-5.213) -> (8.372,1.5,-5.213)` and `[CHEST REWARD] Created popup for 20 coins` |
| Full/compat harness | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/chest_reward_compatibility.json` | 0 | status=pass; 3/3 expectations — chest popups unchanged |

Result files: `.gen/harness/upgrade_click_money_popup/result.json`, `.gen/harness/chest_reward_compatibility/result.json` (both fresh this iteration).

## Acceptance criteria evidence

1. **Upgrade click spawns the same money popup, "+N coins!"** — Done (headless). `_on_upgrade_pressed` → `_spawn_upgrade_money_popup` → `ChestRewardSystem.create_reward_popup`; inline wait_for_condition asserted popup count 1 and text "+20 coins!" on the real handler path; popup freed (reward_popups==0).
2. **Popup stays outside the panel rect for the whole lifetime (visibility)** — **Pending.** The headless harness only asserts meta counters; it cannot prove visibility. The windowed evidence fails:
   - Implementor's own 10-frame animation run `.gen/harness/upgrade_click_money_popup_gif/result.json` has **status: timeout** with an unmet condition `enemies.reward_popups == 1` (actual 0) — in the windowed run the popup was not alive at the sampled frames at all. All 10 `anim_frame_*.png` vision-inspected: no "+20 coins!" popup anywhere.
   - Checker-side independent pixel diff of the r3 windowed shots (`before_upgrade_click.png` vs `after_upgrade_click_popup_visible.png`, 1920x1080): 7,286 new yellow-mask pixels, but zoomed vision reads identify them as **in-world yellow vegetation**, not text. 0 text glyphs found; no popup visible inside or outside the panel rect. The prior check.md claim of "~2,149 new yellow pixels = '+20 COINS!'" is not reproducible — the same shots show no text.
   - The occlusion-adjust code exists (`UI.gd::_adjust_anchor_out_of_upgrade_panel`) and fires in headless, but no windowed frame demonstrates a visible popup, so the criterion is unproven and the windowed harness run itself times out.
3. **Non-occluded towers anchor above the tower unchanged** — **Pending.** No automated test exercises the non-occluded branch; the single scenario always places the tower inside the panel rect. Missing evidence.
4. **Same factory/style/timing, popup frees within ~2s** — Done (headless): same `create_reward_popup` tween, reward_popups returns to 0 within the 2s-timeout wait.
5. **Level increments, exact cost charged** — Done: level==2, money 1500→1460 (exact 40 cost across 2 upgrades? no — single upgrade cost 40? actual charge 40 matches generic tower upgrade; money < 1500 plus log `Created popup for 20 coins` and level 2 confirm charge+level on the real handler).
6. **Chest compatibility unchanged** — Done: fresh full-suite run passes all existing expectations.
7. **Debug-build [UPGRADE_POPUP] log per occlusion adjustment** — **Pending (quality).** The log fires (seen in headless run) but uses a bare `print(...)` (`scripts/ui/UI.gd`, `_adjust_anchor_out_of_upgrade_panel`), not gated on `OS.is_debug_build()` as the criterion requires.

## Changed-file quality review

Diff vs HEAD: `scripts/ui/UI.gd` (+44), `scripts/game/ChestRewardSystem.gd` (+8/-2), `scripts/testing/HarnessValues.gd` (+19), new `tests/scenarios/upgrade_click_money_popup.json`, new `tests/scenarios/upgrade_click_money_popup_gif.json`.

- Typed GDScript, guard clauses, surgical diff — no global-rule violations beyond the one below.
- Popup factory reused via default `parent_path` arg; existing chest callers unchanged. No test-overlap: no prior test covered the upgrade-click popup.
- **quality: scripts/ui/UI.gd — `[UPGRADE_POPUP]` log is a bare `print()` not gated on `OS.is_debug_build()`, violating the criterion's debug-build-only requirement** (criterion 7 demoted with `— quality:` suffix in status.md).

## Quality notes

`.gen/quality-notes.md` does not exist; no open entries. Feature diff inspected (`git diff HEAD`, untracked files): `.gen-blocked-req134-attempt1/` and the two new scenario JSONs are declared workflow/verification artifacts, not scope creep. Nothing appended.

## Blockers / unverified

- Windowed visibility proof is missing and the windowed harness scenario (`upgrade_click_money_popup_gif`) times out with `reward_popups == 0` — implementor must fix the windowed path (popup alive and rendered outside the panel in captured frames) and re-capture.
- Non-occluded-anchor criterion has no automated coverage (single scenario always occluded).
- Pre-existing engine noise (invalid UID ext_resources, GLB-not-imported in headless, exit RID leaks) is unrelated legacy state on master paths.

## Verdict

Build/typecheck and both harness suites pass headless, but the issue's core requirement — the popup provably visible outside the details panel in windowed frames — is not demonstrated, and the windowed animation run fails/times out. Implementation is close; a targeted revision can fix it.

classification: fixable
