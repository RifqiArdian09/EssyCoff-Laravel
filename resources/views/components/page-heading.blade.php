<div class="mb-6 border-b border-gray-200 dark:border-gray-700 pb-3">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">
        {{ $title ?? $slot }}
    </h1>
    @if (isset($subtitle))
        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            {{ $subtitle }}
        </p>
    @endif
</div>
