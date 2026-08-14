# C3 verification — issue #63

## Scope and lifecycle

- Worktree: `/workspace/git-workspaces/godot-td/issue-63`
- Approved runner coordinates: `project=godot-td`, `workspace=godot-td/issue-63`
- No production or test source files were modified.
- No dashboard events were published.
- Worker was released after the final project command: `release_project_worker(..., remove=true)` returned `success=true`, `status=removed`, `removed=true`.

## Exact runner commands and returned results

All commands below were sent as tokenized `run_project_cmd` arrays; no shell, Docker, direct-host Godot, or PowerShell wrapper was used.

1. Preflight:
   ```text
   ["godot","--version"]
   ```
   Exit `0`, `timedOut=false`; returned `4.4.1.stable.official.49a5bc7b6`.

2. Fresh editor/import gate:
   ```text
   ["godot","--headless","--path",".","--editor","--quit-after","300"]
   ```
   Exit `0`, `timedOut=false`, duration `9223ms`.

3. Focused headless scenario:
   ```text
   ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json"]
   ```
   Exit `0`, `timedOut=false`, duration `10218ms`. Runner output contained `[Harness] status=pass exit=0` and wrote the focused structured result. The subsequent windowed invocation reused the legacy mutable result path, so the final file at that path is the windowed run; the fresh headless assertions were captured in the returned runner output but no separate stdout/stderr file path was exposed by `run_project_cmd`.

4. Related baseline:
   ```text
   ["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/map_swap_leaks_underground_enemies.json"]
   ```
   Exit `0`, `timedOut=false`, duration `6208ms`; runner output contained `[Harness] status=pass exit=0`.

5. Windowed focused scenario:
   ```text
   ["godot","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/issue_63_clear_previous_map_tower_effects.json","--rendering-method","gl_compatibility","--audio-driver","Dummy"]
   ```
   Exit `0`, `timedOut=false`, duration `44875ms`; runner output contained `[Harness] status=pass exit=0`.

## Structured evidence

- Focused result path (legacy mutable path, final contents from windowed run):
  `/workspace/git-workspaces/godot-td/issue-63/.gen/harness/issue_63_clear_previous_map_tower_effects/result.json`
  - `status=pass`, `headless=false`, all listed expectations `pass`.
  - Timeline still records the required lifecycle: pre-reload `map_generation=1`, `porter_active=true`; post-reload `map_generation=2`, `porter_active=false`, Porter target/shot/launch/impact/damage all `0`; repeated post-reload checkpoint remains zero; map-B fire action has `target_fire=1`, `shot_fire=1`, `launch_fire=1`.
  - Screenshot record: `issue_63_map_reload_boundary`, `outcome=captured`, `static=false`, `1920x1080`.
- Baseline result path:
  `/workspace/git-workspaces/godot-td/issue-63/.gen/harness/map_swap_leaks_underground_enemies/result.json`
  - `status=pass`, `headless=true`, all expectations pass.
  - `post_swap_clean` records `map=map_1`, `enemies.underground=0`, `enemies.dissolving=0`, and `enemies.total=0`; `mapB_playable` reaches wave `2`.
- Fresh PNG:
  `/workspace/git-workspaces/godot-td/issue-63/.gen/harness/issue_63_clear_previous_map_tower_effects/shots/issue_63_map_reload_boundary.png`
  - Vision inspection found no stale Porter ring/beam/dissolve/projectile visible, but the pixels show a static Map 6/debug/editor state (`Wave 1/4`, no towers, no active wave), not a visibly demonstrated Map-B transition or active Map-B tower. Visual acceptance is therefore **unverified/incomplete**, not a pass.
- Raw runner evidence: the current `run_project_cmd` responses exposed one combined inline `output` field and did not return separate stdout/stderr paths. I preserved the exact commands, returned exit codes, durations, structured paths, and diagnostic findings here; I do not claim nonexistent separate raw-log paths.

## Independent diagnostics scan

The runner outputs were scanned independently for targeted engine diagnostics. No targeted `Parse Error`, `Failed to load script`, or changed-file resource-load failure was observed. Existing/non-targeted diagnostics were present:

- UI/resource warnings/errors: missing `Root/ButtonsContainer/TowerButtons/Tower1`, missing `EnemyHealthBar/.../IconBoss`, duplicate `pressed`/`layer_changed` signal connections, missing UID recreation, missing FBX texture references.
- Headless shutdown diagnostics: leaked renderer dummy RIDs/resources, leaked ObjectDB instances, and PagedAllocator/resource-in-use messages.
- Windowed startup diagnostics: Vulkan extension unavailable, fallback to OpenGL 3; V-Sync unsupported; ALSA device errors followed by dummy-audio fallback. The requested compatibility renderer did initialize as `OpenGL API 4.5 ... Compatibility ... llvmpipe`; audio fell back to dummy as expected.
- Windowed shutdown diagnostics: GLES3 RIDs/shaders/textures/buffers and ObjectDB/resource-in-use leak messages.

These diagnostics did not change the harness assertion result, but they prevent calling the editor/runtime streams clean. Shutdown/resource diagnostics are recorded separately from harness assertions and are not relabeled as feature assertion failures.

## Verdicts and blockers

- **Focused headless:** `pass` for behavioral assertions. Fresh runner exit `0`; returned harness status pass; pre/post Porter lifecycle and map-B action evidence are present. Overall stream is not clean because of known UI/resource and shutdown diagnostics.
- **Related baseline:** `pass`. Fresh runner exit `0`; structured baseline status pass; underground/dissolving/total enemy cleanup and map-B playability assertions pass. Same class of known diagnostics was present.
- **Windowed focused:** harness `pass`, renderer/audio setup reached the requested compatibility/dummy fallback, and a fresh PNG was captured. **Visual criterion: unverified/incomplete** because the inspected PNG shows Map 6 idle/debug state rather than a visible map-A → map-B transition with fresh Map-B tower activity.
- **Overall C3:** `incomplete` for the visual acceptance criterion; not an infrastructure-blocked run. The remaining gap is screenshot/state coverage, plus the runner's lack of separately exposed raw stdout/stderr paths and the legacy mutable focused `result.json` path overwriting the headless artifact.

## Unverified criteria

1. The PNG does not visibly prove Map-B activity or the transition; headless evidence cannot substitute for this visual requirement.
2. A separate immutable raw stdout and stderr capture for each current runner invocation was not exposed by the approved runner response; combined inline output was scanned and this limitation is recorded rather than fabricated away.
3. The focused headless structured result was overwritten by the required later windowed run because the harness uses a mutable per-scenario result path. The focused headless pass is independently supported by its fresh runner output and the resulting timeline, but the immutable artifact-retention requirement remains incomplete.
4. This C3 run did not exercise a separate real restart/Continue-equivalent lifecycle beyond the scenario's debug map reload path; no such criterion is claimed as verified.

No checker-side source or scenario changes were made.