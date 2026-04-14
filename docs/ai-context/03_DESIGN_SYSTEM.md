# 🎨 Design System & Layout Rules

## 🌐 Full-Width Mandate
- **NEVER** use `container`, `max-w-*`, or centered constraints on main content
- Header, filter bar, grid, footer: all `width: 100vw` with responsive padding
- Grid: `display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));`
- Gaps scale with viewport: `1rem` (mobile) → `2rem` (≥1920px)

## 🎨 Color Palette
```css
@theme {
  --color-stage-50: #fff8f0;
  --color-stage-100: #ffedd5;
  --color-stage-500: #f97316;
  --color-stage-600: #ea580c;
  --color-stage-900: #1e1b4b;
  --color-spotlight: #fef08a;
}
```
- **Dark Mode**: `@variant dark { ... }` or `dark:` prefixes
- **Auto-Detect**: `window.matchMedia('(prefers-color-scheme: dark)')` → localStorage fallback

## 🔤 Typography
- Font: `Inter`, system-ui fallback
- Headings: `font-bold`, `tracking-tight`
- Body: `text-sm` (mobile) → `text-base` (desktop)
- Line height: `1.5` for readability

## 📱 Responsive Breakpoints
| Size | Columns | Gap | Padding |
|------|---------|-----|---------|
| `<640px` | 2 | 1rem | `px-4` |
| `≥640px` | 2 | 1.25rem | `px-6` |
| `≥1024px` | 3-4 | 1.5rem | `px-8` |
| `≥1440px` | 4-5 | 1.75rem | `px-10` |
| `≥1920px` | 5-6 | 2rem | `px-12` |

## 🌓 Dark Mode Rules
1. Check OS preference on load
2. Respect `localStorage('darkMode')`
3. Listen to system changes in real-time
4. Toggle button always visible next to language switcher

## 🌓 Dark Mode Implementation with Livewire 4

### Alpine.js Store (Global State)
Place this in your main layout file (`resources/views/layouts/app.blade.php`):

```javascript
<script>
document.addEventListener('alpine:init', () => {
    Alpine.store('theme', {
        dark: localStorage.getItem('darkMode') === 'true' 
            || (localStorage.getItem('darkMode') === null 
                && window.matchMedia('(prefers-color-scheme: dark)').matches),
        
        init() {
            // Apply initial state
            if (this.dark) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
            
            // Watch for changes
            this.$watch('dark', (value) => {
                if (value) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
                localStorage.setItem('darkMode', value);
            });
            
            // Listen for system changes
            window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                if (localStorage.getItem('darkMode') === null) {
                    this.dark = e.matches;
                }
            });
        },
        
        toggle() {
            this.dark = !this.dark;
        },
        
        enable() {
            this.dark = true;
        },
        
        disable() {
            this.dark = false;
        }
    });
});
</script>
```

### Dark Mode Toggle Component (`⚡dark-mode-toggle.blade.php`)

```blade
<?php
use Livewire\Component;

new class extends Component {
    public function toggle()
    {
        // Dispatch event for Alpine to handle
        $this->dispatch('theme-toggled');
    }
    
    public function render()
    {
        return <<<'HTML'
            <div>
                <button 
                    wire:click="toggle"
                    @click="$store.theme.toggle()"
                    class="p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors"
                    :aria-label="$store.theme.dark ? 'Switch to light mode' : 'Switch to dark mode'"
                >
                    <!-- Sun icon (light mode) -->
                    <svg x-show="!$store.theme.dark" class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"/>
                    </svg>
                    
                    <!-- Moon icon (dark mode) -->
                    <svg x-show="$store.theme.dark" x-cloak class="w-5 h-5 text-indigo-300" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"/>
                    </svg>
                </button>
            </div>
        HTML;
    }
};
?>
```

### Livewire Component Integration for Dark Mode

```php
<?php
use Livewire\Component;
use Livewire\Attributes\On;

new class extends Component {
    #[On('theme-toggled')]
    public function handleThemeChange()
    {
        // Re-fetch data if needed for dark mode specific content
        // For example, load different images or adjust contrast
        $this->dispatch('$refresh');
    }
    
    public function render()
    {
        return <<<'HTML'
            <div>
                <!-- Component content that responds to dark mode -->
                <div class="bg-white dark:bg-gray-800">
                    <h2 class="text-gray-900 dark:text-white">Title adapts to theme</h2>
                </div>
            </div>
        HTML;
    }
};
?>
```

### CSS Strategy with Tailwind 4

```css
/* Using @variant directive (Tailwind 4) */
@variant dark {
    body {
        @apply bg-gray-950 text-gray-100;
    }
    
    .card {
        @apply bg-gray-800 border-gray-700;
    }
}

/* Or use dark: prefix in classes */
<div class="bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700">
    Content adapts automatically
</div>

/* Custom dark mode variants */
@layer utilities {
    .dark\:custom-class {
        @apply dark:bg-stage-900 dark:text-stage-100;
    }
}
```

### System Preference Detection with Fallback

```javascript
// Complete initialization sequence
function initDarkMode() {
    // Check localStorage first
    const stored = localStorage.getItem('darkMode');
    
    if (stored !== null) {
        return stored === 'true';
    }
    
    // Fall back to system preference
    return window.matchMedia('(prefers-color-scheme: dark)').matches;
}

// Listen for system changes when no user preference is set
const darkModeMediaQuery = window.matchMedia('(prefers-color-scheme: dark)');
darkModeMediaQuery.addEventListener('change', (e) => {
    if (localStorage.getItem('darkMode') === null) {
        document.documentElement.classList.toggle('dark', e.matches);
    }
});
```

### Testing Dark Mode

```php
// In Livewire tests
test('dark mode persists after page reload', function () {
    $this->withCookie('darkMode', 'true')
        ->get('/')
        ->assertSee('dark');
});

// In browser tests
test('dark mode toggle works', async ({ page }) => {
    await page.click('[aria-label="Switch to dark mode"]');
    const htmlClass = await page.getAttribute('html', 'class');
    expect(htmlClass).toContain('dark');
});
```
