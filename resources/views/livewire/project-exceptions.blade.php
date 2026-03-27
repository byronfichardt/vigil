<div>
    {{-- Header --}}
    <div class="mb-6">
        <div class="flex items-center gap-2 text-sm text-gray-400 mb-4">
            <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-white">Dashboard</a>
            <span>/</span>
            <span class="text-gray-300">{{ $project->name }}</span>
        </div>
        <div class="flex items-center justify-between">
            <h1 class="text-2xl font-bold text-white">{{ $project->name }}</h1>
            <a href="{{ route('projects.settings', $project) }}" wire:navigate
                class="rounded-lg border border-gray-700 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                Settings
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex items-center gap-2">
            @foreach(['unresolved', 'resolved', 'ignored', 'all'] as $filter)
                <button wire:click="$set('status', '{{ $filter }}')"
                    @class([
                        'rounded-lg px-3 py-1.5 text-sm font-medium transition-colors',
                        'bg-gray-800 text-white' => $status === $filter,
                        'text-gray-400 hover:text-white hover:bg-gray-800/50' => $status !== $filter,
                    ])>
                    {{ ucfirst($filter) }}
                </button>
            @endforeach
        </div>
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search exceptions..."
                class="w-full rounded-lg border border-gray-700 bg-gray-800 pl-10 pr-4 py-2 text-sm text-white placeholder-gray-500 focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500 sm:w-80">
            <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    {{-- Exception List --}}
    <div class="space-y-2">
        @forelse($exceptions as $exception)
            <a href="{{ route('exceptions.show', $exception) }}" wire:navigate
                class="group block rounded-lg border border-gray-800 bg-gray-900 p-4 hover:border-gray-700 transition-colors">
                <div class="flex items-start justify-between gap-4">
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-3">
                            @if($exception->status === 'unresolved')
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-red-500"></span>
                            @elseif($exception->status === 'resolved')
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-green-500"></span>
                            @else
                                <span class="h-2.5 w-2.5 shrink-0 rounded-full bg-gray-500"></span>
                            @endif
                            <p class="truncate font-mono text-sm font-semibold text-white group-hover:text-vigil-400 transition-colors">
                                {{ class_basename($exception->exception_class) }}
                            </p>
                        </div>
                        <p class="mt-1 truncate pl-5.5 text-sm text-gray-400">{{ $exception->message }}</p>
                        <p class="mt-1 pl-5.5 text-xs text-gray-500">
                            {{ $exception->file }}:{{ $exception->line }}
                        </p>
                    </div>
                    <div class="shrink-0 text-right">
                        <span class="inline-flex items-center rounded-full bg-gray-800 px-2.5 py-0.5 text-xs font-medium text-gray-300">
                            {{ $exception->occurrence_count }}x
                        </span>
                        <p class="mt-1 text-xs text-gray-500">{{ $exception->last_seen_at->diffForHumans() }}</p>
                    </div>
                </div>
            </a>
        @empty
            <div class="rounded-lg border border-gray-800 bg-gray-900 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-4 text-sm font-medium text-white">
                    @if($search)
                        No exceptions matching "{{ $search }}"
                    @elseif($status === 'unresolved')
                        No unresolved exceptions
                    @else
                        No exceptions found
                    @endif
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    @if($status === 'unresolved' && !$search)
                        All clear! Your application is running smoothly.
                    @endif
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $exceptions->links() }}
    </div>
</div>
