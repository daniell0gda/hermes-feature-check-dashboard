## ✅ Done
(none — full test suite not green, so all items held in Pending)

## ⬜ Pending
- With no perk owned, `ProgressionManager.get_bounty_config()` is unchanged from today's shape and carries no grave-robber bonus.
- Owning `traps_grave_robber` at L1/L2/L3 exposes a bonus-gold configuration through the existing bounty path (`get_bounty_config()`) equivalent to +10%/+15%/+25% of the enemy's base reward on qualifying kills.
- Re-applying a level or replaying levels 1..N of `traps_grave_robber` (save/load) sets the bonus to that level's exact percentage rather than compounding or collapsing.
- Resetting progression (new run / `reset`) clears the grave-robber bonus so no bonus applies afterwards.
- Debug-build `[EconomyProgression]` log line per grave-robber level change naming the perk name, new level, and resulting bonus percentage.
- When an enemy dies to a trap-sourced killing blow while underground and `traps_grave_robber` L1 is owned, the gold awarded for that kill equals base reward plus 10% of base reward (exact integer gold delta asserted).
- At L2 and L3 the same setup awards exactly +15% and +25% of base reward respectively over the base reward.
- A trap killing blow on an above-ground (surface) enemy awards exactly the base reward — no bonus.
- An underground enemy killed by a non-trap source (tower/projectile) while the perk is owned awards exactly what the existing bounty rules give — no grave-robber bonus.
- The bonus rides the existing bounty/economy payout path: with the perk owned, an ordinary qualifying kill's total gold still reflects any concurrently-owned `gold_on_kill`/`curse_blood_money` amounts unchanged (no cross-perk interference either direction).
- Debug-build `[GRAVE_ROBBER]` log line per qualifying bonus payout naming the enemy id and the bonus gold amount.
- Focused harness scenario `traps_grave_robber_progression.json` runs headless to `[Harness] status=pass` with exit code 0, asserting inline (wait_for_condition) each of: L1/L2/L3 bonus config values, exact gold delta on underground trap kill, zero delta on surface trap kill, and zero delta on non-trap underground kill.

Note: every criterion above has fresh passing focused-harness evidence
(`.gen/harness/traps_grave_robber_progression/result.json`, status=pass,
117/117 actions ok) and clean changed-file quality; they are held in Pending
solely because the build/test gate fails: `progression_chest_pool` and
`progression_pick` regress deterministically (new Common perk joins the seeded
chest-pick pool and shifts seeded draws), and the 177-scenario full-suite
invocation exceeds the runner's 420 s tool cap (timeout on two attempts).
Restore suite green, then re-promote.

## ❌ Impossible
(none)
