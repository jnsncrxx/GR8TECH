<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-default-theme="{{ session('user_preferences.dark_mode', false) ? 'dark' : 'light' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }} - @yield('title', 'Dashboard')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        (function() {
            const defaultTheme = document.documentElement.dataset.defaultTheme || 'light';
            let theme = defaultTheme;
            try {
                const stored = localStorage.getItem('theme');
                if (stored) {
                    theme = stored;
                }
            } catch (e) {
                // ignore localStorage errors
            }
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            }
        })();
    </script>
    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    /* ── Employee Portal — Custom Styles ── */
    .emp-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(0,0,0,.08), 0 4px 16px rgba(0,0,0,.05);
        border: 1px solid #e5e7eb;
        margin-bottom: 1.5rem;
        overflow: hidden;
    }
    .emp-card-header {
        display: flex;
        align-items: center;
        gap: .75rem;
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #f1f3f5;
        background: #fafafa;
    }
    .emp-card-header .section-icon {
        width: 34px; height: 34px;
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        font-size: .875rem; flex-shrink: 0;
    }
    .emp-card-header h3 {
        font-size: 1rem; font-weight: 600; color: #111827; margin: 0;
    }
    .emp-card-header p {
        font-size: .75rem; color: #6b7280; margin: 0;
    }
    .emp-card-body { padding: 1.5rem; }

    /* section accent colours */
    .sec-personal .section-icon { background:#eff6ff; color:#2563eb; }
    .sec-work     .section-icon { background:#f0fdf4; color:#16a34a; }
    .sec-details  .section-icon { background:#fef9c3; color:#ca8a04; }
    .sec-emergency .section-icon { background:#fff1f2; color:#e11d48; }
    .sec-loans    .section-icon { background:#f5f3ff; color:#7c3aed; }
    .sec-account  .section-icon { background:#e0f2fe; color:#0284c7; }
    .sec-id       .section-icon { background:#fce7f3; color:#db2777; }
    .sec-statutory .section-icon { background:#f3f4f6; color:#4b5563; }
    .sec-mfg      .section-icon { background:#fff7ed; color:#ea580c; }
    .sec-overrides .section-icon { background:#ecfdf5; color:#059669; }

    /* form controls */
    .form-label {
        display: block; font-size: .8125rem; font-weight: 500;
        color: #374151; margin-bottom: .375rem;
    }
    .form-control {
        width: 100%;
        padding: .5rem .75rem;
        border: 1px solid #d1d5db;
        border-radius: 8px;
        font-size: .875rem; color: #111827;
        background: #fff;
        transition: border-color .15s, box-shadow .15s;
        outline: none;
    }
    .form-control:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59,130,246,.15);
    }
    .form-control.is-error { border-color: #ef4444; }
    .form-control[readonly], .form-control:disabled {
        background: #f9fafb; color: #6b7280; cursor: not-allowed;
    }
    .field-error { font-size: .75rem; color: #ef4444; margin-top: .25rem; display: flex; align-items: center; gap: .25rem; }
    .field-hint  { font-size: .72rem; color: #9ca3af; margin-top: .25rem; }

    /* Employee number badge */
    .emp-num-badge {
        display: inline-flex; align-items: center; gap: .5rem;
        background: #eff6ff; border: 1.5px solid #bfdbfe;
        border-radius: 8px; padding: .45rem .9rem;
        font-size: .875rem; font-weight: 700; color: #1d4ed8;
        letter-spacing: .03em;
    }
    .emp-num-badge i { color: #93c5fd; }

    /* Profile photo */
    .photo-wrap {
        position: relative; width: 100px; height: 100px; cursor: pointer;
    }
    .photo-wrap img, .photo-wrap .photo-placeholder {
        width: 100px; height: 100px; border-radius: 50%;
        object-fit: cover; border: 3px solid #e5e7eb;
    }
    .photo-wrap .photo-placeholder {
        display: flex; align-items: center; justify-content: center;
        background: linear-gradient(135deg,#e0f2fe,#ddd6fe);
        color: #64748b; font-size: 2rem;
    }
    .photo-overlay {
        position: absolute; bottom: 2px; right: 2px;
        background: #2563eb; color: #fff; width: 28px; height: 28px;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: .75rem; box-shadow: 0 2px 4px rgba(0,0,0,.15); border: 2px solid #fff;
    }
    </style>
</head>
<body class="font-sans antialiased bg-brand-surface text-brand-black">
    <div class="min-h-screen">
        <!-- Sidebar -->
        <x-dashboard.sidebar :user="$user" :activeRoute="$activeRoute ?? 'dashboard'" />

        <!-- Main Content -->
        <div class="lg:ml-72">
            <!-- Top Navigation -->
            <x-dashboard.header title="Dashboard" :user="$user" />

            <!-- Dashboard Content -->
            <main class="p-3 sm:p-4 lg:p-6 xl:p-8">
                @yield('content')
            </main>
        </div>

        <!-- Sidebar Overlay -->
        <div class="fixed inset-0 z-40 bg-brand-black/70 hidden" id="sidebar-overlay" onclick="toggleSidebar()"></div>
    </div>

    <script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        
        sidebar.classList.toggle('-translate-x-full');
        overlay.classList.toggle('hidden');
    }

    // Sidebar Time In/Out functions
    async function sidebarTimeIn() {
        const btn = event.target.closest('button');
        if (!btn) return;
        
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3 text-lg text-gray-400"></i><span>Processing...</span>';

        try {
            const response = await fetch('{{ route("attendance.time-in") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok) {
                showSidebarMessage(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showSidebarMessage(data.error || 'Failed to clock in', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        } catch (error) {
            console.error('Error clocking in:', error);
            showSidebarMessage('Failed to clock in', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    async function sidebarTimeOut() {
        const btn = event.target.closest('button');
        if (!btn) return;
        
        const originalHTML = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-3 text-lg text-gray-400"></i><span>Processing...</span>';

        try {
            const response = await fetch('{{ route("attendance.time-out") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            const data = await response.json();

            if (response.ok) {
                showSidebarMessage(data.message, 'success');
                setTimeout(() => {
                    window.location.reload();
                }, 1000);
            } else {
                showSidebarMessage(data.error || 'Failed to clock out', 'error');
                btn.disabled = false;
                btn.innerHTML = originalHTML;
            }
        } catch (error) {
            console.error('Error clocking out:', error);
            showSidebarMessage('Failed to clock out', 'error');
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        }
    }

    function showSidebarMessage(message, type) {
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 ${type === 'success' ? 'bg-green-500' : 'bg-red-500'} text-white px-6 py-3 rounded-lg shadow-lg z-50`;
        toast.textContent = message;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.remove();
        }, 3000);
    }

    // Close sidebar on escape key
    document.addEventListener('keydown', function(event) {
        if (event.key === 'Escape') {
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');
            
            if (!sidebar.classList.contains('-translate-x-full')) {
                sidebar.classList.add('-translate-x-full');
                overlay.classList.add('hidden');
            }
        }
    });

    // Update time every minute
    function updateTime() {
        const timeElement = document.getElementById('current-time');
        const timeElementMobile = document.getElementById('current-time-mobile');
        const now = new Date();
        
        // Use proper timezone for Philippines (Asia/Manila)
        const fullOptions = { 
            timeZone: 'Asia/Manila',
            year: 'numeric', 
            month: 'short', 
            day: 'numeric', 
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        };
        
        const mobileOptions = { 
            timeZone: 'Asia/Manila',
            hour: 'numeric', 
            minute: '2-digit',
            hour12: true 
        };
        
        if (timeElement) {
            timeElement.textContent = now.toLocaleDateString('en-US', fullOptions);
        }
        
        if (timeElementMobile) {
            timeElementMobile.textContent = now.toLocaleTimeString('en-US', mobileOptions);
        }
    }

    // Update time immediately and then every minute
    updateTime();
    setInterval(updateTime, 60000);
    </script>
</body>
</html>