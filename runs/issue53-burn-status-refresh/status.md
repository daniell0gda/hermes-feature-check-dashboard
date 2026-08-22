## ✅ Done
- Refreshing the burn on an already-burning enemy preserves the accumulated fractional damage carry instead of zeroing it, so the next integer damage tick accounts for it.
- Re-applying burn to an already-burning enemy never delivers less total burn damage than letting the original application run to expiry unrefreshed, under identical timing and payload.
- A fresh burn applied to a previously unburned enemy still starts with no carried-over fractional damage and delivers exactly its configured per-tick schedule.
- Debug-build [BURN] log line per burn refresh naming the preserved pending fractional amount and the new tick schedule.
- A headless harness scenario applies two overlapping burn payloads to the same enemy through the same public apply entry point a tower uses and asserts total delivered burn damage is monotonically non-decreasing relative to a single-application baseline run of the same seed and timing.
- The same scenario asserts the refreshed burn still terminates after its refreshed duration and flushes any remaining fractional damage at expiry rather than dropping it.

## ⬜ Pending

## ❌ Impossible

classification: pass
