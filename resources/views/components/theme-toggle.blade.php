<flux:button
    x-data
    x-on:click="$flux.appearance = 'dark'"
    icon="moon"
    variant="subtle"
    class="dark:hidden"
    aria-label="Toggle dark mode"
/>
<flux:button
    x-data
    x-on:click="$flux.appearance = 'light'"
    icon="sun"
    variant="subtle"
    class="hidden dark:flex"
    aria-label="Toggle light mode"
/>
