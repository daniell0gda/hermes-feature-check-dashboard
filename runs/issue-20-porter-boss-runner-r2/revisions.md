# Revision: miss chance + 3 levels + windowed shots

First run check passed the original 1-level always-reroute perk. Manual-tester then failed (no windowed PNGs). Mid-run product changes:

- 70% miss at L1, -5% per extra level (L2 65%, L3 60%), values in `Balance.gd`
- Unique perk now `maxLevels` 3
- Harness must force hit/miss rolls

Continuation must re-plan remaining criteria, re-code if needed, re-check focused harness, and produce windowed manual-tester PNGs (never `--headless`).
