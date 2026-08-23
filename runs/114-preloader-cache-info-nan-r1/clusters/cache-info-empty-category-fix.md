# Cluster 1: cache-info-empty-category-fix

- Owned file scope: `scripts/utils/AssetPreloader.gd`, `tests/utils/test_asset_preloader_cache_info.gd`, `tests/utils/test_asset_preloader_cache_info.tscn`
- Dependencies: none
- Parallel: false (single cluster)

## Acceptance criteria

- get_cache_info() reports percentage 0.0 for an empty asset category instead of NaN.
- Every category entry in get_cache_info()["categories"] has total 0, cached 0, and finite percentage 0.0 for SOUNDS and UI_TEXTURES.
- A headless run printing get_cache_info() over COMMON_ASSETS emits only finite percentages for every category (no NaN/inf anywhere in the output).
- Non-empty categories keep their existing semantics: percentage equals cached/total*100.0 with total unchanged.
- Debug-build [AssetPreloader] log line per empty-category percentage substitution, naming the category name.

## Verification commands

- Focused test: ["godot", "--headless", "--path", ".", "res://tests/utils/test_asset_preloader_cache_info.tscn"]
- Full test: ["godot", "--headless", "--path", ".", "res://scenes/Main.tscn", "--", "--harness=res://tests/scenarios/smoke_placement.json"]
- Typecheck/build: ["godot", "--headless", "--path", ".", "--check-only", "--script", "scripts/utils/AssetPreloader.gd"]
