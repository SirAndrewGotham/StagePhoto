<button
    type="button"
    @click="$store.darkMode.toggle()"
    class="p-2 rounded-lg hover:bg-stage-100 dark:hover:bg-stage-900"
    aria-label="Toggle dark mode"
>
    <svg x-show="!$store.darkMode.enabled" class="w-5 h-5">☀️</svg>
    <svg x-show="$store.darkMode.enabled" class="w-5 h-5">🌙</svg>
</button>
