# Request: Issue #114 — AssetPreloader.get_cache_info() returns NaN for empty asset categories

- **Project:** poke-defense-godot (runner key `godot-td`)
- **Workspace:** `poke-defense-godot/issue-preloader-cache-info-nan`
- **Branch:** `issue/preloader-cache-info-nan` (cut from origin/master @ d241462)
- **Issue:** https://github.com/daniell0gda/poke-defense-godot/issues/114
- **Request ID:** 114-preloader-cache-info-nan-r1

## Problem

`AssetPreloader.get_cache_info()` in `scripts/utils/AssetPreloader.gd` computes per-category
percentage as `float(cached_count) / float(asset_list.size()) * 100.0`.
`AssetType.SOUNDS` and `AssetType.UI_TEXTURES` are declared as empty placeholder arrays, so
`asset_list.size()` is 0 → NaN.

## Done when

1. `get_cache_info()` reports `0.0` for an empty category (or omits it) — never NaN.
2. A printed call over the real `COMMON_ASSETS` shows finite percentages for every category.

## Notes for workers

- Runner key is `godot-td`; workspace is exactly `poke-defense-godot/issue-preloader-cache-info-nan`.
- Manual testing: this is a small utility fix; still-captureable evidence = printed finite cache info.
  If manual_testing is set to `required`, a windowed/headless print of `get_cache_info()` output over
  COMMON_ASSETS with captured stdout is acceptable evidence; UI-sanity criterion does not apply
  (no UI change).
