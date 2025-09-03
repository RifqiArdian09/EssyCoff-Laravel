@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Pagination Navigation" class="flex items-center gap-2">
        @if ($paginator->onFirstPage())
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-700 text-gray-400 dark:text-zinc-500 cursor-not-allowed"
                  aria-disabled="true" aria-label="Previous">
                <x-heroicon-s-chevron-double-left class="w-5 h-5" />
            </span>
        @else
            <a href="{{ $paginator->previousPageUrl() }}" rel="prev"
               class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-white/5 transition"
               aria-label="Previous">
                <x-heroicon-s-chevron-double-left class="w-5 h-5" />
            </a>
        @endif

        @if ($paginator->hasMorePages())
            <a href="{{ $paginator->nextPageUrl() }}" rel="next"
               class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-200 hover:bg-gray-50 dark:hover:bg-white/5 transition"
               aria-label="Next">
                <x-heroicon-s-chevron-double-right class="w-5 h-5" />
            </a>
        @else
            <span class="inline-flex items-center justify-center w-9 h-9 rounded-md border border-gray-200 dark:border-zinc-700 bg-gray-100 dark:bg-zinc-700 text-gray-400 dark:text-zinc-500 cursor-not-allowed"
                  aria-disabled="true" aria-label="Next">
                <x-heroicon-s-chevron-double-right class="w-5 h-5" />
            </span>
        @endif
    </nav>
@endif
