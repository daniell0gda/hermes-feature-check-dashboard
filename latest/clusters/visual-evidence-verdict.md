# Cluster: visual-evidence-verdict

parallel: false
depends_on: [close-camera-scenario]

## Scope

- `.gen/screenshots/` — fresh PNGs and GIF copies from the windowed run (owned)
- `.gen/check.md` — NEW checker verdict file written this run (owned)

No production source files in this cluster.

## Dependencies

close-camera-scenario (the close top-down camera must land before shots can be judged)

## Work summary

Run the updated scenario windowed (never `--headless`; add `--rendering-method gl_compatibility --audio-driver Dummy` if Vulkan fails), capture fresh PNGs, export a 30fps GIF from the record_frames output, copy them into `.gen/screenshots/`, and write a NEW `.gen/check.md`. Judge only fresh shots; headless tags alone do not pass the visual criterion. Distant speck or empty-floor crop ⇒ `classification: fixable`.

## Acceptance criteria

- A fresh windowed (non-headless) capture of `frostbite_fangs_chilled_hit` shows the trap and at least one live underground enemy large enough in frame to judge body color; neither a distant speck nor a crop of empty floor qualifies.
- In that fresh PNG the chilled enemy's frost/ice tint is clearly distinguishable from a normal green Cactoro body.
- The explicit `record_frames` action produces consecutive engine frames suitable for a 30fps GIF of the chill applying, and fresh PNG/GIF copies are present under `.gen/screenshots/`.
- A NEW `.gen/check.md` written this run records the verdict from the fresh shots only; if the shots are still a distant speck or empty-floor crop the classification is `fixable`, and headless pass tags alone never satisfy the visual criterion.
- A manual windowed test answering `ui_feels_broken: yes` fails the manual test.

## Verification commands

Same three run_project_cmd commands as cluster 1 (`godot-td` / `poke-defense-godot/issue-traps-frostbite-fangs`). The visual verdict itself is judged by inspecting the fresh PNGs under `.gen/harness/traps_frostbite_fangs_progression/shots/` and their copies in `.gen/screenshots/`; manual testing is required and windowed only.
