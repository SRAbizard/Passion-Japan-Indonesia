<script>
/**
 * Force sidebar nav groups into a clean state on EVERY admin/student
 * page load. The state expected by the user is:
 *
 *   - Dashboard (no active item)           → all groups collapsed
 *   - Deep page (e.g. /admin/courses/...)  → the group containing
 *                                            the active item is open,
 *                                            every other group closed
 *
 * Earlier versions of this file tried to honour Filament's own
 * localStorage persistence (collapsedGroups), which led to surprises
 * like "I expanded Recruitment two days ago and now Dashboard still
 * shows it open." We deliberately ignore the cached state now.
 *
 * Runs at BODY_START, polls until the sidebar DOM appears, then writes
 * the desired state into both localStorage AND the Alpine store so the
 * paint shows it correctly on the first frame.
 */
(function () {
    const STATE_KEY = 'collapsedGroups';

    let tries = 0;
    const interval = setInterval(() => {
        if (++tries > 40) { clearInterval(interval); return; } // ~4s

        const groups = document.querySelectorAll('.fi-sidebar-group[data-group-label]');
        if (groups.length === 0) return;
        clearInterval(interval);

        const labelsToCollapse = [];
        groups.forEach(g => {
            const label = g.dataset.groupLabel;
            if (! label) return;
            // Keep the group containing the active page expanded.
            const hasActive = g.querySelector('.fi-sidebar-item.fi-active, .fi-active');
            if (hasActive) return;
            labelsToCollapse.push(label);
        });

        try {
            localStorage.setItem(STATE_KEY, JSON.stringify(labelsToCollapse));
        } catch (e) { /* localStorage unavailable in some browser modes */ }

        const store = window.Alpine?.store?.('sidebar');
        if (store && typeof store.toggleCollapsedGroup === 'function') {
            // Bring the runtime store into sync with what we just wrote.
            labelsToCollapse.forEach(label => {
                if (! store.groupIsCollapsed(label)) {
                    store.toggleCollapsedGroup(label);
                }
            });
            // Conversely, make sure the active group is *expanded*.
            groups.forEach(g => {
                const label = g.dataset.groupLabel;
                if (! label) return;
                const hasActive = g.querySelector('.fi-sidebar-item.fi-active, .fi-active');
                if (hasActive && store.groupIsCollapsed(label)) {
                    store.toggleCollapsedGroup(label);
                }
            });
        } else {
            // Fallback: add the CSS class directly to avoid a flash of
            // expanded groups while Alpine hasn't booted yet.
            groups.forEach(g => {
                if (labelsToCollapse.includes(g.dataset.groupLabel)) {
                    g.classList.add('fi-collapsed');
                }
            });
        }
    }, 100);
})();
</script>
