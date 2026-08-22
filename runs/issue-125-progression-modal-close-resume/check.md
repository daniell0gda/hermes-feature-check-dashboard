# Check verification — issue #125 ProgressionModal close/resume

## Verdict

classification: pass

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-progression-modal-close-resume)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner preflight | `godot --version` | 0 | 4.4.1.stable |
| Editor/parse gate | `godot --headless --path . --editor --quit-after 300` | 0 | no parse errors; script classes registered |
| Focused harness | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_close_resume.json` | 0 | `[Harness] status=pass exit=0`; all 6 expectations pass (`.gen/harness/progression_modal_close_resume/result.json`) |
| Full suite (smoke) | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/smoke_tower_roster.json` | 0 | `[Harness] status=pass exit=0`, zero FAIL lines |
| Windowed visual evidence | `godot --path . --write-movie .gen/manual/harness_close.avi res://scenes/Main.tscn -- --harness=res://tests/scenarios/progression_modal_close_resume.json` | 0 | status=pass; frames extracted and inspected |

Note: HudTheme.tres texture warnings/errors appear on every run including baseline scenarios (missing `res://textures/ui/hud/*.png` imports) — pre-existing, unrelated to this change, does not affect harness results.

## Criteria evidence

Cluster 1 — modal-close-resume-transition (`scripts/ui/ProgressionModal.gd`)
- No modal remains after any close path / renders nowhere — PASS: scenario asserts `modal.count==0`, `visible_count==0`, `any_visible==false` after both the window-close path (`close("harness")`) and accept path (`close("choose_option:money")`); all pass.
- Pause restored to pre-open value — PASS: `close()` restores `_was_paused`; scenario asserts `tree.paused==false` after each close; PREDELETE safety net retained.
- Dismissal effective at/before resume — PASS: code hides before unpausing; post-close `wait_for_duration 0.5s` then `any_visible==false` holds; visually confirmed in frame_02.png.
- `[PROGRESSION_MODAL]` log per close naming path + restored pause state — PASS: log assertions regex-match `close path=harness paused_restored=false` and `close path=choose_option:.* paused_restored=false`. Deviation from strict debug-build gating documented in quality-notes.md (advisory).
- Accept while already unpaused stays unpaused — PASS structurally (`_was_paused=false` captured at open; both closes restored `paused_restored=false` with game unpaused after).

Cluster 2 — close-transition-harness-coverage
- Focused scenario drives close, passes only when modal gone + resumed — PASS: fresh run this iteration, status=pass, would fail if modal remained or tree stayed paused.
- Harness can resolve open modal count/visibility — PASS: new `modal` source in HarnessValues.gd (`count`, `visible_count`, `any_visible`), exercised by the passing expectations.
- Out-of-range choose_option leaves modal open + game paused — PASS: index-99 probe asserts `modal.count==1` and `tree.paused==true` after 0.5s wait.

## Windowed screenshot evidence (required for visible UI)

- `.gen/manual/harness_close.avi` (windowed movie capture of the focused scenario run).
- `.gen/manual/frame_01.png` — mid-run: "Choose a Reward" ProgressionModal visibly open over gameplay.
- `.gen/manual/frame_02.png` — final frame after closes: no modal rendered anywhere; HUD/gameplay visible (money 131.0 reflects accepted reward).

Inspected via vision: modal present before close, absent after, gameplay running. Headless-only bar exceeded.

## Changed-file quality findings

New/changed code reviewed against /opt/data/coding_rules.md and CLAUDE.md: typed GDScript throughout, single close funnel, surgical scope, guard clause against double close. Two advisory entries appended to .gen/quality-notes.md (unconditional log print; redundant `hide()` after `visible=false`). Neither violates a rule that demotes criteria.

## Blockers

None.

## Unverified items

None.
