<script>
/**
 * Force-collapse all sidebar nav groups on the user's first visit since
 * the config version changed. Bypasses Filament's PHP-side ->collapsed()
 * + localStorage seeding because the closure-based label matching in
 * AdminPanelProvider was unreliable across locales. Reads group labels
 * straight from the rendered DOM and writes them into the same
 * localStorage key Filament uses, so subsequent reloads behave normally.
 *
 * Bump CURRENT_VERSION whenever you want the reset to fire again.
 */
(function () {
    const CURRENT_VERSION = '2026-05-18-v3';
    const VERSION_KEY     = 'pj_nav_version';
    const STATE_KEY       = 'collapsedGroups';

    if (localStorage.getItem(VERSION_KEY) === CURRENT_VERSION) return;

    // The sidebar mounts after this script. Poll briefly until the
    // group elements appear, then collapse all of them. We leave any
    // group containing the active page untouched so users navigating
    // directly into a deep page still see context.
    let tries = 0;
    const interval = setInterval(() => {
        if (++tries > 40) { clearInterval(interval); return; } // give up after ~4s

        const groups = document.querySelectorAll('.fi-sidebar-group[data-group-label]');
        if (groups.length === 0) return;
        clearInterval(interval);

        const labelsToCollapse = [];
        groups.forEach(g => {
            const label = g.dataset.groupLabel;
            if (! label) return;
            // Skip the group containing the currently active item so it
            // stays expanded (Filament marks it with .fi-active).
            const hasActive = g.querySelector('.fi-sidebar-item.fi-active, .fi-active');
            if (hasActive) return;
            labelsToCollapse.push(label);
        });

        // Persist for Filament (it reads this on next boot).
        try {
            localStorage.setItem(STATE_KEY, JSON.stringify(labelsToCollapse));
            localStorage.setItem(VERSION_KEY, CURRENT_VERSION);
        } catch (e) { /* localStorage unavailable */ }

        // Apply visually now, this tick, via Alpine store.
        const store = window.Alpine?.store?.('sidebar');
        if (store && typeof store.toggleCollapsedGroup === 'function') {
            labelsToCollapse.forEach(label => {
                if (! store.groupIsCollapsed(label)) {
                    store.toggleCollapsedGroup(label);
                }
            });
        } else {
            // Alpine not ready: add the class directly so the user
            // doesn't see a flash of expanded groups.
            groups.forEach(g => {
                if (labelsToCollapse.includes(g.dataset.groupLabel)) {
                    g.classList.add('fi-collapsed');
                }
            });
        }
    }, 100);
})();
</script>
