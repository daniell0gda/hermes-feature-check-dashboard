# Check report: req-85-tooltip-polish-r2 (revision-check-2)

classification: fixable

## Verdict

All 11 code-side criteria remain Done with fresh, passing evidence re-run through the
approved runner this check (focused shop-preview harness, editor/import gate,
regression tooltip scenario, filtered suite slices). Revision 2's only change was a
test-tooling fix (`tests/run_all_shard.py` name-filter argument) addressing the open
quality note; no game code changed and no criterion regressed.

The placement criterion stays Pending for its manual half only: `.gen/manual-report.md`
does not exist yet, so the required windowed screenshot pass (Generic / Fire / Ice /
Porter, real card pixels readable per `.gen/ui_scenario.md`) has not been produced.
Headless verification cannot satisfy `manual_testing: required`.

The build/test gate is **not fully green**: the full-suite invocation still cannot
complete inside run_project_cmd's 420 s cap (details below), and the two extra shard
slices attempted this check exposed three pre-existing failures on scenarios that are
**not part of this feature** (verified pre-existing on an unrelated worktree). Per the
build-and-test gate, the one Pending item remains Pending; no Done item was demoted
because none of the failing scenarios touch this feature's changed files.

## Fresh verification this check (all via run_project_cmd project godot-td,
workspace poke-defense-godot/issue-update-tower-descriptions-with-special-c)

| Command | Result |
|---|---|
| `[\"godot\",\"--version\"]` | exit 0 — Godot 4.4.1.stable.official.49a5bc7b6; runner healthy |
| `[\"godot\",\"--headless\",\"--path\",\".\",\"res://scenes/Main.tscn\",\"--\",\"--harness=res://tests/scenarios/tower_shop_preview_card.json\"]` | exit 0, `[Harness] status=pass exit=0`; fresh `.gen/harness/tower_shop_preview_card/result.json`: status=pass, all 34 actions ok, all expectations pass. Log shows `[SHOP-PREVIEW] show tower_id=generic/fire/porter` + hides; placement waits 18–21 ok (`position.x >= 8`, `end.x <= 1529`, overlap ratio 0.0 vs ButtonsContainer and vs UpgPanel with details panel visible) |
| `[\"godot\",\"--headless\",\"--path\",\".\",\"--editor\",\"--quit-after\",\"300\"]` | exit 0 in ~10 s. Only the pre-existing invalid-UID warnings in untouched `HudTheme.tres`/`UI.tscn`. No parse/resource/script diagnostics on any changed file |
| `[\"python3\",\"tests/run_all_shard.py\",\"0\",\"1\",\"tower_shop\"]` | exit 0 — `PASS tower_shop_preview_card` (revision-2 filter works) |
| `[\"python3\",\"tests/run_all_shard.py\",\"0\",\"1\",\"tower\",\"tooltip\"]` | exit 0 — `PASS tower_descriptions_tooltip` (12-tower description regression intact) |
| `[\"python3\",\"tests/run_all_shard.py\",\"0\",\"1\"]` (full single-shard suite) | run_project_cmd tool timeout at 420 s — infra/tooling cap, not runner-unreachable (probe passed seconds earlier), not a project failure introduced by this feature |
| `[\"python3\",\"tests/run_all_shard.py\",\"0\",\"2\"]` | tool timeout at 420 s (slice too large to finish in cap) |
| `[\"python3\",\"tests/run_all_shard.py\",\"1\",\"2\"]` | exit 137 (runner killed at cap after partial output): 5 PASS, **3 FAIL/timeout** — see "Pre-existing full-suite failures" below |

## Acceptance criteria evidence

Criteria 1–11 (as numbered in prior check.md): all still Done. This check freshly
reconfirmed:

- Public API show/hide + wood card + shared facts + placement geometry: focused
  harness status=pass, all actions/expectations green, `[SHOP-PREVIEW]` events logged.
- Editor/import gate clean on changed scenes/scripts (fresh exit-0 editor run).
- All 12 XML descriptions intact incl. Porter teleport/no-damage:
  `tower_descriptions_tooltip` scenario passes fresh.
