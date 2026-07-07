<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['user']));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['user']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="flex items-center h-20 px-6 bg-gradient-to-r from-brand-green to-brand-black shadow-sm border-b border-brand-black/20">
    <div class="flex items-center flex-1 min-w-0">
        <div class="w-12 h-12 bg-brand-red rounded-lg flex items-center justify-center mr-4 flex-shrink-0 shadow-lg shadow-brand-black/20">
            <i class="fas fa-chart-line text-brand-white text-lg"></i>
        </div>
        <div class="min-w-0 flex-1">
            <h1 class="text-brand-white font-semibold text-base truncate"><?php echo e($user->role === 'hr' ? 'Human Resources' : ucfirst($user->role) . ' Dashboard'); ?></h1>
            <p class="text-brand-white/80 text-sm truncate">Dashboard</p>
        </div>
    </div>
    <button class="lg:hidden text-brand-white/70 hover:text-brand-white transition-colors p-2 rounded-lg hover:bg-brand-black/25 flex-shrink-0 ml-2" onclick="toggleSidebar()">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>
<?php /**PATH C:\Users\Xzanderyiel\Desktop\GR8TECH_Payroll-master\resources\views/components/dashboard/sidebar/header.blade.php ENDPATH**/ ?>