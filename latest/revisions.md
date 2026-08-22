classification: fixable
next_role: code
reason: stated classification — focused scenario fails at the trap-trigger step, scenario-side fix
revision: 3
budget_remaining: 1
Fix tests/scenarios/traps_buried_ordnance_progression.json so the trap actually hits an underground enemy (place trap at the exact enemy fixture position or force an enemy into trigger_radius 0.3), then rerun the focused harness and wait for check.