- Debug-build log contract: log lines observed in the live runner output.

Placement criterion (the single Pending item):

- Headless half fully verified again (geometry clamps + zero overlap with both the
  towers bar and the open details panel, asserted from real rendered pixels via the
  `shop_preview_card` HarnessValues source).
- Manual windowed screenshot half still outstanding: no `.gen/manual-report.md`.
  Owned by the optional manual-tester profile; headless cannot produce it.

## Pre-existing full-suite failures (not caused by this feature)

The `1/2` slice surfaced three failing scenarios. Each was cross-checked against the
untouched worktree `poke-defense-godot/issue-boss-cave-reward-and-spawner-lifetime-tests`
(commit 57f32a0, 2026-08-19 — five days before this feature branch's base 42e05d6 of
2026-08-24, which itself contains none of this feature's uncommitted changes):

- `cave_decline_seals_reveal_unseals` — PASSES there (exit 0, status=pass). Fails here
  because this worktree's base predates `origin/master` commit 1e2ef2a (2026-08-20,
  cave discovery-curve fix); the scenario JSON exists but the matching engine changes
  are not ancestors of HEAD. Stale base, not feature damage.
- `cave_discovery_long_carve` — same cause: scenario times out here ("unknown cave
  field 'discovery_chance'"); its engine fix 1e2ef2a is not an ancestor of this
  worktree's HEAD either. Pre-existing relative to the declared base.
- `cannon_bunker_buster_progression` — fails identically on the untouched older
  worktree (progression offer list does contain `cannon_bunker_buster`, i.e. the
  exclusion assertion is unsatisfiable at that commit). Pre-existing before this
  feature's base; out of scope for req-85 and recorded here for the leader.

None of these scenarios exercise any file changed by this feature (`UI.gd`,
`TowersConfig.gd`, `towers.xml`, `TowerShopPreviewCard.*`, harness values,
`run_all_shard.py`). They do not demote any criterion of this issue.

During the cross-check the checker temporarily checked out three scenario files into
the *other* workspace (`poke-defense-godot/issue-boss-cave-reward-and-spawner-lifetime-tests`)
via runner git commands and then restored it byte-for-byte to a clean `git status`
(verified empty). The feature workspace was never modified by these probes.

## Changed-file quality findings

- `tests/run_all_shard.py` (revision-2 diff): minimal, documented filter addition;
  AND-combined substring match keeps one invocation under the tool cap. One cosmetic
  wart: a duplicated `sid = os.path.splitext(...)` line (recomputed after the filter
  continue; harmless dead store). Advisory only.
- No new game-code changes in revision 2; revision-code-1 findings stand (no violations
  found previously in `UI.gd` placement/facts code or `TowerShopPreviewCard.gd`;
  typed GDScript throughout, small focused functions, guard clauses).
- No test-overlap introduced: `tower_shop_preview_card.json` extends one scenario;
  the shard-filter change adds no test at all.
- Scope notes (unchanged, advisory): `logs/balance/map_difficulty.csv` churn is a
  generated artifact rewritten by every harness run, not scope creep; untracked
  `.gen-r1-pass-20260823/` scratch must not be committed with the feature.

## Quality notes follow-up

Open entry "full-suite shard timeout" (iteration revision-check-1): the requested
name-filter subset mode now exists and was exercised successfully this check
(`... 0 1 tower_shop` → PASS in ~4 s). However the unrestricted full-suite invocation
still exceeds the 420 s runner cap, so the entry is resolved only in part — appended a
RESOLVED-with-caveat resolution plus a narrower residual note (see quality-notes.md).

## Blockers

- Full-suite single invocation still exceeds run_project_cmd's 420 s cap even sliced
  in halves (~82 scenarios/slice × ~5–30 s boot each). Needs quarter shards, parallel
  shards, or a raised runner timeout before "full test command" can complete as one
  runner call.
- Manual windowed screenshot pass outstanding (manual-tester profile; no
  `.gen/manual-report.md`).

## Unverified items

- Windowed pixel check of the real card for Generic/Fire/Ice/Porter with readable
  special line and correct placement — pending manual tester. This alone keeps the
  placement criterion Pending.
