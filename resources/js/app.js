import axios from 'axios';
import Alpine from 'alpinejs';

// Set up Axios defaults
window.axios = axios;
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.Alpine = Alpine;

// Get CSRF token from meta tag
const token = document.head.querySelector('meta[name="csrf-token"]');
if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Theme utilities
window.applyTheme = function(theme) {
    const root = document.documentElement;
    if (theme === 'dark') {
        root.classList.add('dark');
    } else {
        root.classList.remove('dark');
    }
    try {
        localStorage.setItem('theme', theme);
    } catch (e) {
        // ignore storage errors
    }
    window.updateThemeToggleIcon(theme);
};

window.updateThemeToggleIcon = function(theme) {
    const icon = document.getElementById('themeToggleIcon');
    if (!icon) return;
    icon.classList.toggle('fa-moon', theme !== 'dark');
    icon.classList.toggle('fa-sun', theme === 'dark');
};

window.toggleTheme = function() {
    const currentTheme = document.documentElement.classList.contains('dark') ? 'dark' : 'light';
    window.applyTheme(currentTheme === 'dark' ? 'light' : 'dark');
};

// Add notification manager to Alpine store
Alpine.data('notificationManager', () => ({
    open: false,
    notifications: [],
    unreadCount: 0,
    loading: false,
    
    init() {
        this.loadNotifications();
        setInterval(() => {
            if (!this.open) {
                this.loadNotifications();
            }
        }, 30000);
    },
    
    // ... rest of the functions as above
}));

// Password toggle functionality
window.togglePassword = function(fieldName) {
    const passwordInput = document.getElementById(fieldName);
    const toggleIcon = document.getElementById('toggleIcon-' + fieldName);
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
};

// Add interactive effects to inputs
document.addEventListener('DOMContentLoaded', function() {
    const defaultTheme = document.documentElement.dataset.defaultTheme || 'light';
    const storedTheme = (() => {
        try {
            return localStorage.getItem('theme');
        } catch (e) {
            return null;
        }
    })();
    window.applyTheme(storedTheme || defaultTheme);

    const inputs = document.querySelectorAll('input');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('scale-105');
        });
        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('scale-105');
        });
    });
});

// Form submission with AJAX (optional)
window.submitFormAjax = function(formElement, successCallback, errorCallback) {
    const formData = new FormData(formElement);
    
    axios.post(formElement.action, formData)
        .then(response => {
            if (successCallback) {
                successCallback(response);
            }
        })
        .catch(error => {
            if (errorCallback) {
                errorCallback(error);
            }
        });
};

// Start Alpine
Alpine.start();