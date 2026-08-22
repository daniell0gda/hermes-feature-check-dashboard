# Team-leader report

- **Result:** failed
- **Classification:** **pass**
- **Feature:** perk-molten-shackles
- **Run:** issue-93-molten-shackles
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- `molten_shackles` is a Common Fire-progression perk with 3 levels; applying it three times reaches levels 1, 2, then 3, after which it is no longer eligible, and after `reset_for_new_game()` it is back at level 0 with its config inactive.
- At any `molten_shackles` level, when no Fire burn perk (`get_fire_burn_config()`) is active, the molten-shackles config reports inactive/zero effect.
- When a Fire burn perk is active together with `molten_shackles`, the exposed armor-strip config carries a flat per-tick base armor amount of 1 / 2 / 3 for shackles levels 1 / 2 / 3, scaled by the burn config's level multiplier.
- On an armored enemy with an active Fire burn perk and `molten_shackles` at level 1, burn ticks reduce the enemy's `armor` by the configured flat base amount scaled by the burn config's level multiplier, while the enemy stays alive.
- With a higher Fire burn perk level and the same `molten_shackles` level, each burn tick strips more armor than at the lower burn perk level.
- When no Fire burn perk is owned, burn ticks leave an armored enemy's `armor` unchanged (expected behaviour, covered by a dedicated game-test scenario).
- With a Fire burn perk owned but `molten_shackles` not taken, burn ticks leave an armored enemy's `armor` unchanged.
- Burn ticks under `molten_shackles` never reduce `armor` below 0, and do not change the HP damage dealt by those ticks compared with the same burn without the perk.
- Reusing existing burn visuals, no new VFX is introduced: burning an enemy with `molten_shackles` produces the same BurnStatus/BurnVFX presentation as burning without it.
- Debug-build [MOLTEN_SHACKLES] log line per burn tick that strips armor, naming the enemy, the armor before/after, and the applied strip amount.

## ⬜ Pending

## ❌ Impossible

## Check

# Check report — molten_shackles (issue #93)

Classification: **pass**

## Commands (all via run_project_cmd, project=godot-td, workspace=godot-td/issue-molten-shackles)

- Probe `godot --version` — exit 0 (Godot 4.4.1.stable)
- Typecheck/build `godot --headless --path . --editor --quit-after 300` — exit 0
- Focused `--harness=res://tests/scenarios/molten_shackles_progression.json` — exit 0, `[Harness] status=pass exit=0`; result: `.gen/harness/molten_shackles_progression/result.json`
- Focused `--harness=res://tests/scenarios/molten_shackles_armor_shred.json` — exit 0, `[Harness] status=pass exit=0`; result: `.gen/harness/molten_shackles_armor_shred/result.json`
- Full suite `--harness=res://tests/scenarios/smoke_tower_roster.json` — exit 0, `[Harness] status=pass exit=0`; result: `.gen/harness/smoke_tower_roster/result.json`

## Acceptance criteria evidence

1. Common Fire perk, 3 levels, L1→2→3 then ineligible, reset → level 0 inactive — PASS. `molten_shackles_progression` scenario (apply ×3, eligibility, reset_for_new_game) all ok; log shows `[FireProgression] molten_shackles L1/L2/L3 -> base_armor=1/2/3`.
2. No fire burn perk → config inactive/zero at every shackles level — PASS. Progression scenario arms with shackles-only report enabled=false, armor_per_tick=0.
3. With burn perk active: armor_per_tick = base 1/2/3 × burn level multiplier — PASS. Scenario asserts 1/2/3 at shackles L1/2/3 and scaling side (shackles L1 + Ember Coat L3 → 3).
4. Shackles L1 + burn L1: armored Orc Enemy King (armor 60) stripped 1/tick over 4 ticks → armor 56, enemy alive — PASS. Armor-shred arm 4; log shows `[MOLTEN_SHACKLES] strip enemy=Orc Enemy_boss armor=...->... amount=1.0` per tick.
5. Higher burn level, same shackles level strips more — PASS. Arm 5 (burn L3, shackles L1): 3/tick, armor 60→48.
6. No burn perk owned → armor unchanged — PASS. Arm 1: armor stays 60.
7. Burn owned, no shackles → armor unchanged — PASS. Arm 3: armor stays 60.
8. Armor never below 0; HP damage identical with/without perk — PASS. All arms end hp=1615 (identical); strip clamps `max(0.0, before - strip)` in `BurnStatus._apply_molten_shackles_armor_strip()`.
9. No new VFX — PASS. Diff touches no VFX files; strip reuses existing BurnStatus path.
10. Debug `[MOLTEN_SHACKLES]` log per stripping tick with enemy name, before/after, amount — PASS. Verified live in harness output, gated by `OS.is_debug_build()`.

## Changed-file quality (per /opt/data/coding_rules.md)

- `scripts/game/status/BurnStatus.gd` — clean; strip gated on `&"fire"`, no casts, minimal.
- `scripts/progression/managers/FireTowerProgressionManager.gd` — clean; reset() clears new state; multiplier `max(1, _burn_level)` matches plan.
- `autoload/ProgressionManager.gd` — passthrough matches existing pattern.
- `scripts/progression/fire_tower.json` — indentation of the inserted perk block is inconsistent with the surrounding file (2-space vs tab-mixed); JSON parses and all harnesses pass. Advisory only (quality-notes).
- New tests do not overlap existing coverage: no prior scenario asserted burn-tick armor stripping (checked `tests/scenarios/` — armor scenarios are ballista/trap/bar-visual only).

## Quality notes / blockers / unverified

- No blockers. Nothing unverified. Pre-existing HudTheme.tres texture-load warnings appear in all runs including baseline smoke — not introduced by this change.
