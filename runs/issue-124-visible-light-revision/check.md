# Check report — issue-cave-carved-path-torches (iteration 4)

classification: pass

## Verdict

The iteration-3 determinism fix holds under fresh checker verification. All
build/test/harness gates pass through `run_project_cmd`; the previously failing
`cave_pending_seals_entrance_instantly.json` scenario now passes deterministically
(3 consecutive runs, exit 0, `status=pass`, 14/14 actions ok), with RNG cave
discovery suppressed on every carve event (`[CAVE] discovery suppressed: harness
scenario forbids RNG cave discovery`) and only fixture cave 9003 present.
12 of 14 criteria are Done. The remaining 2 are Pending by evidence ownership,
not failure: the windowed screenshot item belongs to the manual tester (headless
runs skip screenshots by design: `outcome: skipped, reason: headless`), and the
`[TORCH]` debug-log criterion is only partially implemented — the log prints the
torch count per recompute but does not name the trigger (initial placement vs
incremental carve).

## Commands executed (all via run_project_cmd, project=godot-td,
workspace=poke-defense-godot/issue-cave-carved-path-torches)

| Command | Exit | Result |
|---|---|---|
| `git status --short` | 0 | feature diff present (9 mod files + new scenario json) |
| `godot --headless --path . --editor --quit-after 300` | 0 | clean import, no script errors |
| `cave_pending_seals_entrance_instantly.json` run 1 | 0 | `status=pass`, 14/14 actions ok |
| `cave_pending_seals_entrance_instantly.json` run 2 | 0 | identical pass (determinism) |
| `cave_pending_seals_entrance_instantly.json` run 3 | 0 | identical pass (determinism) |
| `cave_carved_path_torches.json` | 0 | `status=pass`, all expectations pass |
| `declined_cave_torches_extinguish.json` | 0 | `status=pass` |

Fresh results: `.gen/harness/{scenario}/result.json` written this iteration.
Raw stdout scanned independently of harness status: no game-script
`SCRIPT ERROR`, no GDScript `Parse Error`, no `Invalid call`. The only
`Failed loading resource` / parse-error spam is the pre-existing HudTheme.tres
missing-texture noise (`wood_panel.png` etc.), identical on clean HEAD and
already recorded in quality-notes.md as legacy. Exit-time dummy-renderer leak
warnings are engine shutdown noise per plan.

## Criterion evidence

Cluster 1 (torch coverage): `cave_carved_path_torches` result.json shows all 20
`count_near >= 1` samples green at ±2..±8 units along both axes plus the exit
corridor at [1.5/3.5/5.5, -8], radius covering Torch.LIGHT_RADIUS; torch pool
expanded on demand (`[TorchManager] Expanded torch pool by 10`, active up to
154); `unlit_carved_in_cave == 0` passes.

Cluster 2 (dark pending/declined): pending cave `count_in_cave == 0` before
confirm; declined caves 9102/9103 (and 9101 in focused scenario) report zero
interior torches including carved-path overlap cells.

Cluster 3 (harness/evidence): focused scenario green with full-arm sampling
(verified expectation list in tests/scenarios/cave_carved_path_torches.json:
20 count_near points ~every 2 units). Screenshot sub-criterion remains with the
manual tester — no fresh windowed PNGs exist; headless runs cannot produce them.
`[TORCH]` logging exists but does not name the trigger type — Pending.

Cluster 4 (sealing regression determinism): three consecutive deterministic
passes; sequence logged: route true → cave pending + route false (physically
sealed) → 1 s dark (`[TORCH] cave-path update active=31`,
count_in_cave==0) → confirm yes → route restored (distance 8.0, 17 waypoints)
with lighting restored (`[TORCH] cave-path update active=50`). Scenario JSON
diff is exactly one added line (`"suppress_rng_cave_discovery": true`);
no assertions, waits, or thresholds changed (verified via git diff).
Suppression honored at every carve cooldown AND the save-time flush
(`CaveSystem.prepare_for_save` gated by `_rng_discovery_suppressed()`).

## Changed-file quality findings

Feature diff reviewed against `/opt/data/coding_rules.md`: no demoting
violations in new code. Suppression flag is opt-in, minimal, documented;
gap-fill coverage logic reuses existing helpers; harness fields are documented
in HarnessValues header comments. Advisory items already open in quality-notes.md
(duplicated XZ-distance helper x3, HudTheme legacy breakage) remain unresolved
and advisory-only; no new quality notes appended this iteration. Test overlap
check: `cave_pending_seals_entrance_instantly` assertions unchanged (no new
overlapping test); `cave_carved_path_torches` is the plan-mandated new scenario.

## Blockers

None infrastructural. Runner healthy throughout.

## Unverified / handoff

- Manual tester: fresh windowed top-down PNGs showing cross lit end-to-end and
  declined caves dark, with pixel inspection (cluster 3 screenshot criterion).
- Optional follow-up: extend `[TORCH]` log to include trigger kind
  (initial vs incremental carve) to fully satisfy that criterion.
