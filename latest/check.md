# Check report: Warlord's Doctrine (iteration 1)

classification: fixable

NOTE (revision 1, code worker): the two automated-evidence Pending items below are now
covered by an extended `tests/scenarios/warlords_doctrine.json` (level leg asserts
multiplier 1.05/1.09/1.14 at L1/L2/L3; innate-armor leg on map_7 wave 6 asserts
max_armor 303.75 = innate 60 + 15%×1625 and that stripping exactly the granted 243.75
leaves innate 60). Focused run status=pass exit 0; all full-suite legs pass; editor
typecheck clean. See `.gen/coder-reports/implementation.md`. The windowed visual
check remains reserved for the manual tester.

## Verdict
Implementation is functionally correct for what is tested; all runner gates pass fresh
via `run_project_cmd` (project=poke-defense-godot, workspace=poke-defense-godot/issue-warlords-doctrine).
Three criteria lack adequate automated/manual evidence and are moved to Pending.

## Verification commands (all via run_project_cmd, exit codes real)
- Typecheck/build: `godot --headless --path . --editor --quit-after 300` — exit 0, no script errors.
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json` — exit 0, `[Harness] status=pass exit=0`.
- Full suite legs: same invocation for `enemy_armor_ballista`, `enemy_armor_trap`, `enemy_armor_bar_visual` — each exit 0, `status=pass`.

## Criteria evidence

Cluster 1 — perk-definition-and-damage-chain:
- Catalog definition (Common global, exactly 3 levels, 5/9/14% dmg + 8/12/15% armor): DONE — verified directly in `scripts/progression/global.json` diff.
- Multiplier exactly 1.05/1.09/1.14 at L1/L2/L3: PENDING — harness asserts only L1 (1.05); L2/L3 (1.09/1.14) never applied/asserted in any scenario. Missing evidence.
- Additive stacking with tower_dmg: DONE — run log `[PROGRESSION] apply tower_dmg L1 multiplier=1.10`; scenario asserts 1.10.
- reset_for_new_game → 1.0 / armor ratio 0: DONE — scenario asserts multiplier == 1.0 and doctrine level == 0 after reset; `_warlds_damage_ratio` cleared in both `reset_for_new_game()` and `_load_state_and_apply()`.

Cluster 2 — spawn-bonus-armor-and-health-bar:
- Unarmored enemy spawns armor>0/max_armor>0 with perk, zero without: DONE — run log `[WARLORDS-DOCTRINE] spawn_bonus level=1 granted_armor=1.76 on Mushnub` (8% of hp 22); baseline zero-armor spawn proven in enemy_armor_ballista leg (Mushnub takes raw 10 dmg, no [Armor] line).
- Innate-armor enemy: bonus added on top of innate: PENDING — no scenario applies the perk while spawning an innate-armored enemy (enemy_armor_ballista/trap run pre-perk). Missing evidence.
- Scripted armor hit strips granted armor first, bonus lands in HP: DONE — result.json: armor_hit(21 dmg / 5 armor_damage) → `[Armor] Mushnub depleted: 1.76 armor removed by 5.0 armor damage`, hp==1, instance damage ==21.
- Armor bar row appears on previously-unarmored enemy (windowed visual): PENDING — generic ArmorRow path exists and `enemy_armor_bar_visual` passes headless, but the required windowed visual checkpoint belongs to the manual tester; `.gen/manual-report.md` absent. Unverified.
- [WARLORDS-DOCTRINE] debug log lines per application and per spawn, filterable: DONE — both lines observed verbatim in focused-run output, gated by `OS.is_debug_build()`, marker-prefixed.

## Changed-file quality findings
- Diff reviewed against /opt/data/coding_rules.md + worktree CLAUDE.md. Typed locals used, debug logging follows the [TAG] convention, nesting shallow, no dead code. One advisory note appended to quality-notes.md: whitespace-only reformatting of unrelated entries in `scripts/progression/global.json` inflates the diff (no functional risk).
- Test overlap: new `tests/scenarios/warlords_doctrine.json` does not duplicate existing scenarios — existing armor scenarios run pre-perk and serve as its zero-armor baseline.

## Blockers
None (runner healthy; no infra failure).

## Unverified items
- L2/L3 damage multipliers end-to-end.
- Doctrine bonus on innate-armor enemy with perk active.
- Windowed armor-bar visual check (manual tester).
