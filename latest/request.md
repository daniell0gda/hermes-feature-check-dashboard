# Request: porter-broad-sweep (issue #82)

Project: poke-defense-godot
Git workspace: /workspace/git-workspaces/poke-defense-godot/issue-porter-broad-sweep
Branch: issue/porter-broad-sweep
Request ID: r82-porter-broad-sweep

## Feature

Add a new Common perk `porter_broad_sweep` ("Broad Sweep"), levels L1–3, that requires
the `porter_mass_transit` perk ("Mass Transit", Unique porter mode) and increases Mass
Transit's sweep radius by +50% / +80% / +100% at levels 1/2/3.

Key constraint: this scales ONLY the sweep radius introduced with porter-mass-transit —
not Porter's normal targeting `range`. Pure stat modifier; no new visuals required.

Issue URL: https://github.com/daniell0gda/poke-defense-godot/issues/82

## Acceptance criteria (from "Done when")

1. New Common perk `porter_broad_sweep` exists with 3 levels (L1/L2/L3).
2. Its `needs` list contains `["porter_mass_transit"]`.
3. At L1/L2/L3 it multiplies Mass Transit's sweep radius by +50%/+80%/+100%
   (i.e. ×1.5 / ×1.8 / ×2.0 of the base sweep radius).
4. It does NOT change Porter's normal targeting range.
5. Follows existing perk registration patterns (see how porter_mass_transit and other
   follow-on perks like siege-breaker/static-breach are defined and offered).

## Runner notes (redo notes for workers)

- Use the project runner via `run_project_cmd`: project key `godot-td`,
  workspace `poke-defense-godot/issue-porter-broad-sweep`. Do NOT invent workspace names.
- Godot editor gate: `godot --headless --path . --editor --quit-after 300`
- Harness commands must pass the scene argument explicitly before user args.

## Manual testing

Manual testing: required if any player-facing surface changes (perk picker card,
perk description text). The sweep radius itself is invisible; teleport feedback is
already covered by Mass Transit. If only data/stat plumbing changed with no visible
UI beyond the existing perk picker listing, planner may set `manual_testing: none`
— but if the perk card appears in the picker UI, capture it.
