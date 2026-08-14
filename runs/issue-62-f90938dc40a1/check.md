# Issue #62 — independent final checker after revision 1

## Classification

**blocked**

The implementable revision probes produce useful fresh evidence, but the required process-boundary Continue hard stop timed out in both headless and windowed attempts. Exact live-enemy/spawner restoration is still not implemented or verifiable, genuine naptime does not exist in the inspected product path, the smoke-reference document is absent, and the required 3x3 fresh matrix is incomplete. This cannot be classified as `pass` or `fixable` by checker-only work.

## Inputs inspected

- `.gen/plan.md`
- `.gen/revisions.md`
- `.gen/revision-plan.md`
- `.gen/clusters/revision-1.md`
- prior `.gen/check.md` and `.gen/status.md`
- all four `.gen/coder-reports/*.md`, including `.gen/coder-reports/revision-1.md`
- repository `CLAUDE.md` and `/opt/data/coding_rules.md`
- actual worktree status, `git diff --check`, `git diff --stat`, and the source diff
- all fresh revision-1 structured results under `.gen/harness/issue_62_revision1*/`
- all four fresh revision-1 windowed PNGs under `.gen/harness/issue_62_revision1_visual-windowed-1/shots/`

The requested `docs/tests/smoke-tests-reference.md` is absent from the checkout and remains an explicit repository blocker; no replacement was created.

## Minimal fresh checker commands

Project commands used the approved runner only, with `project=godot-td` and `workspace=godot-td/issue-62`:

| Command | Exit/runner result | Evidence |
|---|---:|---|
| `godot --version` | 0 | `4.4.1.stable.official.49a5bc7b6` |
| `godot --headless --path . --editor --quit-after 300` | 0 | Fresh editor/import gate completed; output contained no `Parse Error`, `Failed loading resource`, `Failed to load script`, or targeted resource-loader diagnostic. |

The known timed-out Continue commands were **not repeated**. The worker was released with `remove=true` after the final project command.

## Fresh revision evidence

- Seed: `.gen/harness/issue_62_revision1_seed-headless-1/result.json` — `status=pass`; action 2 records both producer names, `before_wave=1`, `after_wave=2`, `before_token=0`, `after_token=1`; action 3 records `before == after_failure`, `failed=true`, `preserved=true`, and recovery.
- Continue headless: `.gen/harness/issue_62_revision1_continue-headless-continue-1/result.json` — `status=timeout`, zero actions/expectations, `timeout.reason=game scene did not become available`, elapsed `45.002s`.
- Continue windowed: `.gen/harness/issue_62_revision1_continue-windowed-1/result.json` — `status=timeout`, zero actions/expectations, same timeout reason, elapsed `45.019s`.
- Visual headless first attempt: `.gen/harness/issue_62_revision1_visual-headless-1/result.json` — `status=fail`; `final_clear_probe` action returned `ok=false`, `completion_time=0.0`, despite `phase=finished`.
- Visual headless retry: `.gen/harness/issue_62_revision1_visual-headless-2/result.json` — `status=pass`; final-clear action returned `ok=true`, `phase=finished`, `completion_time=0.002`; failure fingerprint preservation/recovery passed. Headless screenshots are correctly `skipped/headless`, not visual evidence.
- Visual windowed: `.gen/harness/issue_62_revision1_visual-windowed-1/result.json` — `status=pass`; Saving, Save safe, genuine final-clear/victory, and recovery actions/screenshots captured. The structured final-clear detail reports `phase=finished` and positive completion time, but also reports `game_state=playing`; the victory pixels are stronger evidence of the visible panel, while the inconsistent field is recorded rather than ignored.

## Criteria-by-criteria findings

