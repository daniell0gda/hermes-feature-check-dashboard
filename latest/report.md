# Team-leader report

- **Result:** failed
- **Classification:** blocked
- **Feature:** underground-carve-topdown-camera-rotation
- **Run:** issue130-underground-carve-topdown-camera-rotation
- **Lifecycle:** dashboard publish only; project commit/push/close not implied

## Status

## ✅ Done
- (none — all criteria moved to Pending)

## ⬜ Pending
- When carve mode is activated while on the underground layer, the active camera's rotation becomes a top-down bird's-eye view (camera forward pointing straight down at the underground board) without changing the camera's position or zoom.
- Activating carve mode changes only the camera's rotation: the camera's position and its distance/zoom relative to the view target are exactly what they were immediately before activation.
- Canceling carve mode (ESC, right-click cancel path, or any existing cancel route that ends carve mode) restores the camera rotation that was active immediately before carve mode was entered, when the player did not rotate the camera manually during carving.
- If the player manually rotated the camera while carve mode was active, canceling carve mode leaves the camera at the player's current angle instead of restoring the pre-carve angle.
- While carve mode is active on the underground layer, the player's normal camera rotation input (right-mouse drag or shift+left drag) still rotates the camera.
- Entering dig-hole, place-exit, place-block, or tower-selection modes does not rotate the camera to the top-down angle; only carve mode triggers the rotation.
- Debug-build `[CARVE_CAMERA]` log line per rotation event: one when the top-down angle is applied (with the pre-carve angles captured) and one when a cancel restores or deliberately skips restoring them (with which of the two happened).
- A harness value source exposes the active camera's rotation basis (and position/zoom-equivalent) so scenarios can compare camera orientation before, during, and after carve mode.
- The focused scenario asserts, under the harness: top-down orientation after entering carve mode on the underground layer, unchanged position/zoom across the transition, exact restoration after plain cancel, and retained player angle after a scripted manual rotation followed by cancel.

## ❌ Impossible
- (none)

## Check

# Check report — underground-carve-topdown-camera-rotation (iteration 1)

classification: blocked

## Verdict

Blocked by runner infrastructure, not by the project alone. `run_project_cmd` was
used (never the host shell) for every attempted verification command, and every
invocation failed at container start:

- `{"project":"godot-td","workspace":"godot-td/issue-underground-carve-topdown-camera-rotation","cmd":["git","status","--short"]}` → HTTP 422, exit 126:
  `OCI runtime exec failed: exec failed: unable to start container process: chdir to cwd ("/workspaces/godot-td/issue-underground-carve-topdown-camera-rotation") set in config.json failed: no such file or directory`
- `godot --version` @ `godot-td/issue-130` → same 422 chdir failure.
- `git status --short` @ `poke-defense-godot/check` → same 422 chdir failure.
- Host: `/workspaces` does not exist; `docker ps` → `Cannot connect to the Docker daemon at unix:///var/run/docker.sock`.

The runner's pre-provisioned workspace directory is missing and Hermes has no
Docker socket access to create it; the project-runner skill prohibits
bootstrapping the workspace through the host shell. Therefore the typecheck/build
gate and the full test gate could not be executed and are treated as failed.

## Gate results

| Gate | Command | Result |
|---|---|---|
| Typecheck/build | `["godot","--headless","--editor","--path",".","--quit-after","3"]` | NOT RUN — runner 422 chdir failure (infra) |
| Focused test | `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/carve_camera_topdown.json"]` | NOT RUN this session — runner 422 chdir failure (infra) |
| Full test loop | plan.md full-test command | NOT RUN — runner 422 chdir failure (infra) |

## Evidence from the implementor's own stored run

`.gen/harness/carve_camera_topdown/result.json` (written 2026-08-22T12:18:12,
before the runner broke) records `"status": "fail"`:

