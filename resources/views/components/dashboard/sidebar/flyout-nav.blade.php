@props([
    'label' => '',
    'panelClass' => 'w-72 max-h-[min(70vh,32rem)]',
    'panelZIndex' => 'z-[100]',
    'nested' => false,
])

@php
    $triggerBase = $nested
        ? 'w-full flex items-center justify-between rounded-md px-3 py-2 text-sm transition-colors'
        : 'w-full flex items-center justify-between px-4 py-3 text-sm font-medium rounded-lg transition-all duration-200 group';
@endphp

<div class="relative" x-data="sidebarFlyout()">
    <button
        type="button"
        x-ref="flyoutTrigger"
        @mouseenter="showFlyout()"
        @mouseleave="hideFlyout()"
        {{ $attributes->merge(['class' => $triggerBase]) }}
    >
        {{ $trigger }}
    </button>
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 translate-x-1"
        x-transition:enter-end="opacity-100 translate-x-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 translate-x-0"
        x-transition:leave-end="opacity-0 translate-x-1"
        @mouseenter="showFlyout()"
        @mouseleave="hideFlyout()"
        class="brand-sidebar-flyout fixed {{ $panelZIndex }} {{ $panelClass }} overflow-y-auto rounded-lg border py-2"
        :style="`top: ${flyoutTop}px; left: ${flyoutLeft}px`"
    >
        @if($label !== '')
            <p class="brand-sidebar-flyout__label px-4 pb-2 text-xs font-semibold uppercase tracking-wider">{{ $label }}</p>
        @endif
        {{ $slot }}
    </div>
</div>
