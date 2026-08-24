# Cluster 3: windowed-manual-proof

- owned files: `.gen/ui_scenario.md`
- dependencies: 2
- parallel: false

## Acceptance criteria

- Manual windowed run (no --headless): underground screenshots of a curved side-to-side carve show every torch hugging a wall face, with no stick floating mid-corridor or hovering in front of a wall; ui_feels_broken: no.

## Verification commands

- Focused: manual windowed run of `res://tests/scenarios/manual_carve_curve_torches_visible.json` via the approved project runner (windowed, no --headless)
- Full test: same manual run plus review of captured screenshots against this cluster's criterion
- Typecheck/build: not applicable (manual visual proof; no code changes owned by this cluster)
