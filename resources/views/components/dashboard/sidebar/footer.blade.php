@props(['user'])

<div class="w-full p-4 border-t border-brand-black/10 bg-brand-surface flex-shrink-0">
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <div class="w-10 h-10 bg-gradient-to-r from-brand-green to-brand-red rounded-xl flex items-center justify-center shadow-lg">
                <i class="fas fa-user text-brand-white text-sm"></i>
            </div>
        </div>
        <div class="ml-3 flex-1">
            <p class="text-sm font-semibold text-brand-black">{{ $user->full_name }}</p>
            <p class="text-xs text-brand-black/70">{{ ucfirst($user->role) }} • {{ $user->employee->department->name ?? 'System' }}</p>
        </div>
        <div class="flex items-center space-x-2">
            <!-- Status indicator -->
            <div class="w-2 h-2 bg-brand-green rounded-full"></div>
            <span class="text-xs text-brand-black/70">Online</span>
        </div>
    </div>
    
    <!-- Quick stats for user -->
    <div class="mt-3 grid grid-cols-2 gap-2">
        <div class="bg-brand-white rounded-lg p-2 text-center border border-brand-black/10">
            <p class="text-xs text-brand-black/70">Last Login</p>
            <p class="text-xs font-medium text-brand-black">{{ $user->last_login_at ? $user->last_login_at->format('M d') : 'Never' }}</p>
        </div>
        <div class="bg-brand-white rounded-lg p-2 text-center border border-brand-black/10">
            <p class="text-xs text-brand-black/70">Status</p>
            <p class="text-xs font-medium text-brand-green">Active</p>
        </div>
    </div>
</div>
