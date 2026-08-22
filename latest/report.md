# Team-leader report

- **Result:** failed
- **Classification:** fixable
- **Feature:** towers-bar-slot-row-overflow-margin
- **Run:** issue105-towers-bar-slot-row-overflow-margin-1
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- The pixel budget arithmetic from issue #105 (12 x slot min width + 11 x separation + UpdateTowersBtn width + 2 x BarRow separation vs the bar's available width at 1920) is recorded as a comment next to whatever constant governs it, so future edits see the remaining headroom.
- Debug-build `[TOWERS_BAR]` log line per overflow-degradation event, naming the roster size and which degradation path engaged.

## ⬜ Pending
- With the full surface roster unlocked at the 1920 logical viewport, every TowerShopSlot in Root/ButtonsContainer/Panel/BarRow/TowerButtons is fully visible inside the TowersBarPanel: no slot's rect extends past the panel's inner rect and the slot row's reported width does not exceed the panel's available width.
- When the roster outgrows the bar (a 13th+ surface tower exists), the slot row degrades by design instead of clipping: slots shrink down to a documented floor size, or the row scrolls, or the bar reserves a second line — and in every case no slot rect extends outside the TowersBarPanel inner rect at the 1920 logical viewport. — quality: scripts/ui/UI.gd: shrink-to-floor clamps to TOWERS_BAR_SLOT_FLOOR_WIDTH even when the fair share is below floor, so with enough slots the row can still exceed the panel inner rect (fresh harness run: all_slots_inside == false at roster 13)
- The TowerButtons node remains an HBoxContainer (never converted to a flow container) after loading a map on both surface and underground layers, and after an underground round trip the bar panel's height returns to its pre-trip value (no permanent growth).
- A `game-test` scenario loads a map with the full roster unlocked, captures a windowed screenshot checkpoint of the towers bar, and its headless assertions report pass with no slot clipped (or the slot row's width measured within the bar's inner width). — quality: tests/scenarios/towers_bar_slot_overflow.json: scenario times out (all_slots_inside never true at roster 13); windowed manual pass not performed
- The same scenario leaves the underground trap row (UndergroundTraps with Trap1/Trap2/Trap3/Trap5) laid out inside its panel with no regression versus current behavior.

## ❌ Impossible
- (none)

## Check

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
