# Revision 1 — targeted visual evidence

## Prior classification
The checker classified the run `fixable`: focused behavior and headless/baseline/windowed commands passed, but the fresh PNG was an idle/debug Map 6 state and did not visibly prove map-B activity or transition.

## Fixed acceptance gate
Produce a fresh windowed screenshot at the map-reload boundary that visibly shows the reloaded map-B state with a newly created/firing tower (or an equivalent clearly active map-B scene), while still showing no stale Porter ring/beam/dissolve/effect from map A. Preserve the existing behavioral assertions and do not weaken the issue.

## Scope
Target only the scenario/checkpoint setup and minimal harness screenshot timing/action needed to capture map-B activity. Do not change production teardown code. Re-run focused headless and windowed checks after the revision.

## Stop condition
If the fresh windowed run still cannot produce a screenshot that proves map-B activity, retain the exact evidence and finish incomplete; do not claim visual pass from headless status or an idle image.
