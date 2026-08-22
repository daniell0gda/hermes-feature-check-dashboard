# Check report — issue #123 progression_pick scenario (revision-check-2, iteration 3)

Classification: **pass**

## Runner gate

- Preflight `run_project_cmd` `["godot","--version"]` project=poke-defense-godot
  workspace=poke-defense-godot/issue-progression-pick-scenario-stale → exit 0,
  Godot 4.4.1.stable.official.49a5bc7b6.
- Focused scenario `["godot","--headless","--path",".","res://scenes/Main.tscn",
  "--","--harness=res://tests/scenarios/progression_pick.json"]` → runner exit 0
  (4236 ms wall). Fresh evidence: `.gen/harness/progression_pick/result.json`
  (status: **pass**, all 4 expectations pass) and raw log inspected separately.
  No separate typecheck/build command is defined for this Godot data-only
  change; the harness run doubles as the build/parse gate (project parsed and
  ran).

## Acceptance criteria

1. **Scenario places a venom tower / owns a compatible tower before chest +
   venom_miasma_bloom; scenario must pass.** — PASS.
   - Timeline: `_set_tower_availability(8)` (action 117), venom pool
     exhaustion via `apply_progression` (actions 3–116),
     `place_tower kind:"venom"` at action 119, before carving/chest.
   - Real chest draw now: `[Progression] draw_choices_for_chest:
     force_mode=false, flagged=0, normal=2, chosen=2` — pool pinned to the two
     venom perks, so the 2-of-2 draw deterministically offers
     venom_miasma_bloom. Modal answered card 2 (upgrade).
   - result.json: status pass; expectations venom_miasma_bloom == 1,
     get_venom_miasma_config.enabled == true, tree.paused == false,
     current_layer == "underground" — all pass.
2. **Preserve and verify chest_duplication granted before carving, including
   cave_chest_duplicate RNG site behavior.** — PASS: apply_progression(global.json,
   chest_duplication) at timeline actions 1–2, before the first carve_rectangle
   at action 121; carving and deterministic chest reward proceed under seed
   20260726 (reward granted; money=59 at modal).
3. **Update "Reaching a chest organically" in
   .claude/skills/game-test/REFERENCE.md.** — PASS: section (~line 350) now
   documents the passing scenario, tower ownership + compatibility filter,
   pool-exhaustion recipe, and corrects the earlier claim: all venom
   progressions have `forceVisibility: false` (matches
   scripts/progression/venom_tower.json).
4. **Native Linux Godot/project-runner verification, fresh focused evidence,
   raw diagnostics inspected separately from harness status.** — DONE by
   checker: all commands via run_project_cmd; raw log read separately from
   result.json status this revision.
5. **Do not close/merge/push; commit not requested.** — OK: HEAD unchanged
   (996f282); working tree dirty only in
   tests/scenarios/progression_pick.json and
   .claude/skills/game-test/REFERENCE.md.

## Changed-file quality

Diff is data + docs only (scenario JSON and REFERENCE.md). No
coding-rule violations in changed lines. Prior iteration's concern (inline
wait_for_condition drawing 100 choices masking the real draw) is mitigated:
the real chest draw now also yields normal=2/chosen=2, and the raw log
confirms the actual modal offer.

## Quality notes

No quality-notes.md present; nothing appended (data-only diff, no scope
creep; .gen/dashboard artifacts excluded by policy).

## Blockers

None. Known pre-existing, non-blocking noise: missing
res://textures/ui/hud/wood_panel.png texture warnings, and the
ProgressionModal/CaveDangerConfirmDialog exclusive-child window conflict
(does not prevent the harness from answering the modal).

classification: pass
