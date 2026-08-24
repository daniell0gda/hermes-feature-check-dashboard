classification: fixable
next_role: code
reason: stated classification
revision: 2
budget_remaining: 0
Redo the failed criteria, then wait for check.

## r4 — empty-model retry
Previous check workers sometimes returned 0 tokens. Latest on-disk check is real (`classification: fixable`, 2026-08-24 05:05). Restart leader with new request-id so check/manual-tester can finish remaining work (GIF + optional drag_spin vacuous fix). Do not re-implement the look_at guards.
