# Coder report: implementation (clusters 1+2+3)

## Changed files
- `scripts/game/CaveSystem.gd` — CaveConfig.min_radius/max_radius/min_spacing changed from `int` to `float`; `_apply_cave_config_from_map` reads room values with `float()` instead of `int()` (no more truncation).
- `scripts/utils/CaveUtils.gd` — `generate_cave_radius(min_radius: float, max_radius: float)`; `find_suitable_cave_position(min_spacing: float)`; `validate_cave_config` now treats `min_radius`/`max_radius`/`min_spacing` as positive floats while keeping `max_caves`/`cooldown_tiles` integral.
- `scripts/config/Balance.gd` — default_config radius keys written as floats (1.0/1.0/3.0) to match the new float contract.
- `scripts/config/maps/*.json` (all 13 incl. custom_map.json, main_menu_map.json, Untitled-2.json) — removed the dead `caves.connectors` block (data-only).
- `scripts/game/MapCreatorDataManager.gd` — default underground config no longer emits `connectors`.
- `scripts/game/MapCreatorConfigProcessor.gd` — processed map config no longer emits `connectors`.
- `scripts/testing/HarnessValues.gd` — `cave` value source exposes new fields `min_radius`, `max_radius`, `min_spacing` from the live cave_config.
- `tests/scenarios/cave_room_config_fidelity.json` — NEW focused scenario: loads map_6 and asserts minRadius 1.5 / maxRadius 3.0 / minSpacing 2.5 survive loading exactly, plus log expectations for the startup dump.

## Criteria
All 10 plan criteria implemented. Focused + 7 regression cave scenarios pass.

## Commands and results
- Editor/import gate `godot --headless --editor --path . --quit-after 60` — exit 0 (~9s warm), no new parse errors.
- Focused test `--harness=res://tests/scenarios/cave_room_config_fidelity.json` — exit 0, `.gen/harness/cave_room_config_fidelity/result.json`: status=pass, all 6 expectations green (actuals 1.5/3.0/2.5). Startup log shows `Min radius: 1.5`, `Max radius: 3.0`, `Min spacing: 2.5`, `Radius scale: 0.6`.
- Full cave suite (8 scenarios run individually via run_project_cmd; profile rejects bash): carve_stops_at_discovered_cave, cave_decline_seals_reveal_unseals, cave_discovery_chance, cave_discovery_pending_placement, cave_pending_seals_entrance_instantly, cave_reveal_only_unseals_carved_blocks, declined_cave_torches_extinguish, cave_room_config_fidelity — all exit 0, result.json status=pass, 0 failing expectations.
- `cave_discovery_long_carve` fails on BOTH baseline (carved_tiles 961 vs >=1000) and with changes (975) — pre-existing flaky scenario, not caused by this work (verified via git stash A/B).

## Notes
- Effective radius band check: map_6 minRadius 1.5 * radiusScale 0.6 = 0.9 < 1.0; live runs confirm discovered caves with radius ~0.97 (below 1.0, impossible under int truncation).
- Baseline noise in harness stdout (HudTheme.tres UID warnings, headless teardown leak errors) is unchanged from baseline; no Parse Errors attributable to changed files.
- `git status` shows only the intended files modified plus the new scenario file.
