# Check report — upgrade-click-money-animation (req-134 r4, check iteration 4)

classification: fixable

## Verification (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-upgrade-click-money-animation — no host Godot)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| Editor/parse gate | `godot --headless --path . --editor --quit-after 300` | 0 | Parse clean; only pre-existing legacy UID/GLB warnings. (First attempt hit runner exec exit 137; identical rerun passed — transient infra, not a code failure.) |
| Focused harness | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` | 0 | status=pass, 3/3 expectations. Log: `[UPGRADE_POPUP] tower screen (803.8431, 403.5959) inside panel [P:(770,172) S:(380,502)] - anchor moved (-5.684,1.5,-5.213) -> (8.371838,1.5,-5.212997)` + `[CHEST REWARD] Created popup for 20 coins` |
| Full/compat harness | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/chest_reward_compatibility.json` | 0 | status=pass, 3/3 expectations |

Fresh result files this iteration: `.gen/harness/upgrade_click_money_popup/result.json`, `.gen/harness/chest_reward_compatibility/result.json`.

## Acceptance criteria evidence

1. **Windowed capture starts immediately after the click (~0.1s first frame, ~0.2s interval, ~1.5s span, no pre-waits)** — **Pending.** The windowed run on record (`.gen/harness/upgrade_click_money_popup_gif/result.json`, headless:false) still has `wait_for_duration seconds=0.18` between the click and the first screenshot whose `elapsed_wall_sec` is **0.913**, and every subsequent frame gap is 0.52–0.79s wall — not ~0.2s. The final `wait_for_condition reward_popups == 1` fails (`actual: 0`, action `ok: false`) and the scenario's overall status is timeout-shaped: by the sampled frames the 1.5s popup was already dead. The revision-scope timing fix was not made.
2. **Run output logs the projected screen coords of the anchor** — **Pending as stated.** No dedicated log line for a *projected anchor* exists; the only coordinates logged are inside the ungated `[UPGRADE_POPUP] print` of the tower's *pre-adjustment* screen position plus world-space anchors. In windowed runs that log did not appear at all (0 matches in `.gen/harness/_logs/upgrade_click_money_popup_gif.out.log`).
3. **Windowed frames show compact yellow "+20 coins!" cluster at projected anchor ± margin OUTSIDE panel rect, text-vs-vegetation distinguished, crop visually inspected** — **Pending.** Independent vision inspection this iteration of the saved crops (`.gen/screenshots/popup_zoom_after_upgrade_click.png`, `after_upgrade_click_popup_visible.png`): the "popup" in the zoom crop is small dark-outlined yellow glyphs over the stone path, but the full-frame after shot shows **no readable popup anywhere**, including x≈730–1060/y≈350–470 where the manual report places it; nothing demonstrably sits outside the details panel rect (the panel itself is closed in that shot). Yellow vegetation false-positive risk remains unexcluded. Manual-report claims are not independently reproducible from the saved frames.
4. **Debug-build [UPGRADE_POPUP] log per occlusion adjustment, gated on OS.is_debug_build(); release prints nothing** — **Pending (quality).** `scripts/ui/UI.gd:1210` uses a bare `print(...)`. It fires ungated in my fresh headless run; a release build would also print.
5. **Non-occluded branch exercised by test (unchanged anchor asserted)** — **Pending.** Neither scenario places a tower outside the panel rect; the guard-clause path (`not panel_rect.has_point(screen_pos)` / hidden panel) has zero automated coverage.
6. **Headless harness upgrade_click_money_popup.json keeps passing with existing expectations** — Done (fresh this iteration): status=pass, money<1500, level==2, reward_popups==0, inline wait asserts `reward_popup_text contains "+20 coins!"`.
7. **Chest compatibility unchanged** — Done (fresh full harness pass).

## Changed-file quality review

Diff vs HEAD: `scripts/ui/UI.gd` (+44), `scripts/game/ChestRewardSystem.gd` (+8/-2), `scripts/testing/HarnessValues.gd` (+19), new `tests/scenarios/upgrade_click_money_popup.json`, `tests/scenarios/upgrade_click_money_popup_gif.json`.

- Typed GDScript, guard clauses, surgical diff; chest callers unchanged via default `parent_path`; no test-overlap with prior suite (no earlier upgrade-popup coverage existed).
- quality: scripts/ui/UI.gd:1210 — `[UPGRADE_POPUP]` log is a bare `print()`, not gated on `OS.is_debug_build()` (criterion 4 demoted with `— quality:` suffix).

## Quality notes

No prior `.gen/quality-notes.md` existed (created fresh this iteration); the UI.gd debug-gating violation is recorded there as an open entry. Feature diff inspected (`git diff HEAD` + untracked): `.gen-blocked-req134-attempt1/`, scenario JSONs, and `.gen/` artifacts are declared workflow/verification state, not scope creep.

## Blockers / unverified

- Windowed burst-capture rework (criteria 1–3) was the entire point of r4 and is still missing: same late/slow frame cadence, same failed `reward_popups == 1` condition, no anchored pixel proof outside the panel.
- Non-occluded branch has no automated coverage.
- Manual tester's PASSED verdict rests on the same saved shots I could not confirm show a popup; treated as unverified rather than accepted.

## Verdict

Build/typecheck and both headless harnesses are green through the runner, but all three windowed-evidence criteria, the debug-gating criterion, and non-occluded-branch coverage remain open. Targeted revision can fix it.

classification: fixable
