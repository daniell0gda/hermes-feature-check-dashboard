# Check report — upgrade-click-money-animation (req-134 r5, check iteration 5)

classification: fixable

## Verification (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-upgrade-click-money-animation — no host Godot)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable.official.49a5bc7b6 |
| Editor/parse gate | `godot --headless --path . --editor --quit-after 300` | 0 | Parse clean; only pre-existing legacy UID/GLB import warnings. |
| Focused harness | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` | 0 | status=pass, 3/3 expectations (money<1500, level==2, reward_popups==0). Log: `[UPGRADE_POPUP] tower screen (803.8431, 403.5959) inside panel [P: (770.0, 172.0), S: (380.0, 502.0)] - anchor moved ... -> (8.371838, 1.5, -5.212997)` + `popup anchor ... projected screen (1190.0, 403.5959)` |
| Non-occluded scenario | `... --harness=res://tests/scenarios/upgrade_click_money_popup_unoccluded.json` | 0 | status=pass, 4/4 expectations incl. `was_last_upgrade_popup_occluded == false` and `get_last_upgrade_popup_screen_pos != (0,0)` (projected (701.7532, 403.5959)); log shows no "inside panel" line — anchor untouched. |
| Full/compat harness | `... --harness=res://tests/scenarios/chest_reward_compatibility.json` | 0 | status=pass, 3/3 expectations |
| Burst scenario | `... --harness=res://tests/scenarios/upgrade_click_money_popup_gif.json` | 0 | status=pass; `[UPGRADE_POPUP_BURST] 11 frames over 2.002s (first at 0.000s, engine time_scale 0.20)` |

Fresh result files this iteration: `.gen/harness/{upgrade_click_money_popup,upgrade_click_money_popup_unoccluded,upgrade_click_money_popup_gif,chest_reward_compatibility}/result.json`.

## Acceptance criteria evidence

1. **Upgrade click triggers the same money-increase animation (headless asserts keep passing)** — **Done.** Fresh focused run: status=pass; `[CHEST REWARD] Created popup for 20 coins at (8.371838, 1.5, -5.212997)`; money 1500→1460, level→2, reward_popups back to 0.
2. **Windowed burst frames show the yellow "+20 coins!" popup OUTSIDE the panel rect, pixel-anchored to the projected screen position AND visually inspected** — **Pending.** The capture mechanism now meets the timing spec: fresh burst run fired `_on_upgrade_pressed` immediately before `burst_capture` (no wait between, result.json actions 8→9) and logged 11 frames at wall offsets 0.000/0.187/0.387/0.587/0.788/0.988/1.188/1.388/1.588/1.795/2.002s — first frame at 0.0s, ~0.2s cadence, 2.0s span. However, the saved frames on disk are from the Aug 22 windowed run (file mtimes Aug 22/23 12:4x, before this iteration's runs; this iteration's runs were headless, where screenshot checkpoints are `skipped: headless` and produce no pixels). Independent inspection of those saved frames — crops centered on the logged projected anchor (screen 1190, 403.6) at 2–3x zoom, plus a frame-02 vs frame-09 pixel-diff scan — found **no yellow "+20 coins!" text cluster anywhere near the anchor or in any high-diff region**; only yellow vegetation speckles on the grass slope. The prior manual-report PASSED claim is not reproducible from the saved evidence. A fresh windowed (headless:false) burst run with the fixed cadence, followed by crop inspection at the logged anchor, is still required.
3. **Chest compatibility unchanged** — **Done.** Fresh full harness pass (3/3).
4. **`[UPGRADE_POPUP]` print debug-build gated; release prints nothing** — **Done (quality violation fixed).** Both print sites (`UI.gd` ~1191 projected-anchor log, ~1229 occlusion log) are wrapped in `if OS.is_debug_build():`; the gated lines fired in the fresh debug headless run. Prior quality-notes entry resolved.
5. **Run output logs the popup anchor's projected screen coordinates** — **Done.** `[UPGRADE_POPUP] popup anchor (8.371838, 1.5, -5.212997) projected screen (1190.0, 403.5959)` in the fresh focused-run log (and the unoccluded variant logs (701.7532, 403.5959)).
6. **Non-occluded branch exercised by a test asserting an unchanged anchor** — **Done.** `upgrade_click_money_popup_unoccluded.json`: status=pass, 4/4; tower at (-9.4, 1.5, -5.213) projects to (701.7532, 403.5959) — outside the panel rect — with `was_last_upgrade_popup_occluded == false` and no occlusion log line.
7. **Headless harness upgrade_click_money_popup.json keeps passing with existing expectations** — **Done.** Fresh status=pass, 3/3.

## Revision-scope mechanism review (coder report claims vs evidence)

- `AgentHarness.gd::_burst_capture` now reads `interval_sec`, `span_sec`, `time_scale`, `downscale`; `HarnessScreenshot.gd` supports `downscale` on capture; scenario JSON uses `interval_sec: 0.2, span_sec: 2.0, time_scale: 0.2, downscale: 0.5`. The recorded wall offsets confirm the r4 blocker (0.9s first-frame latency, 0.5–0.8s gaps) is fixed at the mechanism level. `time_scale 0.2` stretches the 1.5s popup to ~7.5s engine-time during the burst so slow grabs sample it end-to-end — a reasonable, documented approach.
- Claim "windowed pixel confirmation is the manual tester's step" is not yet satisfied: the newest saved windowed frames predate the cadence fix, and the crops cut from them (crop_anchor_frame_02.png etc.) show no popup. The windowed re-run remains outstanding.

## Changed-file quality review

Diff vs HEAD: `scripts/ui/UI.gd` (+~70), `scripts/game/ChestRewardSystem.gd`, `scripts/testing/AgentHarness.gd`, `scripts/testing/HarnessScreenshot.gd`, `scripts/testing/HarnessValues.gd`, new `tests/scenarios/upgrade_click_money_popup.json`, `upgrade_click_money_popup_unoccluded.json`, `upgrade_click_money_popup_gif.json`.

- Typed GDScript, guard clauses, focused helpers, debug-gated logging per CLAUDE.md conventions; surgical diff; chest callers unchanged via default `parent_path`.
- No test-overlap: no prior upgrade-popup or unoccluded-branch coverage existed in the suite; the new unoccluded scenario covers a previously untested guard path.
- No new quality violations found in this iteration's changed files. The prior UI.gd debug-gating violation is fixed and marked RESOLVED in `.gen/quality-notes.md`.

## Quality notes

`.gen/quality-notes.md`: open `debug-gating` entry from iteration 4 resolved this iteration (both print sites now gated on `OS.is_debug_build()`). Feature diff inspected (`git diff HEAD` + untracked): scenario JSONs and declared workflow artifacts (`.gen*`) are not scope creep.

## Blockers / unverified

- Criterion 2 (windowed yellow-cluster pixel proof at the projected anchor, visually inspected crop) remains unverified: no post-fix windowed frames exist. Everything needed to produce them (fast burst, projected-coord log, downscale) is in place and verified headless; only the windowed execution + crop inspection is missing.
- Manual tester's earlier PASSED verdict rests on frames this check could not confirm; treat as superseded by a fresh windowed run.

## Verdict

Build/typecheck and all three headless harnesses (focused, unoccluded, compat) are green through the runner; burst-capture timing, projected-coord logging, debug gating, and non-occluded coverage are now Done. One criterion (windowed pixel evidence) remains Pending and is achievable with a single windowed run of the existing gif scenario plus crop inspection.

classification: fixable
