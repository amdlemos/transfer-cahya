@props(['class' => ''])

<div class="bg-white dark:bg-zinc-900 rounded-lg shadow-sm border border-zinc-200 dark:border-zinc-700 p-6 {{ $class }}">
    {{ $slot }}
</div>

