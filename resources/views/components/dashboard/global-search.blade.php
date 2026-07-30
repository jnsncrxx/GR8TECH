@props(['user'])

<div class="global-search flex-1 max-w-xl mx-2 sm:mx-4 lg:mx-6"
     x-data="globalSearch()"
     @keydown.window="onGlobalKeydown($event)"
     x-cloak>

    {{-- Desktop / tablet inline search --}}
    <div class="hidden md:block relative" x-ref="desktopWrap">
        <div class="relative">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm pointer-events-none"></i>
            <input
                type="text"
                x-ref="desktopInput"
                x-model="term"
                @input.debounce.300ms="search()"
                @focus="open = term.length >= 2"
                @keydown.down.prevent="move(1)"
                @keydown.up.prevent="move(-1)"
                @keydown.enter.prevent="select(activeIndex)"
                @keydown.escape="close()"
                placeholder="Search employees, records, departments... (⌘K)"
                autocomplete="off"
                class="w-full pl-9 pr-8 py-2 text-sm bg-gray-50 border border-gray-200 rounded-lg focus:bg-white focus:border-blue-400 focus:ring-2 focus:ring-blue-100 outline-none transition-colors dark:bg-slate-800 dark:border-slate-700 dark:text-gray-100 dark:focus:bg-slate-800"
            >
            <button type="button" x-show="term.length > 0" @click="clearSearch(); $refs.desktopInput.focus()"
                    class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 p-1">
                <i class="fas fa-times-circle text-sm"></i>
            </button>
        </div>

        <template x-if="open">
            <div @click.away="close()" class="absolute left-0 right-0 mt-2 bg-white border border-gray-200 rounded-xl shadow-xl z-50 dark:bg-slate-900 dark:border-slate-700 overflow-hidden">
                <x-dashboard.global-search-results />
            </div>
        </template>
    </div>

    {{-- Mobile trigger --}}
    <div class="md:hidden">
        <button type="button" @click="openMobile()" class="p-2 text-gray-500 hover:text-gray-700 rounded-lg hover:bg-gray-100 transition-colors dark:text-gray-200 dark:hover:bg-slate-700">
            <i class="fas fa-search text-lg"></i>
        </button>
    </div>

    {{-- Mobile full-screen overlay --}}
    <template x-if="mobileOpen">
        <div class="fixed inset-0 z-50 bg-white dark:bg-slate-900 flex flex-col md:hidden">
            <div class="flex items-center gap-2 p-3 border-b border-gray-200 dark:border-slate-700">
                <div class="relative flex-1">
                    <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                    <input
                        type="text"
                        x-ref="mobileInput"
                        x-model="term"
                        @input.debounce.300ms="search()"
                        @keydown.enter.prevent="select(activeIndex)"
                        placeholder="Search..."
                        autocomplete="off"
                        class="w-full pl-9 pr-3 py-2.5 text-sm bg-gray-50 border border-gray-200 rounded-lg outline-none focus:border-blue-400 dark:bg-slate-800 dark:border-slate-700 dark:text-gray-100"
                    >
                </div>
                <button type="button" @click="close()" class="text-sm font-medium text-gray-500 px-2 py-2">Cancel</button>
            </div>
            <div class="flex-1 overflow-y-auto">
                <x-dashboard.global-search-results />
            </div>
        </div>
    </template>
</div>

@once
<script>
function globalSearch() {
    return {
        term: '',
        open: false,
        mobileOpen: false,
        loading: false,
        error: false,
        modules: [],
        flatResults: [], // flattened for keyboard nav across groups
        activeIndex: -1,
        activeModule: null,
        requestToken: 0,

        onGlobalKeydown(e) {
            const isCmdK = (e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k';
            if (isCmdK) {
                e.preventDefault();
                if (window.innerWidth < 768) {
                    this.openMobile();
                } else {
                    this.open = true;
                    this.$nextTick(() => this.$refs.desktopInput?.focus());
                }
            }
        },

        openMobile() {
            this.mobileOpen = true;
            this.$nextTick(() => this.$refs.mobileInput?.focus());
        },

        close() {
            this.open = false;
            this.mobileOpen = false;
        },

        clearSearch() {
            this.term = '';
            this.modules = [];
            this.flatResults = [];
            this.activeIndex = -1;
            this.error = false;
        },

        setModuleFilter(key) {
            this.activeModule = this.activeModule === key ? null : key;
            this.search();
        },

        async search() {
            if (this.term.trim().length < 2) {
                this.modules = [];
                this.flatResults = [];
                this.open = false;
                return;
            }

            this.open = true;
            this.loading = true;
            this.error = false;

            const token = ++this.requestToken;
            const params = new URLSearchParams({ q: this.term });
            if (this.activeModule) params.set('module', this.activeModule);

            try {
                const res = await fetch(`{{ route('search') }}?${params.toString()}`, {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    }
                });

                if (token !== this.requestToken) return; // stale response, ignore

                if (!res.ok) throw new Error('Search failed');

                const data = await res.json();
                this.modules = data.modules || [];
                this.flatResults = this.modules.flatMap(m => m.results);
                this.activeIndex = this.flatResults.length ? 0 : -1;
            } catch (e) {
                if (token !== this.requestToken) return;
                this.error = true;
                this.modules = [];
                this.flatResults = [];
            } finally {
                if (token === this.requestToken) this.loading = false;
            }
        },

        move(delta) {
            if (!this.flatResults.length) return;
            const max = this.flatResults.length - 1;
            this.activeIndex = Math.min(max, Math.max(0, this.activeIndex + delta));
            this.$nextTick(() => {
                const el = document.querySelector(`[data-result-index="${this.activeIndex}"]`);
                el?.scrollIntoView({ block: 'nearest' });
            });
        },

        select(index) {
            const result = this.flatResults[index];
            if (!result) return;
            window.location.href = result.url;
        },

        highlight(text) {
            if (!text) return '';
            const escaped = this.term.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
            const re = new RegExp('(' + escaped + ')', 'ig');
            const safeText = String(text).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return safeText.replace(re, '<mark class="bg-yellow-200 text-inherit rounded-sm px-0.5 dark:bg-yellow-500/40">$1</mark>');
        },
    }
}
</script>
@endonce
