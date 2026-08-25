classification: fixable
next_role: code
reason: stated classification
revision: 2
budget_remaining: 1

## Scope for revision-code-1 (retry after 1800s timeout)

Goal: complete the full-suite regression gate that iteration-1 could not
(`run_all_shard.py` OOMs / exceeds the 420s tool window as one call).

HARD BUDGET RULES (iteration-1 timed out by running scenarios one-by-one — 53
of 179 in 30 min; do NOT repeat that):

1. Do NOT invoke individual scenarios one at a time.
2. Run the suite in finer shards, each a single `run_project_cmd` call well
   under 420s: `python3 tests/run_all_shard.py <start> 8` style slices
   (or `0 8`, `8 8`, ... as the script's arg convention allows). Record each
   slice's exit status. If a slice OOMs or times out, split that slice finer;
   do not fall back to per-scenario runs except for a failing scenario you
   must confirm individually (max 3 such confirmations).
3. Known pre-existing failures are already documented in
   `.gen/quality-notes.md` (cannon_bunker_buster,
   fire_flashover_spread/fire_wildfire_spread_runtime,
   issue_35_timed_hazards_map_change, projectiles_10x_beam_cone,
   porter_boss_runner). For any NEW failing scenario not in that list, do a
   stash-based A/B (`git stash push -u`, rerun just that scenario, `git stash
   pop`) — max 3 A/B checks this iteration.
4. The feature diff itself is DONE and green (focused harness pass, editor
   gate pass). Make NO source changes unless an A/B proves a real regression.
5. Write `.gen/coder-reports/revision-1.md` EARLY and update it as you go, so
   a timeout still leaves durable evidence. Include: shards attempted, pass/
   fail counts, new-failure A/B verdicts, and whether the full suite completed.
6. Stop condition: every shard attempted OR budget exhausted → write report
   and return. Do not exceed ~25 minutes total.
