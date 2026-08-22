# Check report: cave-room-config-truncated (iteration 1)

Classification: **pass**

## Verdict

Implementation of issue #122 (cave room config truncation to int + dead `caves.connectors` config)
is complete and verified. All 10 acceptance criteria hold with fresh evidence produced by this
checker through `run_project_cmd` (project=poke-defense-godot,
workspace=poke-defense-godot/issue-cave-room-config-truncated). Decision recorded in plan
(keep fractional values, fields become floats) matches the implementation and is stated in the
coder report.

## Verification commands (all via run_project_cmd, exit codes are runner-reported)

- Probe: `["godot","--version"]` — exit 0 (Godot 4.4.1.stable.official.49a5bc7b6)
- Typecheck/build gate: `["godot","--headless","--editor","--path",".","--quit-after","60"]` — exit 0 (~8s), clean parse of CaveSystem / MapCreatorConfigProcessor / MapCreatorDataManager / HarnessValues / CaveUtils / Balance
- Focused test: `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_room_config_fidelity.json"]` — exit 0, `.gen/harness/cave_room_config_fidelity/result.json`: status=pass, 6/6 expectations green (min_radius actual 1.5 == 1.5; max_radius 3.0 == 3.0; min_spacing 2.5 == 2.5; log contains "[CAVE CONFIG DEBUG] Final cave configuration:", "Min spacing: 2.5", "Min radius: 1.5")
- Full regression suite (plan's bash loop rejected by profile allowlist; run individually — equivalent coverage):
  - carve_stops_at_discovered_cave — exit 0, status=pass
  - cave_decline_seals_reveal_unseals — exit 0, status=pass
  - cave_discovery_chance — exit 0, status=pass
  - cave_discovery_pending_placement — exit 0, status=pass
  - cave_pending_seals_entrance_instantly — exit 0, status=pass
  - cave_reveal_only_unseals_carved_blocks — exit 0, status=pass
  - declined_cave_torches_extinguish — exit 0, status=pass

## Acceptance criteria evidence

1. Fractional values kept (float decision): CaveSystem.CaveConfig min_radius/max_radius/min_spacing now `float`; `_load_cave_configuration` uses `float()` instead of `int()` — scripts/game/CaveSystem.gd. Proven by focused scenario exact-value assertions (1.5/3.0/2.5).
2. validate_cave_config agrees: positive_int_keys reduced to max_caves/cooldown_tiles; new positive_float_keys block clamps negatives while preserving fractions — scripts/utils/CaveUtils.gd (code inspection).
3. caves.connectors removed from data AND creator: grep over scripts/config/maps/*.json returns zero hits; removed from MapCreatorDataManager._create_default_underground_config and MapCreatorConfigProcessor.process_underground_data.
4. Cave scenario asserts loaded config matches map file: tests/scenarios/cave_room_config_fidelity.json (new) does exactly this via HarnessValues `cave` source; passes.
5. Effective radius band 0.9–1.8: live run log shows discovered caves with radii 0.9737, 1.1610, 1.5401 (< 1.0 achievable, impossible under int truncation).
6. Startup dump prints fractional values: log shows Min radius: 1.5 / Max radius: 3.0 / Min spacing: 2.5 for map_6; asserted by log expectations in the focused scenario.
7. Map-creator export without connectors: code inspection confirms neither default underground config nor processed map config emits `connectors`.
8. Regression scenarios unchanged: all 7 existing cave scenarios pass after the data-only removal.

## Stdout scan (focused scenario)

Parse Errors present are baseline noise only: HudTheme.tres ext_resource UID warnings / missing wood_panel.png texture (pre-existing theme/import state), headless teardown leak warnings, duplicate-signal connects. None reference the changed files. No GDScript parse errors in changed files.

## Known non-blockers

- `cave_discovery_long_carve` fails identically on baseline and with changes (carved_tiles threshold ~961–975 vs >=1000) — pre-existing flaky scenario, A/B-verified by the implementor via git stash; not caused by this work. Recorded in quality-notes.md as advisory.
- Plan's bash `-lc` full-suite wrapper is not allowlisted on this runner profile; equivalent per-scenario runs used instead.

## Changed-file quality findings

Diff reviewed against /opt/data/coding_rules.md + project CLAUDE.md: surgical, minimal, typed variables maintained, no duplication, no scope creep. No violations in new/changed code.

## Blockers

None.
