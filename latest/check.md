# Check report: req-85-tooltip-polish-r2 (revision-check-1, placement criterion recheck)

classification: fixable

## Verdict

Revision 1's placement work is real and verified fresh through the approved runner: the
focused harness now runs on `map_10`, opens the right-side tower details panel, and asserts
real rendered card geometry — on-screen (`position.x >= 8`, `end.x <= 1529`), zero overlap
with `Root/ButtonsContainer` AND `Root/UpgPanel`. Editor/import gate and the regression
`tower_descriptions_tooltip` scenario also pass fresh. The placement criterion stays Pending
only for its manual half: no `.gen/manual-report.md` exists yet, so the windowed pixel check
(Generic/Fire/Ice/Porter per `.gen/ui_scenario.md`) is still outstanding. Everything else
remains Done with passing evidence.

## Fresh verification this check (all via run_project_cmd project godot-td,
workspace poke-defense-godot/issue-update-tower-descriptions-with-special-c)

| Command | Result |
|---|---|
| `["godot","--version"]` | exit 0 — Godot 4.4.1.stable.official.49a5bc7b6, runner healthy |
| `["godot","--headless","--path","."," "res://scenes/Main.tscn","--","--harness=res://tests/scenarios/tower_shop_preview_card.json"]` | exit 0, `[Harness] status=pass exit=0`; `.gen/harness/tower_shop_preview_card/result.json`: status=pass, all actions ok, all 4 expectations pass. Placement waits 18–21 all ok: `shop_preview_card.position.x >= 8.0`, `end.x <= 1529.0`, overlap ratio vs ButtonsContainer == 0.0 and vs UpgPanel == 0.0, with the details panel visible (`ui.visible == true`). Log shows `[SHOP-PREVIEW] show tower_id=generic/fire/porter` + two hides; event count == 6 asserted. |
| `["godot","--headless","--path","."," --editor","--quit-after","300"]` | exit 0. Only pre-existing invalid-UID warnings in untouched `HudTheme.tres`/`UI.tscn`. No parse/resource/script diagnostics on changed files. |
| `["godot","--headless",...,"--harness=res://tests/scenarios/tower_descriptions_tooltip.json"]` | exit 0, status=pass — 12-tower XML description coverage intact after revision. |
| `["python3","tests/run_all_shard.py","0","1"]` | run_project_cmd tool timeout at 420 s (matches implementor's report). Infra/tooling limitation, not a project failure and not runner-unreachable (probe succeeded seconds earlier). Per-scenario runner runs remain the substitute evidence. |

## Acceptance criteria evidence

1–11 as in the prior check.md, all still Done; fresh harness/editor-gate runs this check
confirm criteria 9, 10, 11 live.

Placement criterion (previously Pending) — implementation now verified headlessly:
- `_position_shop_preview_card()` measures max(min_size, laid-out size), clamps horizontally
  to screen minus 8px margins and, when `Root/UpgPanel` is visible, to its left edge minus 8px.
- Per-frame `_process()` bottom clamp keeps the card above `Root/ButtonsContainer`'s top even
  when autowrap labels settle taller one layout pass late.
- Harness asserts both overlap ratios are exactly 0.0 with the details panel open.
- Remaining gap: manual windowed screenshots with vision-readable pixels have not been
  produced (`manual_testing: required` in the request). The criterion stays Pending until
  `.gen/manual-report.md` supplies those shots. Headless cannot satisfy that half.

## Changed-file quality findings

- `scripts/ui/UI.gd` revision diff (placement functions, `_process` clamp, harness reads):
  typed throughout, small focused functions, guard clauses, documented comments explaining
  non-obvious layout-timing decisions. No violations of `/opt/data/coding_rules.md` or
  CLAUDE.md found in changed code.
- `scripts/testing/HarnessValues.gd` new `shop_preview_card` source mirrors the existing `ui`
  geometry-source pattern; no duplication concern (different node under test).
- No test-overlap: `tower_shop_preview_card.json` extends the same scenario rather than
  duplicating an existing test; `tower_descriptions_tooltip.json` covers a distinct path.
- Advisory scope note (unchanged from r1): `logs/balance/map_difficulty.csv` churn is a
  generated artifact rewritten by every harness run, not agent scope creep. Untracked
  `.gen-r1-pass-20260823/` scratch should not be committed with the feature.

## Blockers

- Full-suite single-shard invocation exceeds run_project_cmd's 420 s cap (~165 scenarios ×
  ~30 s boot). Tooling limitation; needs a shard-subset mode or raised runner timeout.

## Unverified items

- Manual windowed screenshot pass (Generic/Fire/Ice/Porter, real card pixels readable,
  placement visible) — owned by the optional manual-tester profile via
  `.gen/manual-report.md`; not present at check time. This alone keeps the placement
  criterion Pending.
