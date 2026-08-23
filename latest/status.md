## ✅ Done
- Debug-build `[TORCH]` log line appears per torch recompute event, naming the trigger (initial placement vs incremental carve) and the number of torches placed.
- After carving a 2-by-18 plus 18-by-2 cross underground, every sampled point along all four arms at roughly 2-unit intervals has at least one active torch within its light coverage radius.
- When a new corridor is carved that connects to an already-lit carved path, torches appear along the new corridor's entire length, not only near the junction.
- Every carved cell reachable in the connected carved network lies within coverage of at least one placed torch (no unlit carved cells reported by the torch state source).
- A pending (unconfirmed) dangerous cave interior contains zero torches even when it overlaps already-carved, otherwise-lit path cells.
- After a dangerous cave is declined and sealed, its interior contains zero active torches, including any cells that overlap previously carved path.
- The headless `cave_carved_path_torches` scenario passes with count_near expectations sampled along the full length of all four cross arms (~every 2 units), not only inside the cave room.
- When `cave_pending_seals_entrance_instantly.json` runs, no cave other than the scenario's own fixture (id 9003) is discovered, carved, locked, or sealed before the scenario's first route assertion completes, regardless of RNG rolls (harness suppression of RNG cave discovery is honored for every carve event).
- The scenario's initial hole-to-exit route assertion (`has_route_from == true`) succeeds deterministically on repeated runs from the same fixed scenario seed.
- At the moment the dangerous-cave confirmation question is presented (before any player decision), `has_route_from` for the scenario's hole returns false while the cave is marked pending, proving the entrance is physically sealed rather than logically locked.
- One second after sealing, the pending dangerous cave's interior contains zero active torches.
- Confirming "yes" restores the exact hole-to-exit route (`has_route_from == true`) and the cave interior again reports valid lighting (nonzero torch coverage within the confirmed cave).
- The scenario keeps its original assertions unchanged: no expectation, wait_for_condition, or threshold may be removed or loosened by the determinism or lighting fixes.

## ⬜ Pending
- In a fresh windowed gl_compatibility (llvmpipe) top-down run of `cave_carved_path_torches.json`, each of the four carved cross arms shows visibly lit floor pixels along its entire length — no arm reads fully dark.
- Fresh windowed-run screenshot PNGs exist with current timestamps showing the full carved cross visibly lit end to end and declined caves dark, and their pixels have been inspected (manual tester).

## ❌ Impossible