| Criterion | Classification/evidence |
|---|---|
| Indicator lifecycle | **Partially met.** Fresh windowed result records real `save_hold_begin` with `indicator=Saving`, then Save safe, and recovery. Saving, Save safe, finished, and recovered PNGs are present and inspected. A fresh windowed Save failed PNG is not present in revision-1; failure is structurally covered by the raw fingerprint probe, not visually covered in this revision. |
| Immediate last-known-good preservation | **Met for the deterministic debug seam.** Seed and visual results record equal `before`/`after_failure` fingerprints, `failed=true`, then a changed `after_recovery` and successful recovery. This proves the named seam, not arbitrary filesystem failure modes. |
| Both real clear producers / exactly-once transition | **Met for the narrow probe.** Seed action `clear_producer_probe` records `Spawner.all_clear` and `SpawnerSystem.all_spawners_clear`; wave advances 1→2 and token 0→1 exactly once. This is not evidence of process-boundary resume. |
| Genuine finished transition | **Partially met / not sufficient for overall acceptance.** The successful visual result drives `final_clear_probe`, records positive completion time and `phase=finished`, and the inspected PNG visibly shows `CONGRATULATIONS`, `All waves defeated!`, completion stats, and Next Map/Restart Map. The first visual headless attempt failed and the structured success detail retains `game_state=playing`, so this criterion is reported with that inconsistency rather than as an unqualified clean pass. |
| Process-boundary Continue/reload | **Blocked.** Both fresh Continue result files contain no actions or expectations and terminate at the exact 45-second path `game scene did not become available`. No second-process map/phase/wave/schema load was proven. Do not retry the known timeout as a speculative cycle. |
| Active-wave resume and exact enemy/spawner restoration | **Blocked/design failure.** The seed proves only a checkpoint payload/phase. No successful Continue comparison exists, and the load path still does not reconstruct exact live enemy identity, position/progress, health/effects, spawner queues, or timers. Aggregate wave/count/payload evidence is insufficient. |
| Post-wave/before-next semantics | **Partially met.** The producer probe proves one guarded increment when both producer signals are emitted. Process-boundary continuation and real user/auto next-wave behavior remain unproven. |
| Checkpoint coverage | **Incomplete.** Main/active/failure/recovery and Saving/finished are represented; genuine interrupted/menu, naptime, exact resume, and complete process-boundary coverage are not. `PHASE_NAPTIME` remains only a label; no real product transition was found. |
| Naptime | **Blocked/design failure.** No real naptime transition exists in the inspected runtime. No fake label or screenshot is accepted as evidence. |
| Automation / 3x3 matrix | **Incomplete.** Unique suffixed result directories work, but only one seed run, two visual headless attempts, one visual windowed run, and the two timed-out Continue attempts are present. Three fresh headless and three fresh windowed focused runs were not completed. |
| Smoke-reference requirement | **Blocked by repository gap.** `docs/tests/smoke-tests-reference.md` is absent; existing scenarios cannot silently substitute for the requested source-of-truth matrix. |
| Diagnostics | **Fresh editor gate clean for targeted parse/resource/script errors.** Successful focused evidence reports no `Parse Error`, `Failed loading resource`, or `Failed to load script`. Recurring missing-node/duplicate-signal, renderer/audio fallback, and shutdown leak/ObjectDB/RID diagnostics from prior focused output remain separate project noise and are not erased by exit 0. |

## Visual observations — every fresh revision-1 windowed PNG

- `.../indicator_saving.png`: 1920x1080; top bar visibly reads bright yellow `Saving`; layout is readable with no clipping or error text; active Wave 1/4 gameplay/debug panel remains visible.
- `.../indicator_save_safe.png`: 1920x1080; green `Save safe` is clearly readable in the top bar; layout is intact and no parse/resource/script error text is visible.
- `.../indicator_finished.png`: 1920x1080; visibly shows `CONGRATULATIONS`, `All waves defeated!`, completion statistics, tabs, and `Next Map`/`Restart Map`; no error text or clipping. This is genuine victory-panel pixel evidence, notwithstanding the structured `game_state=playing` inconsistency noted above.
- `.../indicator_recovered.png`: 1920x1080; green `Save safe` is visible and failure text is cleared; victory overlay remains visible; layout is readable. The vision inspection noted the far-right bottom tower list is somewhat cut by the viewport edge, but the required indicator is fully visible.

## Exact blocker path and recommended next action

Terminal blocker: the real seed→MainMenu→Continue second process was launched in both modes, but the Continue harness reached neither a Game scene nor any action/expectation before its 45-second budget. The exact structured path is `status=timeout` → `timeout.reason=game scene did not become available` in both Continue result files above; runner outcome was exit 1 / transport 422 per the revision report.

Next action: issue owner/design owner must resolve the process-boundary MainMenu→Continue transition and define/implement a deterministic exact enemy/spawner snapshot contract, or explicitly amend those acceptance requirements. A genuine naptime product transition and the missing smoke-reference source must also be resolved. Only then rerun the complete immutable 3-headless × 3-windowed matrix, inspect every PNG (including a visual Save failed checkpoint), and re-check the inconsistent finished `game_state` field. No source/scenario edits, commit, push, merge, dashboard mutation, or issue closure were performed by this checker.
