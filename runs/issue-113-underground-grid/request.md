# Request: underground grid sized from map dimensions (#113)

Implement GitHub issue https://github.com/daniell0gda/poke-defense-godot/issues/113

## Goal

Size the underground voxel grid from the loaded map's `geometry.dimensions` so any map is diggable across its whole surface. Shipped 20x20 maps must keep exactly the grid they have today (40x40 cells of 0.5). Floor plane and boundary walls must follow the same dimensions. Remove `Game.setup_as_menu_backdrop`'s local grid override once the general path does this. A carve that falls outside the grid must say so instead of partially succeeding in silence.

## Acceptance

- Grid sized from map `geometry.dimensions`; 20x20 maps remain 40x40.
- Floor plane and boundary walls match those dimensions.
- `Game.setup_as_menu_backdrop` local override removed.
- Out-of-grid carve reports instead of silent partial success.
- Verified on `custom_map` with a windowed run: a tunnel dug near the map edge appears.

## Constraints

- Project commands only via runner `godot-td` workspace `poke-defense-godot/issue-underground-grid-fixed-at-20-units`.
- Visible underground/map work needs windowed screenshots, not headless-only.
- Do not commit, push, merge, or close the issue.
