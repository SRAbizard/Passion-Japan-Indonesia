<script data-navigate-once>
/**
 * Sidebar nav behavior — two concerns:
 *
 *  1. Initial-state migration. Filament writes a JSON array to
 *     localStorage('collapsedGroups') ONCE on first visit; after that,
 *     it never re-runs the init. When we change the panel's default
 *     collapsed config (AdminPanelProvider::navigationGroups()), users
 *     with an existing entry are stuck with the old state. We bump
 *     `pj_nav_version` and force-reseed when it changes.
 *
 *  2. Exclusive groups — only one nav group expanded at a time. Each
 *     <li class="fi-sidebar-group"> carries its label in
 *     `data-group-label`. When the user opens a group, walk every
 *     other group and collapse it via the same store API Filament
 *     uses, so the persisted state stays consistent.
 */
(function () {
    // ── (1) One-time reset on config-version change ──────────────────
    // Bump CURRENT_VERSION whenever you change the panel's default
    // collapsed state in AdminPanelProvider so existing users pick it up.
    const CURRENT_VERSION = '2026-05-18-collapsed-default';
    const VERSION_KEY     = 'pj_nav_version';

    if (localStorage.getItem(VERSION_KEY) !== CURRENT_VERSION) {
        // Wipe Filament's cached state — its boot script will re-init
        // from the new collapsed() config on next render tick.
        localStorage.removeItem('collapsedGroups');
        localStorage.setItem(VERSION_KEY, CURRENT_VERSION);
    }

    // ── (2) Exclusive groups (run once per page) ─────────────────────
    if (window.__passionExclusiveNav) return;
    window.__passionExclusiveNav = true;

    const TRIGGER_SELECTOR = '.fi-sidebar-group-btn, .fi-sidebar-group-collapse-btn';

    document.addEventListener('click', function (e) {
        const trigger = e.target.closest(TRIGGER_SELECTOR);
        if (!trigger) return;

        const clickedGroup = trigger.closest('.fi-sidebar-group[data-group-label]');
        if (!clickedGroup) return;
        const clickedLabel = clickedGroup.dataset.groupLabel;

        // Defer one frame so Filament's own toggle runs first.
        requestAnimationFrame(() => {
            const store = window.Alpine?.store?.('sidebar');
            if (!store || typeof store.groupIsCollapsed !== 'function') return;

            document.querySelectorAll('.fi-sidebar-group[data-group-label]').forEach(group => {
                const label = group.dataset.groupLabel;
                if (! label || label === clickedLabel) return;

                // If this other group is currently expanded, collapse it.
                if (! store.groupIsCollapsed(label)) {
                    store.toggleCollapsedGroup(label);
                }
            });
        });
    }, true);
})();
</script>
