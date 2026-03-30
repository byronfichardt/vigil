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

    {{-- Sub-nav tabs --}}
    <div class="flex items-center gap-4 border-b border-gray-800 mb-6">
        <a href="{{ route('projects.show', $project) }}" wire:navigate
            class="border-b-2 border-transparent pb-3 pt-1 text-sm font-medium text-gray-400 hover:text-white transition-colors">
            Exceptions
        </a>
        <a href="{{ route('projects.logs', $project) }}" wire:navigate
            class="border-b-2 border-vigil-500 pb-3 pt-1 text-sm font-medium text-white transition-colors">
            Logs
        </a>
    </div>

    {{-- Filters --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:flex-wrap">
        <select wire:model.live="level"
            class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
            <option value="">All levels</option>
            @foreach($levels as $lvl)
                <option value="{{ $lvl }}">{{ ucfirst($lvl) }}</option>
            @endforeach
        </select>

        <select wire:model.live="channel"
            class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
            <option value="">All channels</option>
            @foreach($channels as $ch)
                <option value="{{ $ch }}">{{ $ch }}</option>
            @endforeach
        </select>

        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search logs..."
                class="w-full rounded-lg border border-gray-700 bg-gray-800 pl-10 pr-4 py-2 text-sm text-white placeholder-gray-500 focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500 sm:w-64">
            <svg class="absolute left-3 top-2.5 h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>

        <div class="flex items-center gap-2">
            <input type="date" wire:model.live="dateFrom"
                class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
            <span class="text-gray-500 text-sm">to</span>
            <input type="date" wire:model.live="dateTo"
                class="rounded-lg border border-gray-700 bg-gray-800 px-3 py-2 text-sm text-white focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
        </div>
    </div>

    {{-- Log List --}}
    <div class="space-y-2">
        @forelse($logs as $log)
            <div x-data="{ open: false }"
                class="rounded-lg border border-gray-800 bg-gray-900 transition-colors hover:border-gray-700">
                <button @click="open = !open" class="w-full p-4 text-left">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center gap-3">
                                <span @class([
                                    'inline-flex shrink-0 items-center rounded-full px-2 py-0.5 text-xs font-medium',
                                    $log->levelColor(),
                                ])>
                                    {{ $log->level }}
                                </span>
                                <span class="text-xs text-gray-500">{{ $log->channel }}</span>
                            </div>
                            <p class="mt-1 truncate text-sm text-gray-300">{{ $log->message }}</p>
                        </div>
                        <div class="flex shrink-0 items-center gap-3">
                            <span class="text-xs text-gray-500">{{ $log->logged_at->diffForHumans() }}</span>
                            <svg :class="{ 'rotate-180': open }" class="h-4 w-4 text-gray-500 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </div>
                    </div>
                </button>

                <div x-show="open" x-cloak x-collapse class="border-t border-gray-800 px-4 pb-4 pt-3">
                    {{-- Full message --}}
                    <div class="mb-3">
                        <h4 class="text-xs font-medium uppercase text-gray-500 mb-1">Message</h4>
                        <p class="text-sm text-gray-300 whitespace-pre-wrap">{{ $log->message }}</p>
                    </div>

                    {{-- Context --}}
                    @if($log->context)
                        <div class="mb-3">
                            <h4 class="text-xs font-medium uppercase text-gray-500 mb-1">Context</h4>
                            <pre class="overflow-x-auto rounded-lg bg-gray-800 p-3 text-xs text-gray-300">{{ json_encode($log->context, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @endif

                    {{-- Extra --}}
                    @if($log->extra)
                        <div class="mb-3">
                            <h4 class="text-xs font-medium uppercase text-gray-500 mb-1">Extra</h4>
                            <pre class="overflow-x-auto rounded-lg bg-gray-800 p-3 text-xs text-gray-300">{{ json_encode($log->extra, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                        </div>
                    @endif

                    {{-- Metadata --}}
                    <div class="flex flex-wrap gap-x-6 gap-y-2 text-xs text-gray-500">
                        @if($log->environment)
                            <span>Env: <span class="text-gray-400">{{ $log->environment }}</span></span>
                        @endif
                        @if($log->hostname)
                            <span>Host: <span class="text-gray-400">{{ $log->hostname }}</span></span>
                        @endif
                        @if($log->request_url)
                            <span>{{ $log->request_method }} <span class="text-gray-400">{{ $log->request_url }}</span></span>
                        @endif
                        <span>{{ $log->logged_at->format('Y-m-d H:i:s') }}</span>
                    </div>
                </div>
            </div>
        @empty
            <div class="rounded-lg border border-gray-800 bg-gray-900 p-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                </svg>
                <h3 class="mt-4 text-sm font-medium text-white">
                    @if($search)
                        No logs matching "{{ $search }}"
                    @else
                        No logs found
                    @endif
                </h3>
                <p class="mt-2 text-sm text-gray-400">
                    Logs will appear here once your application starts sending them.
                </p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $logs->links() }}
    </div>
</div>
