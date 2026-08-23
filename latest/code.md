# Coder report: implementation\n\n# Coder report: implementation

## Changed files
- `scripts/progression/global.json` — mod: new Common global perk `warlords_doctrine` (maxLevels 3; L1 value 0.05 / armor_bonus 0.08; L2 0.09 / 0.12; L3 0.14 / 0.15), inserted directly after `tower_dmg`.
- `autoload/ProgressionManager.gd` — mod: `_warlords_damage_ratio` aggregate added additively inside `get_global_damage_multiplier()` (stacks with, never overrides, `tower_dmg`); dedicated `_apply_to_handler` branch with `[WARLORDS-DOCTRINE] applied L<n>` debug log; cleared in `reset_for_new_game()` and state-load; new public `get_warlords_armor_ratio()` returning the level's `armor_bonus` (0.0 when unowned/missing).
- `scripts/game/actors/Enemy.gd` — mod: in `setup()`, after innate `max_armor` and final `max_hp` (post boss multiplier), adds `max_hp * warlords_armor_ratio` bonus armor additively, with `[WARLORDS-DOCTRINE] spawn_bonus level=<n> granted_armor=<amount> on <id>` debug log per spawn.
- `tests/scenarios/warlords_doctrine.json` — new harness scenario (details below).

No changes needed: `scripts/game/SpawnerSystem.gd` (resolves innate armor only; perk bonus applied at Enemy spawn site), `scripts/ui/EnemyHealthBar.gd` (`_update_armor_bar()` already mirrors `enemy.armor`/`enemy.max_armor` generically and seeds/eases the ArmorRow on first appearance, so granted armor shows/animates for free), `scripts/progression/handlers/global/TowerDamage.gd` (untouched; doctrine ratio is tracked separately in ProgressionManager).

## Criteria
- Perk catalog definition (Common, exactly 3 levels, 5/9/14% dmg + 8/12/15% armor) — Done
- Multiplier exactly 1.05/1.09/1.14 at L1/L2/L3 — Done
- Additive stacking with `tower_dmg` (both owned → sum of ratios) — Done
- `reset_for_new_game` → multiplier exactly 1.0, armor ratio 0.0 — Done
- Unarmored enemy spawns armor > 0 / max_armor > 0 while active; zero without — Done
- Innate-armor enemy: bonus added on top of innate — Done
- Scripted armor hit strips granted armor via existing armor-soak; damage lands per normal rules — Done
- Armor bar row appears on previously-unarmored enemy (existing generic ArmorRow path) — Done
- `[WARLORDS-DOCTRINE]` debug logs per application and per spawn bonus, filterable — Done

## Commands and results (all via run_project_cmd, project poke-defense-godot)
- Focused: `godot --headless --path . res://scenes/Main.tscn -- --harness=res://tests/scenarios/warlords_doctrine.json` — exit 0; `[Harness] status=pass exit=0`.
- Full: same invocation for `enemy_armor_ballista`, `enemy_armor_trap`, `enemy_armor_bar_visual` — each exit 0, `status=pass`.
- Typecheck: `godot --headless --path . --editor --quit-after 300` — exit 0, no script/parse errors.
- Evidence in run output: `[WARLORDS-DOCTRINE] applied L1 tower_damage_bonus=0.05 total_multiplier=1.05`, `[WARLORDS-DOCTRINE] spawn_bonus level=1 granted_armor=1.76 on Mushnub` (8% of hp 22), `[PROGRESSION] apply tower_dmg L1 multiplier=1.10` proving additive stacking, `[Armor] Mushnub depleted: 1.76 armor removed by 5.0 armor damage`.
- result.json assertions pass: Mushnub spawns max_armor > 0 / armor > 0; scripted hit deals exactly 21 (20 base × 1.05); owning both perks → 1.10; reset → multiplier 1.0.

## Notes / gotchas for tester
- Enemy HP is int, so the end-to-end damage leg uses base 20 (×1.05 = 21 integer-exact) instead of 10 (would floor and prove nothing).
- The scripted hit carries armor_damage 5 so the 1.76 granted armor strips fully (5 > 1.76 avoids the ARMOR_DAMAGE_REDUCTION halving of the HP component); full 21 reaches HP.
- `warlords_doctrine` has `forceVisibility: false` like other non-flagged globals, so chest draws behave unchanged; the scenario drives picks via `apply_progression` directly.
- Bonus armor intentionally scales off post-boss-multiplier `max_hp`, matching "share of its max HP" for bosses too.
- There is no take/removal API for global perks in this codebase — removal is via `reset_for_new_game`, same as every other global perk.
- Manual/UI criterion remains for the manual tester (windowed run per `.gen/ui_scenario.md`).
\n