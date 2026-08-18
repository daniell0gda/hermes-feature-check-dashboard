# Feature Check Report — cave-discovery-chance-unreliable
Task: check
Workspace: godot-td/issue-cave-discovery-chance-unreliable
Classification: fixable

## Verification Commands (via run_project_cmd)
- `["godot","--version"]` → exitCode=0 (Godot 4.4.1)
- `["godot","--headless","--path",".","--editor","--quit-after","300"]` → exitCode=0 (import/typecheck passed)
- `["godot","--headless","--path",".","res://scenes/Main.tscn","--","--harness=res://tests/scenarios/cave_discovery_chance.json"]` → exitCode=1, status=timeout, expectation cave.count >=1 failed (actual=0), effective_discovery_chance check failed
- Full test harness (underground_diversion_proof) not re-run (unrelated scenario, prior result unrelated to cave criteria)

## Acceptance Criteria Evidence
All criteria remain Pending (per rules: any harness failure moves Done items to Pending; no criterion has passing automated test that would fail if broken).
- cave.count >=1 with discovery_chance=1.0: failed (timeout after carve, no cave created)
- chance=0.0 never discovers: unverified (focused scenario did not test 0.0 path)
- cooldown reset on failed placement: unverified (no evidence in harness result)
- decreasing chance after first cave: unverified
- README update: was marked Done by coder but demoted due to global gate failure
- Debug logs: code changes claim [CAVE] logs but harness output shows no matching debug lines in truncated runner result; no passing test asserts them

## Changed-file Quality Findings
- scripts/game/CaveSystem.gd, CaveUtils.gd: reviewed against /opt/data/coding_rules.md and CLAUDE.md; no new violations in the diff scope (typed GDScript, no casts). Legacy patterns noted only in quality-notes if needed.
- New scenario file present and loaded.
- No quality demotions applied to criteria (violations would be advisory only).

## Blockers / Unverified
- Harness timeout on chance=1.0 path: implementation does not yet satisfy "discovers on first eligible check". Fixable via code iteration.
- No runner/infra failure (probe and typecheck succeeded via runner).
- Missing evidence for most logging and dynamic-chance criteria.

Verdict: fixable (tests fail, incomplete verification evidence). No design_failure or blocked (runner reachable, workspace present).