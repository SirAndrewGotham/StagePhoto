/**
 * StagePhoto.ru — Frontend Entry Point
 * Livewire 4 + Alpine 3 + Tailwind 4
 */

import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// =============================================================================
// 🌐 Translation Magic Helper: x-text="t('key')"
// =============================================================================
Alpine.magic('t', () => (key) => {
    // Fallback to key if translation missing (dev-friendly)
    return window.translations?.[key] || key;
});

// =============================================================================
// 🌓 Dark Mode Store: $store.darkMode
// =============================================================================
Alpine.store('darkMode', {
    init() {
        const stored = localStorage.getItem('darkMode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Initialize from localStorage or OS preference
        this.enabled = stored ? stored === 'true' : prefersDark;
        this.apply();

        // Listen for OS preference changes (only if user hasn't manually set)
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (localStorage.getItem('darkMode') === null) {
                this.enabled = e.matches;
                this.apply();
            }
        });
    },

    enabled: false,

    toggle() {
        this.enabled = !this.enabled;
        localStorage.setItem('darkMode', this.enabled.toString());
        this.apply();
    },

    apply() {
        // Toggle 'dark' class on <html> for Tailwind @variant dark
        document.documentElement.classList.toggle('dark', this.enabled);
    }
});

// =============================================================================
// 🌍 Language Store: $store.language
// =============================================================================
Alpine.store('language', {
    init() {
        const stored = localStorage.getItem('language');
        this.current = stored || document.documentElement.lang || 'ru';
        this.apply();
    },

    current: 'ru',

    set(lang) {
        this.current = lang;
        localStorage.setItem('language', lang);
        this.apply();
    },

    apply() {
        // Update <html lang> attribute for SEO/accessibility
        document.documentElement.lang = this.current;

        // Optional: trigger page reload for server-side locale change
        // window.location.search = `?lang=${this.current}`;
    }
});

// =============================================================================
// 🎯 Livewire Event Interceptors (Optional but useful)
// =============================================================================
Livewire.hook('request', ({ uri, options, payload, respond }) => {
    // Add CSRF token to all Livewire requests (Laravel handles this by default,
    // but explicit is better for debugging)
    options.headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content;

    respond(({ status, json }) => {
        // Global error handling
        if (status >= 400) {
            console.error('Livewire request failed:', json);
            // Optional: show toast notification here
        }
    });
});

// =============================================================================
// 🚀 Start Livewire (MUST be last)
// =============================================================================
Livewire.start();
