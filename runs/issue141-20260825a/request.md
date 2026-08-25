# Feature: harness press_button reaches controls inside an embedded subwindow

Issue: https://github.com/daniell0gda/poke-defense-godot/issues/141
Project key for runner: `godot-td`
Runner workspace: `godot-td/issue-press-button-subwindow`
Branch: `issue/press-button-subwindow`
Request ID: issue141-20260825a

## Problem

`HarnessActions._press_button` reports `landed=true` but the Button never fires when the target lives
inside a `Window` node (an embedded subwindow), so no scenario can click anything in
`ProgressionModal` or `RewardsModal`.

Measured evidence from `tests/scenarios/reward_panels_visual.json`:
- `press_button` on `AcceptBtn2` (PerkCard Accept button, tree paused) logged `landed=true` but no
  `[PROGRESSION_MODAL] close path=accept:*` line followed; `modal.count` stayed 1.
- `press_button` on `UI/Root/RewardsModal/Center/Panel/CloseChip` (tree not paused) logged
  `landed=true`, produced no `[REWARDS_MODAL] close trigger=corner_close`.
- `hud_controls_state` presses `UpgradeBtn` in the root viewport and passes — the action itself works;
  it is specifically the subwindow case that fails, pause is not the cause.

`landed=true` only asserts `not disabled and is_visible_in_tree()`. `_deliver_click` pushes into
`button.get_viewport()` (the `Window`) with `push_input(event, true)` and
`event.position = button.get_global_rect().get_center()`; something in that path drops the event.

## Done when

1. `press_button` delivers a real press to a Button inside an embedded subwindow, or `landed`
   reports false with a reason when it cannot.
2. `reward_panels_visual.json` drives its Unique pick with `press_button` instead of
   `progression_modal verb=choose_option`, and asserts the resulting
   `[PROGRESSION_MODAL] close path=accept:upgrade` log line.
3. `landed=true` means the press was actually delivered, or the field is renamed/documented so it
   cannot be read as success.
4. `hud_controls_state` still passes.

## Redo notes

- Workers must use runner project `godot-td` and workspace `godot-td/issue-press-button-subwindow`.
- Godot windowed evidence: use `--rendering-method gl_compatibility --audio-driver Dummy` if Vulkan
  fails; never headless-only for visual claims.
- Manual testing gate applies per team-work skill.
