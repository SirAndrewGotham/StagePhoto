import { Livewire, Alpine } from '../../vendor/livewire/livewire/dist/livewire.esm';

// Global dark mode store
Alpine.store('darkMode', {
    init() {
        const stored = localStorage.getItem('darkMode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        this.enabled = stored ? stored === 'true' : prefersDark;
        this.apply();

        // Listen for system changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (!localStorage.getItem('darkMode')) {
                this.enabled = e.matches;
                this.apply();
            }
        });
    },
    enabled: false,
    toggle() {
        this.enabled = !this.enabled;
        localStorage.setItem('darkMode', this.enabled);
        this.apply();
    },
    apply() {
        document.documentElement.classList.toggle('dark', this.enabled);
    }
});

Livewire.start();
