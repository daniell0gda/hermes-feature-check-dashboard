# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/progression/global.json` — mod: added `frozen_fracture` (Common, maxLevels 3, values 0.10/0.20/0.30, descriptions state the armor-damage bonus) to the global Common pool.
- `scripts/progression/managers/CurseProgressionManager.gd` — mod: `frozen_fracture` added to HANDLED; new `_frozen_fracture_bonus` state (absolute per-level ratio, reset-safe), `_apply_frozen_fracture`, `get_frozen_fracture_config()` returning `{enabled, bonus}` (`enabled: false` when not owned).
- `autoload/ProgressionManager.gd` — mod: new `get_frozen_fracture_config()` passthrough to the Curse manager (same pattern as `get_overheat_config`).
- `scripts/game/actors/enemy/parts/EnemyHealthController.gd` — mod: `_consume_armor` now routes armor damage through `_apply_frozen_fracture_if_needed` (boost only while `enemy.slow_time_left > 0` and perk enabled); HP damage path untouched. Debug-only `[FrozenFracture]` log line per boosted application (enemy id, level, bonus percent, boosted armor dmg). New helpers `_frozen_fracture_config`, `_frozen_fracture_level`, `_apply_frozen_fracture_if_needed`; const `FROZEN_FRACTURE_LOG_MARKER = "[FrozenFracture]"`.
- `tests/scenarios/frozen_fracture_slowed_vs_unslowed.json` — new: map_7 wave 6 (single armored Orc Enemy King, armor 60). Leg 1 unslowed with perk L1: identical neutral armor_hit (10 dmg / 20 armor) takes unmodified armor damage 60→40, hp −5. Leg 2 slowed (harness `apply_effect ice`): same hit 60→38 (20×1.10=22), hp −5 again.
- `tests/scenarios/frozen_fracture_levels_and_expiry.json` — new: L1/L2/L3 legs assert armor 38/36/34 after the same hit while slowed (hp −5 each), plus an expiry leg (0.1 s slow + 3 s wall wait until `frozen_count == 0`) asserting baseline 60→40 restored.
- `tests/scenarios/progression_pick.json` — mod: added 3× `apply_progression frozen_fracture` grants after curse_fragile_optics, per that scenario's own "exhaust every other chest-eligible progression" pattern (the new Common perk joined the eligible pool and broke its deterministic 2-of-2 draw).

## Criteria
- Perk in global Common pool, 3 levels, 10/20/30% bonus, disabled when unowned — Done
- Chest draw offers frozen_fracture; level descriptions state the values — Done (Common type passes `_is_chest_compatible`; descriptions carry the percentages)
- Boost while slowed, from any damage source — Done (boost applied inside `_consume_armor`, which every `take_damage` with armor_dmg passes through, regardless of attacker type/tower)
- Unslowed enemies unaffected even when owned — Done
- Reverts after slow expiry — Done
- Scales armor damage only; HP damage unchanged — Done
- `[FrozenFracture]` debug log per boosted application (enemy id, level, bonus percent) — Done

## Commands and results
- `["godot", "--version"]` — exit 0; 4.4.1.stable
- `["godot", "--headless", "--path", ".", "--editor", "--quit-after", "120"]` (typecheck/build + fresh import) — exit 0; only pre-existing errors (missing icon imports on first scan, `res://debug_enemy_parsing.gd` parse error — pre-existing, untouched)
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/frozen_fracture_slowed_vs_unslowed.json"]` — exit 0; `[Harness] status=pass exit=0`; log shows `[FrozenFracture] boosted enemy=Orc Enemy_boss level=1 bonus=10% armor_dmg=22.0`
- `["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/frozen_fracture_levels_and_expiry.json"]` — exit 0; status=pass; ladder logs armor_dmg=22.0 / 24.0 / 26.0 for L1/L2/L3, expiry leg back to 20.0 (unboosted)
- `enemy_armor_ballista` harness — status=pass (armor regression unaffected)
- `progression_pick` harness — status=pass (after the scenario fix above; failed once before it: venom_miasma_bloom actual 0)
- `ice_rate_matched_speeds` harness — status=pass
- Note: the plan's full-test bash one-liner is not runnable through the runner profile (`bash` is not allowlisted; only godot/python3/git/pwsh). Each listed scenario was run individually through `run_project_cmd` with the identical godot invocation and its `.gen/harness/<id>/result.json` status verified as pass — same coverage, same assertions.

## Notes
- The boost lives in `EnemyHealthController._consume_armor`, so it applies to every damage source that carries an armor-damage component (any tower, traps, scripted hits), gated purely on the enemy's current `slow_time_left > 0` — slow-source attribution in IceTower was not needed.
- The `[FrozenFracture]` boost line prints only when the boost actually changed the value (perk enabled + slowed + armor_dmg > 0), so it doubles as the filterable marker for the game-test log diagnosis.
- `apply_progression` selections live on the ProgressionManager autoload and survive `load_map` (only StatsManager is cleared), which both new scenarios rely on.
- map_7 wave 6 spawns exactly one enemy (armored Orc Enemy King, armor 60, hp 1625), so the harness index-0 enemy is unambiguous and each reload restores armor to 60.
- Gotcha for future perk additions: any new chest-eligible Common perk must be exhausted with apply_progression grants in `progression_pick.json` or its deterministic 2-of-2 chest draw breaks (observed: venom_miasma_bloom actual 0).
- Pre-existing, out of scope: `res://debug_enemy_parsing.gd` parse error (`get_process_frame()` not found) surfaces in every editor scan.
\n