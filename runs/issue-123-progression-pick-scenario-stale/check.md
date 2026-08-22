# Check report — issue #123 progression_pick scenario (iteration 1)

Classification: **fixable**

## Runner gate

- Preflight `run_project_cmd` `["godot","--version"]` project=poke-defense-godot
  workspace=poke-defense-godot/issue-progression-pick-scenario-stale → exit 0,
  Godot 4.4.1.stable.official.49a5bc7b6.
- Editor/import gate `["godot","--headless","--path",".","--editor","--quit-after","300"]`
  → exit 0 (9.2 s), class-name scan clean.
- Focused scenario `["godot","--headless","--path",".","res://scenes/Main.tscn","--",
  "--harness=res://tests/scenarios/progression_pick.json"]` → runner exit 1
  (4237 ms wall); harness wrote fresh evidence:
  `.gen/harness/progression_pick/result.json` (status: **timeout**, finished
  2026-08-22T08:40:39, seed 20260726) and `.gen/harness/_logs/progression_pick.out.log`.

## Acceptance criteria

1. **Update tests/scenarios/progression_pick.json so it places a venom tower / owns a
   compatible tower before opening the chest and selecting venom_miasma_bloom; the
   scenario must pass.** — FAIL.
   - Diff adds `_set_tower_availability(8)`, a `wait_for_condition` proving
     `draw_choices_for_chest(100)` contains `venom_miasma_bloom`, and
     `place_tower kind:"venom"` before carving/opening. Those actions all return ok.
   - But the real chest draw (`count = 2`) pulls 2 weighted picks from a 37-item
     normal pool (`flagged=0, normal=37, chosen=2` in the log;
     `venom_miasma_bloom` has `"forceVisibility": false` in
     scripts/progression/venom_tower.json), so on this seed the modal does not
     contain venom_miasma_bloom: `[Harness] progression modal could not be answered
     with policy {"mode":"upgrade","upgrade":"venom_miasma_bloom"}` at action index 71.
   - result.json: `status: "timeout"`; expectations FAILED —
     progression.venom_miasma_bloom == 0 (want 1),
     progression_call.get_venom_miasma_config.enabled == false (want true),
     tree.paused == true (want false). Only current_layer passed.
   - Additional engine error in log: ProgressionModal cannot become exclusive child
     while CaveDangerConfirmDialog already holds exclusivity
     (`window.cpp:992 _set_transient_exclusive_child`) — may also block modal answer.
   - The scenario does not pass; criterion unmet.
2. **Preserve and verify chest_duplication granted before carving, including
   cave_chest_duplicate RNG site behavior.** — PASS as far as observable:
   `apply_progression(global.json, chest_duplication)` remains the first timeline
   call before any carve; seeded_rng.cave_chest_duplicate = 3433571847 recorded in
   result.json; open_chest reward 59 granted.
3. **Update “Reaching a chest organically” in .claude/skills/game-test/REFERENCE.md
   to match the fixed recipe and tower requirement.** — FAIL.
   Section (line ~350) still says "**That scenario is currently red** (issue #123 …)"
   and still claims `venom_miasma_bloom` is "the only progression with
   `forceVisibility: true`", which contradicts the current
   scripts/progression/venom_tower.json (`"forceVisibility": false`). Untouched by
   the change (git diff touches only the scenario JSON).
4. **Native Linux Godot/project-runner verification, fresh focused evidence, raw
   diagnostics inspected separately from harness status.** — DONE (by checker):
   all commands via run_project_cmd; fresh result.json + .out.log read separately.
5. **Do not close/merge/push; commit not requested.** — OK: working tree has only
   the modified scenario JSON, HEAD unchanged (996f282).

No plan.md or prior status.md exists in the worktree or .gen (small-mode run);
criteria taken verbatim from .gen/request.md.

## Changed-file quality

Diff is data-only (15 added JSON lines in tests/scenarios/progression_pick.json).
No coding-rule violations in the changed lines. One accuracy problem: the new
inline `wait_for_condition` draws 100 choices, which masks the real modal behavior
(2 weighted picks from the non-flagged pool) — the scenario asserts a stronger
condition than what gameplay exercises.

## Quality notes

No quality-notes.md found; none appended (data-only diff, no cross-cutting scope
creep; dashboard/.gen artifacts excluded by policy).

## Blockers

None infrastructural. The remaining work is implementation: either restore
deterministic offering of venom_miasma_bloom for an owned-venom run (e.g. its
`forceVisibility` flag / eligibility interplay — note REFERENCE documents it as
forced, the data says otherwise), or make the scenario robust to the 2-of-37
weighted draw; plus resolve the CaveDangerConfirmDialog exclusive-window conflict
if it blocks modal answering; then update REFERENCE.md and rerun the focused
scenario to green.

classification: fixable
