# Check Report: towers-bar-slot-row-overflow-margin (issue #105)

classification: fixable

## Verdict

FAIL (fixable). The overflow-guard implementation exists and the editor/import
gate passes, but the focused acceptance scenario
`tests/scenarios/towers_bar_slot_overflow.json` fails on a fresh headless run:
with the forced 13th slot, the shrink-to-floor guard engages
(`[TOWERS_BAR] overflow: roster=13 exceeds 1412.0px, degrading via
shrink_to_floor (slot width 104.9)` appears twice in the log) yet
`all_slots_inside == false` persists, the scenario times out at action 9, and
the final expectation block reports `all_slots_inside: False`. The core
criterion "no slot rect extends outside the TowersBarPanel inner rect" is NOT
met under overflow.

## Commands executed (all via run_project_cmd, project=poke-defense-godot,
workspace=poke-defense-godot/issue-towers-bar-slot-row-overflow-margin)

| Command | Exit | Result |
|---|---|---|
| `godot --version` | 0 | runner healthy, Godot 4.4.1.stable |
| `godot --headless --path . --editor --quit-after 300` | 0 | import/parse gate PASS |
| focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/towers_bar_slot_overflow.json` | 1 | FAIL — timeout; expectation `all_slots_inside` false (evidence `.gen/harness/towers_bar_slot_overflow/result.json`, refreshed by checker) |
| full suite via `bash -lc for s in ...` | n/a | rejected HTTP 400 `cmd executable is not allowed by the project profile`; scenarios run individually instead |
| smoke_placement | 0 | pass |
| hud_layer_roundtrip | 0 | pass |
| hud_wood_panels | 0 | pass |
| hud_other_panels | 0 | pass |
| removed_tower_kinds_no_crash | 0 | pass |
| hud_controls_state | 1 | FAIL — also fails with the working-tree change stashed (baseline d241462), i.e. PRE-EXISTING failure, not introduced by this diff |

Note: plan's full-test command uses `bash -lc`, which the profile allowlist
rejects (`bash` not allowed). Individual scenario runs through the runner are
the valid substitute evidence.

## Acceptance criteria status

1. Full-roster 12-slot no-clip at 1920 — PENDING. Scenario action 4 passed
   (`all_slots_inside == true` at roster 12) in the fresh run, but the overall
   scenario fails later, and the criterion shares the failing code path.
2. 13th+ tower degrades by design with no slot outside the panel inner rect —
   PENDING (failing). Guard engages and shrinks to 104.9px but slots still
   measure outside the panel inner rect; likely cause: the guard clamps to
   `TOWERS_BAR_SLOT_FLOOR_WIDTH` even when `fair < floor`, so the row can still
   exceed the available width; also the guard compares `row_min` against
   `tower_buttons_container.size.x` which may lag one layout frame.
3. TowerButtons stays HBoxContainer; no permanent growth after underground
   round trip — SUPPORTED (container_class/trap_container_class expectations
   passed; hud_layer_roundtrip passes) but kept Pending because the owning
   scenario does not complete green.
4. Pixel budget comment next to the governing constant — DONE. Budget comment
   present above `TOWERS_BAR_SLOT_DEFAULT_WIDTH` / `TOWERS_BAR_SLOT_FLOOR_WIDTH`
   in `scripts/ui/UI.gd`.
5. Debug-build `[TOWERS_BAR]` log line per degradation event — DONE. Log line
   observed in out.log naming roster size and degradation path.
6. game-test scenario with screenshot + headless no-clip assertions reports
   pass — PENDING (failing). Scenario file exists and runs, screenshots are
   correctly reported skipped/headless, but assertions fail (see #2). Windowed
   manual pass not performed (manual_testing: required).
7. Underground trap row inside its panel, no regression — SUPPORTED
   (`traps_inside == true` expectation passed) but kept Pending because the
   scenario never completes green.

## Changed-file quality findings

- `scripts/ui/UI.gd` (+146): clean structure, typed helpers, documented
  constants, deferred layout re-check, meta-based restore bookkeeping. No
  global-rule violation found in the new code itself.
- `tests/scenarios/towers_bar_slot_overflow.json`: well-formed; preserves the
  un-weakened no-clip assertion. No overlap with existing scenarios (no prior
  towers-bar overflow coverage existed).
- Advisory (see .gen/quality-notes.md): floor-width clamp can leave the row
  wider than available when even the floor does not fit — directly implicated
  in the failing criterion.

## Blockers

None infra. Runner reachable, workspace present, import cache warm.

## Unverified items

- Windowed manual screenshot pass (manual_testing: required) — not performed;
  required before merge regardless of headless outcome.
