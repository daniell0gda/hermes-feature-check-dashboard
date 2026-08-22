## ✅ Done
- After loading any stock map (e.g. map_6), the loaded cave room configuration equals the values written in the map file: minSpacing 2.5 stays 2.5, minRadius 1.5 stays 1.5, maxRadius stays its written value — no truncation to whole numbers.
- With map_6 loaded, the effective cave radius range after applying radiusScale covers the intended 0.9–1.8 band (a discovered cave's radius can be below 1.0, which the truncated integer config could never produce).
- Changing `minRadius` in a map file from 1.5 to 1.9 changes the loaded minimum-radius value accordingly (editing a fractional value is no longer a no-op).
- `CaveUtils.validate_cave_config` preserves fractional positive values for `min_radius`, `max_radius`, and `min_spacing` instead of rounding them to integers, while still clamping negative values to non-negative and keeping count keys (`max_caves`, `cooldown_tiles`) integral.
- The startup cave-config dump prints the fractional values as configured (e.g. `Min spacing: 2.5` for map_6), so the log can never again show a silently rounded value.
- No stock map JSON under `scripts/config/maps/` contains a `caves.connectors` block anymore.
- Saving/exporting a map from the map creator produces a caves configuration without a `connectors` key (neither the default underground config nor the processed map config emits it).
- All existing cave gameplay scenarios still pass unchanged after the `connectors` removal (removal is data-only, no behavioral drift in discovery, spacing, or spawning).
- A focused headless cave scenario loads a map whose file declares fractional room values and asserts, through harness-readable state, that each loaded value (minRadius, maxRadius, minSpacing) equals the exact fractional number in the map file; the scenario finishes with `status: pass`, exit code 0, and every expectation green.
- The same scenario's raw engine stdout contains no `Parse Error` lines and no new resource-load failures attributable to the changed files (baseline icon-import noise excluded).

## ⬜ Pending

## ❌ Impossible
