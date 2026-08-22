classification: fixable
next_role: code
reason: stated classification
revision: 2
budget_remaining: 0
The focused scenario now passes (all 3 expectations green, headless + windowed). Remaining gap is criterion 3 only: the windowed screenshots were captured but the "+20 coins!" popup is not visibly discernible in them — the details panel occludes the tower at capture time. Make the popup unambiguously visible in the windowed shot (e.g. take the screenshot before `on_tower_selected` reopens the panel, or deselect/hide the panel while the popup is alive), recapture, then wait for check.
