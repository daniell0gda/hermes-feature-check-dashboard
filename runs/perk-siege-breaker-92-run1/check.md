# Check report: perk-siege-breaker (iteration 1)

classification: fixable

## Verification commands (all via run_project_cmd, project=poke-defense-godot, workspace=poke-defense-godot/issue-perk-siege-breaker)

| Gate | Command | Exit | Result |
|---|---|---|---|
| Preflight | `godot --version` | 0 | Godot 4.4.1.stable |
| Typecheck/build | `godot --headless --path . --editor --quit-after 300` | 0 | Import/parse clean |
| Focused suite (plan's python3 runner over tests/scenarios/siege_breaker_*.json) | exit 1 | FAILED |

Harness results (.gen/harness/<id>/result.json):

- `siege_breaker_progression` — **pass** (status pass, all inline wait_for_condition actions ok)
- `siege_breaker_damage_rule` — **timeout** at action 18: after L1 apply_progression (level confirmed == 1), a Cannon-attributed `armor_hit` (damage 10, tower_type_id cannon) left enemy hp 1622 instead of the pinned 1621 (expected 10 × 0.35 = 3.5 HP damage). Downstream legs (L2/L3, non-Cannon-at-level checks) never ran; final expectations `instance_summary.8107.damage == 10` and `.8108.damage == 5` failed with actual 0.0.
- `siege_breaker_vfx` — **timeout** at action 7 with the same hp mismatch (actual 1622 vs pinned 1621); expectation `static_breach_flash >= 2` unresolved ("unknown enemy field 'static_breach_flash'" — the live-enemy field resolver in scripts/testing/HarnessValues.gd `_live_enemy_field()` does not expose `static_breach_flash`; only the aggregate `_enemy_report()` does, i.e. source should be `enemies.Orc Enemy_boss.static_breach_flash` as the pre-existing `static_breach_vfx.json` does).
- `enemy_armor_ballista` — **pass** (existing armor regression unchanged).

Full-suite gate: not run past the failing focused suite; the full command necessarily fails while focused scenarios fail.

## Acceptance criteria status

All criteria moved to Pending per the global gate: the project's own focused/full test commands exit 1. Per-criterion evidence:

- Progression data/manager criteria (4): implemented and exercised by passing `siege_breaker_progression` (Unique type, 3-level cap, eligibility, 35%/20%/no-penalty descriptions, factor 0.5/0.35/0.20/0.0, save/load round-trip, reset restores baseline). Would remain green once the damage-rule/VFX scenarios are fixed.
- Damage-rule criteria (6): partially demonstrated — baseline-unowned leg (cannon 0.5 → hp 1620, armor 60→40 untouched, balista still 0.5) passed inline before the timeout, and `enemy_armor_ballista` regression passes. Level 1/2/3 legs unverified due to the hp-arithmetic timeout.
- `[SiegeBreaker]` debug log line: implemented (`EnemyHealthController.gd` SIEGE_BREAKER_LOG_MARKER); observed firing in the vfx log ("[SiegeBreaker] reduced enemy=Orc Enemy_boss level=1 factor=0.35") but the enclosing scenario times out, so the criterion stays Pending.
- VFX criterion: reuse path implemented (`_play_siege_breaker_crack_vfx()` → existing `EffectsManager.play_static_breach_flash()`; no new second effect), but the scenario cannot observe it because the scenario asserts an unsupported field/source combination and times out earlier anyway. Pending.

## Root causes for the two failing scenarios

1. HP arithmetic mismatch: both scenarios pin 1625 − 10×0.35 = 1621.5 ≈ 1621, but the engine lands on hp 1622 (~3 damage). Either the scenario pins ignore engine-side int/rounding or another modifier interacts; implementor must reconcile the pin against actual engine behavior at L1 (and re-pin L2/L3 legs consistently).
2. `siege_breaker_vfx.json` asserts `{source: "enemy", field: "static_breach_flash"}`, but `_live_enemy_field()` rejects that field; the supported form is `enemies.Orc Enemy_boss.static_breach_flash` (as used by the existing `static_breach_vfx.json`). Fix the scenario, do not widen the harness resolver unnecessarily.

## Changed-file quality findings

- scripts/game/actors/enemy/parts/EnemyHealthController.gd — `_siege_breaker_penalty_factor()` resolves ProgressionManager twice (once directly, once via `_siege_breaker_level()`). Minor duplication in new code; consolidate into one lookup.
- Otherwise new code follows worktree rules: typed variables, guard clauses, debug-only `[SiegeBreaker]` logging per CLAUDE.md, surgical diff (104 insertions across 4 files + 3 new scenarios).

## Blockers

None infrastructural. Runner healthy, editor gate green. Failures are ordinary project/test failures → fixable.

## Unverified items

- All 12 acceptance criteria formally Pending until the focused suite exits 0 and the full suite is rerun.
- manual_testing: required — windowed PNGs/GIFs still outstanding (not checker's to produce).
