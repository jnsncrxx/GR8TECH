@props(['user'])

<div class="flex items-center h-20 px-6 bg-gradient-to-r from-brand-green to-brand-black shadow-sm border-b border-brand-black/20">
    <div class="flex items-center flex-1 min-w-0">
        <div class="w-12 h-12 bg-brand-red rounded-lg flex items-center justify-center mr-4 flex-shrink-0 shadow-lg shadow-brand-black/20">
            <i class="fas fa-chart-line text-brand-white text-lg"></i>
        </div>
        <div class="min-w-0 flex-1">
            <h1 class="text-brand-white font-semibold text-base truncate">{{ $user->role === 'hr' ? 'Human Resources' : ucfirst($user->role) . ' Dashboard' }}</h1>
            <p class="text-brand-white/80 text-sm truncate">Dashboard</p>
        </div>
    </div>
    <button class="lg:hidden text-brand-white/70 hover:text-brand-white transition-colors p-2 rounded-lg hover:bg-brand-black/25 flex-shrink-0 ml-2" onclick="toggleSidebar()">
        <i class="fas fa-times text-lg"></i>
    </button>
</div>
