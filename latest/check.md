# Check report: perk-undermining (iteration 3, revision 2)

classification: pass

## Verdict

All 10 acceptance criteria verified Done. Fresh verification executed through
`run_project_cmd` (project=poke-defense-godot, workspace=poke-defense-godot/issue-perk-undermining)
in this check pass; every command returned exit 0 with `[Harness] status=pass exit=0`.

## Verification commands and results (all via run_project_cmd)

| Command | Exit | Result |
|---|---|---|
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/undermining_progression.json` | 0 | `[Harness] status=pass exit=0`; log shows L1→8.0, L2→15.0, L3→25.0 |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/undermining_trap_armor.json` | 0 | `[Harness] status=pass exit=0`; live strips on map_7 wave 6 armored Orc Enemy King: unowned trap_01 leaves armor exactly 60.0; trap_02@L1 60→52; trap_03@L2 52→37; trap_05@L3 37→12 then 12→0 clamped |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/undermining_scope_isolation.json` | 0 | `[Harness] status=pass exit=0`; surface tower hits under owned L3 change armor only by explicit armor_damage, no perk-derived loss |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/enemy_armor_trap.json` | 0 | `[Harness] status=pass exit=0` (full/baseline scenario) |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/trap_stats_attribution.json` | 0 | `[Harness] status=pass exit=0` |
| `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/traps_serrated_edges_progression.json` | 0 | `[Harness] status=pass exit=0`; existing serrated-edges perk unaffected by the TrapProgressionManager refactor |
| `godot --headless --editor --path . --quit-after 300` | 0 | Editor typecheck/import gate clean |

Fresh result.json files confirmed under `.gen/harness/<scenario>/result.json`.

## Criterion evidence

1. **Definition (Common/traps-only/3 levels)** — `scripts/progression/trap.json`: `undermining`, type Common, maxLevels 3, values 8/15/25. It lives only in the trap progression pool; surface towers never read it. Proven in undermining_progression run.
2. **8/15/25 accessor** — `TrapProgressionManager.get_undermining_armor_damage()` exposed through `ProgressionManager.get_trap_armor_damage()`; harness asserted 8 → 15 → 25 through the same accessor Ballista uses; reset/base is 0.0 when unowned.
3. **Per-hit armor strip, clamped** — `Trap.perform_hit()` reads the perk value per hit and passes it to `take_damage(..., armor_damage)`; live log shows exact deltas at each level and clamp at 0 (37→12→0).
4. **Zero armor change unowned** — unowned trap_01 hit left armor at exactly 60.0 (asserted in undermining_trap_armor).
5. **Surface scope isolation** — undermining_scope_isolation passes with perk owned at L3; only `Trap.gd` calls `get_trap_armor_damage()`.
6. **Debug log** — observed fresh this run: `[Undermining] strip enemy=Orc Enemy_boss trap=trap_02 armor_damage=8.0 armor 60.0->52.0` etc., guarded by `OS.is_debug_build()`.
7. **Strip tint** — amber `UnderminingStripVFX` fired via `EffectsManager.play_undermining_strip()` only when `stripped` is true (armor_damage > 0 and armor_before > 0); absent entirely on unowned hits. Distinct amber color vs StaticBreach blue.
8–10. **Coverage scenarios** — all three new scenarios exist in `tests/scenarios/` and passed fresh; assertions are wait_for_condition equality checks against live state (would fail if behavior broke). No overlap with pre-existing coverage: enemy_armor_trap asserts baseline unowned behavior only and was preserved; the new scenarios assert perk-specific behavior not covered elsewhere.

## Changed-file quality findings

Reviewed diff (`git status`/`git diff`) against /opt/data/coding_rules.md and worktree CLAUDE.md:
typed GDScript members, no casts of raw enum strings, surgical changes confined to feature files,
reuses existing VFX pattern (StaticBreachVFX-style) rather than inventing new plumbing. No violations.

Pre-existing benign warnings (invalid UID ext_resources, missing GLB imports, RID leak at exit)
appear in baseline scenarios too — unrelated to this feature, not attributed to it.

## Quality notes

No changes. No open prior entries required resolution; no cross-cutting scope creep found
(feature diff touches exactly the planned files plus the three planned test scenarios).

## Blockers

None. Manual player-facing verification remains assigned per plan's `manual_testing: required`
(manual-tester profile owns that evidence).

## Unverified items

None beyond manual visual confirmation of the tint in a rendered (non-headless) session, which is
the manual-tester's remit, not a code criterion gap — headless counting of strip dispatches covers
the logic criterion.