- 6 timeline actions failed with `ui has no method '_on_dig_hole'`,
  `_clear_dig_mode`, `_on_carve` (×2), `clear_carve_mode` (×2).
- Expectation `dig_hole_camera_top_down` FAILED (actual true, expected false —
  the dig-hole probe was taken without dig mode ever being entered, so the check
  is both failing and vacuous).
- The three `carve_camera_*` expectations "passed" only vacuously: every probe
  captured the identical untouched camera basis because carve mode was never
  actually entered. They assert nothing about the feature.
- All three `[CARVE_CAMERA]` log regex expectations failed (`actual: ""`).

## Source inspection of the diff (`git diff HEAD`, 6 files, +184)

- `scripts/ui/UI.gd`: `carving_active` setter now calls
  `game.on_carve_camera_mode(armed)` — **no such method exists anywhere**
  (`grep -rn on_carve_camera_mode scripts/` matches only the call site). At
  runtime this is guarded by `has_method`, so it silently does nothing.
- `scripts/game/Game.gd`: contains NO carve-camera logic and NO `[CARVE_CAMERA]`
  logging. The only addition is `debug_look_down_underground()` (orthogonal
  camera teleport for screenshots) which implements none of the acceptance
  criteria and looks like manual-test scaffolding, possibly scope creep.
- No code anywhere rotates the camera to top-down on carve arm, restores it on
  cancel, or tracks manual rotation during carve.
- `scripts/testing/HarnessValues.gd` / `HarnessActions.gd` / `AgentHarness.gd`:
  camera_probe / rotate_camera / camera value source are implemented and look
  reasonable, but they test a feature that does not exist.
- `tests/scenarios/carve_camera_topdown.json` references four UI methods that do
  not exist (`_on_dig_hole`, `_clear_dig_mode`, `_on_carve`, `clear_carve_mode`);
  grep finds none of them in `scripts/ui/UI.gd`.

## Acceptance criteria status (all unmet)

Every criterion below is Pending: the implementing logic is absent from the diff
and/or its scenario actions fail against real UI methods.

1. Carve on underground rotates camera top-down, position/zoom unchanged — Pending (no implementation; during_carve probe identical to before).
2. Only rotation changes across arm — Pending (vacuous pass only).
3. Plain cancel restores pre-carve rotation — Pending (no implementation).
4. Manual rotation during carve survives cancel — Pending (no implementation).
5. Camera rotation input still works while carving — Pending (untested; rotate_camera action ran outside carve mode).
6. Dig-hole/place-exit/place-block/tower-selection do not trigger rotation — Pending (scenario calls nonexistent UI methods; expectation fails).
7. `[CARVE_CAMERA]` debug log lines — Pending (absent from source; log assertions fail).
8. Harness value source exposes camera orientation/placement — implemented (camera source + probes present in HarnessValues/HarnessActions) but unverifiable this session; kept Pending pending a runnable gate.
9. Focused scenario asserts top-down / position-unchanged / restore / kept-player-angle — Pending (scenario currently fails: 4 bad action targets, 3 failed log checks, 1 failed expectation).

## Manual testing

Required by plan (`manual_testing: required`) and request notes. No windowed
screenshot evidence found under `.gen/harness/carve_camera_topdown`
(`"screenshots": []`). Not performed.

## Blockers

1. Runner infrastructure unavailable: `run_project_cmd` 422 chdir failures for
   every workspace slug; `/workspaces` missing; Docker socket unreachable from
   Hermes. Re-provision the runner workspace (host/root side) and re-run check.
2. Implementation incomplete: `Game.on_carve_camera_mode` and all
   `[CARVE_CAMERA]` logging missing; scenario targets four nonexistent UI
   methods. Coder must implement cluster 1 and fix the scenario action names.

## Quality notes

See `.gen/quality-notes.md` (appended: silent no-op notification pattern;
vacuous harness passes masking a missing feature; suspected scope creep in
`debug_look_down_underground`). Advisory only.
