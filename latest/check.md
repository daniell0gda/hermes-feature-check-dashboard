# Check report — issue #123 progression_pick scenario (revision-check-1, iteration 2)

Classification: **fixable**

## Runner gate

- Preflight `run_project_cmd` `["godot","--version"]` project=poke-defense-godot
  workspace=poke-defense-godot/issue-progression-pick-scenario-stale → exit 0,
  Godot 4.4.1.stable.official.49a5bc7b6.
- Focused scenario `["godot","--headless","--path",".","res://scenes/Main.tscn",
  "--","--harness=res://tests/scenarios/progression_pick.json"]` → runner exit 1
  (4231 ms wall). Fresh evidence: `.gen/harness/progression_pick/result.json`
  (status: **timeout**) and `.gen/harness/_logs/progression_pick.out.log`.
  No separate typecheck/build command is defined for this Godot data-only change;
  the harness run doubles as the build/parse gate (project parsed and ran).

## Acceptance criteria

1. **Scenario places a venom tower / owns a compatible tower before chest +
   venom_miasma_bloom; scenario must pass.** — FAIL.
   - The diff's new actions all succeed: `_set_tower_availability(8)` ok,
     `wait_for_condition` on `draw_choices_for_chest(100)` containing
     venom_miasma_bloom ok, `place_tower kind:"venom"` ok.
   - But the real chest draw at open_chest remains
     `[Progression] draw_choices_for_chest: force_mode=false, flagged=0, normal=37, chosen=2`
     — venom_miasma_bloom has `"forceVisibility": false`
     (scripts/progression/venom_tower.json lines 14/28/41), so it is not in the
     2-of-37 weighted draw on seed 20260726. Action index 71 blocked:
     "progression modal could not be answered with policy
     {\"mode\":\"upgrade\",\"upgrade\":\"venom_miasma_bloom\"}".
   - result.json expectations FAILED: venom_miasma_bloom == 0 (want 1),
     get_venom_miasma_config.enabled == false (want true), tree.paused == true
     (want false); only current_layer passed.
   - Engine error also present: ProgressionModal cannot become exclusive child
     while CaveDangerConfirmDialog holds exclusivity (window.cpp:992).
2. **Preserve and verify chest_duplication granted before carving, including
   cave_chest_duplicate RNG site behavior.** — PASS as observable:
   apply_progression(global.json, chest_duplication) is timeline action index 1,
   before all carving; carving and open_chest proceed; reward granted
   (money=59 at modal).
3. **Update "Reaching a chest organically" in
   .claude/skills/game-test/REFERENCE.md to match the fixed recipe and tower
   requirement.** — FAIL. Section (~line 350) still says "That scenario is
   currently red" and still claims venom_miasma_bloom is "the only progression
   with forceVisibility: true", contradicting venom_tower.json
   (forceVisibility: false). git diff touches only the scenario JSON.
4. **Native Linux Godot/project-runner verification, fresh focused evidence,
   raw diagnostics inspected separately from harness status.** — DONE by
   checker: all commands via run_project_cmd; fresh result.json + .out.log read
   separately this revision.
5. **Do not close/merge/push; commit not requested.** — OK: working tree has
   only the modified tests/scenarios/progression_pick.json; HEAD unchanged
   (996f282).

## Changed-file quality

Diff remains data-only (15 added JSON lines in the scenario). No coding-rule
violations in changed lines. Same accuracy concern as iteration 1: the inline
wait_for_condition draws 100 choices, asserting far more than the real modal
draw (2 weighted picks), masking the actual failure mode.

## Quality notes

No quality-notes.md present; nothing appended (data-only diff, no scope creep;
.gen/dashboard artifacts excluded by policy).

## Blockers

None infrastructural. Remaining work is implementation: make the chest draw
actually offer venom_miasma_bloom for an owned-venom run (fix the
forceVisibility/eligibility interplay or otherwise pin the offer), address the
CaveDangerConfirmDialog exclusive-window conflict if it blocks modal answering,
update REFERENCE.md, then rerun the focused scenario to green.

classification: fixable
