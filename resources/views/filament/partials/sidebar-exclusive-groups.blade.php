<script data-navigate-once>
/**
 * Exclusive sidebar navigation groups — only one expanded at a time.
 * When the user opens a group, any other open group is collapsed.
 * Implemented as an event delegate so it survives Livewire/Alpine re-renders.
 */
(function () {
    if (window.__passionExclusiveNav) return;
    window.__passionExclusiveNav = true;

    const SELECTORS = [
        '.fi-sidebar-group-button',
        '[data-fi-sidebar-group-button]',
        '.fi-sidebar-group-collapse-button',
    ].join(', ');

    document.addEventListener('click', function (e) {
        const btn = e.target.closest(SELECTORS);
        if (!btn) return;

        // Defer until after Filament/Alpine has finished toggling this group.
        requestAnimationFrame(() => {
            const allBtns = document.querySelectorAll(SELECTORS);
            allBtns.forEach(other => {
                if (other === btn) return;
                if (other.getAttribute('aria-expanded') === 'true') {
                    other.click();
                }
            });
        });
    }, true);
})();
</script>
