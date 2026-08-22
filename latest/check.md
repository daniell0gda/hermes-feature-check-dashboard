# Check report — upgrade-click-money-animation (req-134 r2, iteration 1)

Classification: fixable

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-upgrade-click-money-animation)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Runner probe | `godot --version` | 0 | 4.4.1.stable.official |
| Editor/parse gate | `godot --headless --path . --editor --quit-after 300` | 0 | Scripts parse clean; no new class-cache issues |
| Focused harness | `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/upgrade_click_money_popup.json` | 1 | status: **fail** — `.gen/harness/upgrade_click_money_popup/result.json`, log `.gen/harness/_logs/upgrade_click_money_popup.out.log` |

## Acceptance criteria evidence

1. "Clicking Upgrade triggers the money-increase animation" — implemented in
   `scripts/ui/UI.gd::_on_upgrade_pressed` → `_spawn_upgrade_money_popup()` →
   `ChestRewardSystem.create_reward_popup()` (same effect chest payouts use).
   Harness timeline actions 7–11 all ok: popup appears (`enemies.reward_popups == 1`,
   text `+20 coins!`) within ~0.3 s of the click and the tower levels to 2.
   **Behaviorally proven while the popup is alive**, but the scenario's final expectation fails.

2. "Animation visually matches existing money-increase effect" — it literally reuses
   `ChestRewardSystem.create_reward_popup`, anchored at tower position +1.5 Y. Proven by
   construction plus live assertion of text/count. Windowed screenshot not captured
   (headless skips screenshots); criterion 3 (windowed screenshot) remains unverified.

3. "Verified in-game via windowed screenshot" — NOT done. Both screenshot checkpoints
   report `outcome: skipped, reason: headless`. No windowed run exists in this iteration.

## Why the focused harness fails

The failure is in the new scenario's own final expectation, not the feature:

```
expectation: enemies.reward_popups == 0   → actual 1  → FAIL
money < 1500        → 1460  → pass
tower.level == 2    → 2     → pass
```

The popup tween lives 1.5 s; after the click the timeline only waits ~0.55 s wall clock
before the snapshot. At expectation time the popup is still on screen (correctly), so
`reward_popups == 0` cannot pass. The inline `wait_for_condition` steps already proved the
popup existed and freed correctly is unproven only for the "freed" half.

Fix (coder): either wait ≥1.5 s after the click before the final expectations (e.g.
`wait_for_duration seconds: 1.3` before the last step), or change the final expectation to a
`wait_for_condition` on `enemies.reward_popups == 0` with a timeout > remaining popup life.
No production-code change is indicated by this failure.

## Changed-file quality review (diff vs HEAD: ChestRewardSystem.gd, HarnessValues.gd, UI.gd)

No rule violations found in changed code:

- Typed GDScript throughout new code; guard clauses keep nesting shallow (CLAUDE.md rules).
- Reuses the existing popup factory instead of duplicating it (code-reuse + surgical changes).
- Harness instrumentation is confined to `HarnessValues._enemy_report()`, tagged via node meta;
  no gameplay logic altered beyond the requested popup spawn.
- Note (advisory): `_spawn_upgrade_money_popup` uses single-line `if x: return` style matching
  surrounding UI.gd conventions — acceptable per "match existing style".

## Quality notes

`.gen/quality-notes.md` does not exist; no prior open entries to re-check. Nothing appended:
no scope creep found (diff is minimal and on-target), no duplicated bad patterns.

## Blockers / unverified

- Criterion 3 windowed screenshot: pending a `-Windowed` harness run (runner supports it;
  headless worker was used). Fixable, not blocked.
- Full-suite regression run: no project-defined "run all scenarios" command exists; the
  editor parse gate plus focused scenario are the available gates. Existing suite untouched
  by this diff except additive fields in HarnessValues (verified parse-clean).

## Verdict

classification: fixable
