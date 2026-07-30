{{-- Module filter chips --}}
<template x-if="modules.length > 0 || activeModule">
    <div class="flex items-center gap-1.5 px-3 pt-3 pb-1 overflow-x-auto">
        <template x-for="m in modules" :key="m.key">
            <button type="button"
                    @click="setModuleFilter(m.key)"
                    :class="activeModule === m.key ? 'bg-blue-600 text-white border-blue-600' : 'bg-gray-50 text-gray-600 border-gray-200 hover:bg-gray-100 dark:bg-slate-800 dark:text-gray-300 dark:border-slate-700'"
                    class="flex-shrink-0 text-xs font-medium px-2.5 py-1 rounded-full border transition-colors">
                <span x-text="m.label"></span>
                <span class="opacity-70" x-text="' · ' + m.count"></span>
            </button>
        </template>
    </div>
</template>

{{-- Loading state --}}
<div x-show="loading" class="px-4 py-6 flex items-center justify-center text-gray-400 text-sm">
    <i class="fas fa-spinner fa-spin mr-2"></i> Searching...
</div>

{{-- Error state --}}
<div x-show="!loading && error" class="px-4 py-6 text-center text-sm text-red-500">
    <i class="fas fa-exclamation-triangle mr-1"></i> Something went wrong. Try again.
</div>

{{-- Empty state --}}
<div x-show="!loading && !error && term.length >= 2 && modules.length === 0" class="px-4 py-8 text-center">
    <i class="fas fa-search text-2xl text-gray-300 mb-2"></i>
    <p class="text-sm text-gray-500">No results for "<span x-text="term"></span>"</p>
</div>

{{-- Hint state (query too short) --}}
<div x-show="term.length > 0 && term.length < 2" class="px-4 py-6 text-center text-sm text-gray-400">
    Keep typing to search...
</div>

{{-- Results, grouped by module --}}
<div x-show="!loading && !error && modules.length > 0" class="max-h-[70vh] md:max-h-96 overflow-y-auto py-1">
    <template x-for="(m, mIdx) in modules" :key="m.key">
        <div>
            <div class="px-3 pt-2 pb-1 text-xs font-semibold text-gray-400 uppercase tracking-wide" x-text="m.label"></div>
            <template x-for="(r, rIdx) in m.results" :key="r.id">
                <a :href="r.url"
                   :data-result-index="flatResults.indexOf(r)"
                   @mouseenter="activeIndex = flatResults.indexOf(r)"
                   :class="activeIndex === flatResults.indexOf(r) ? 'bg-blue-50 dark:bg-slate-800' : 'hover:bg-gray-50 dark:hover:bg-slate-800'"
                   class="flex items-center gap-3 px-3 py-2.5 transition-colors">
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0 dark:bg-slate-800 dark:text-blue-400">
                        <i class="fas" :class="r.icon || 'fa-circle'"></i>
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate dark:text-gray-100" x-html="highlight(r.title)"></p>
                        <p class="text-xs text-gray-500 truncate dark:text-gray-400" x-show="r.subtitle" x-html="highlight(r.subtitle)"></p>
                    </div>
                    <span class="text-xs text-gray-400 flex-shrink-0" x-text="r.meta"></span>
                </a>
            </template>
        </div>
    </template>
</div>
