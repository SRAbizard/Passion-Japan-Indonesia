<script>
/**
 * Runs at BODY_START (synchronously, before Filament's sidebar Livewire
 * component boots). Resets the cached collapsedGroups list when the
 * admin-panel nav config version changes, so users with stale state
 * pick up new ->collapsed() defaults on their next request.
 *
 * Filament's init logic (vendor/filament/filament/resources/views/livewire/sidebar.blade.php)
 * reads localStorage.collapsedGroups, and ONLY seeds from PHP when the
 * key is missing — it never re-syncs. We delete the key here so its
 * init runs again with the current config.
 *
 * NOTE: deliberately no `@ js(...)` in this comment — Blade would
 * compile the directive even inside a JS block comment.
 *
 * Bump CURRENT_VERSION whenever AdminPanelProvider::navigationGroups()
 * changes its ->collapsed() defaults.
 */
(function () {
    const CURRENT_VERSION = '2026-05-18-collapsed-default';
    const VERSION_KEY     = 'pj_nav_version';

    try {
        if (localStorage.getItem(VERSION_KEY) !== CURRENT_VERSION) {
            localStorage.removeItem('collapsedGroups');
            localStorage.setItem(VERSION_KEY, CURRENT_VERSION);
        }
    } catch (e) {
        // localStorage may be unavailable in some browser modes; ignore.
    }
})();
</script>
