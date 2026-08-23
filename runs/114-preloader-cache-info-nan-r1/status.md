## ✅ Done
- Every category entry in get_cache_info()["categories"] has total 0, cached 0, and finite percentage 0.0 for SOUNDS and UI_TEXTURES.
- A headless run printing get_cache_info() over COMMON_ASSETS emits only finite percentages for every category (no NaN/inf anywhere in the output).
- Non-empty categories keep their existing semantics: percentage equals cached/total*100.0 with total unchanged.
- Debug-build [AssetPreloader] log line per empty-category percentage substitution, naming the category name.

## ✅ Done
- get_cache_info() reports percentage 0.0 for an empty asset category instead of NaN.

## ❌ Impossible
