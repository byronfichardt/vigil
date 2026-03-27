<div>
    {{-- Breadcrumb --}}
    <div class="flex items-center gap-2 text-sm text-gray-400 mb-6">
        <a href="{{ route('dashboard') }}" wire:navigate class="hover:text-white">Dashboard</a>
        <span>/</span>
        <a href="{{ route('projects.show', $exceptionGroup->project) }}" wire:navigate class="hover:text-white">{{ $exceptionGroup->project->name }}</a>
        <span>/</span>
        <span class="text-gray-300">{{ class_basename($exceptionGroup->exception_class) }}</span>
    </div>

    {{-- Header --}}
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="min-w-0">
            <div class="flex items-center gap-3 mb-2">
                @if($exceptionGroup->status === 'unresolved')
                    <span class="inline-flex items-center rounded-full bg-red-500/10 px-2.5 py-1 text-xs font-medium text-red-400 border border-red-500/20">Unresolved</span>
                @elseif($exceptionGroup->status === 'resolved')
                    <span class="inline-flex items-center rounded-full bg-green-500/10 px-2.5 py-1 text-xs font-medium text-green-400 border border-green-500/20">Resolved</span>
                @else
                    <span class="inline-flex items-center rounded-full bg-gray-500/10 px-2.5 py-1 text-xs font-medium text-gray-400 border border-gray-500/20">Ignored</span>
                @endif
                <span class="text-sm text-gray-400">{{ $exceptionGroup->occurrence_count }} occurrence{{ $exceptionGroup->occurrence_count !== 1 ? 's' : '' }}</span>
            </div>
            <h1 class="text-xl font-bold font-mono text-white break-all">{{ $exceptionGroup->exception_class }}</h1>
            <p class="mt-2 text-gray-300">{{ $exceptionGroup->message }}</p>
            <p class="mt-1 text-sm text-gray-500">
                {{ $exceptionGroup->file }}:{{ $exceptionGroup->line }}
                &middot; First seen {{ $exceptionGroup->first_seen_at->diffForHumans() }}
                &middot; Last seen {{ $exceptionGroup->last_seen_at->diffForHumans() }}
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            @if($exceptionGroup->status === 'unresolved')
                <button wire:click="resolve" class="rounded-lg bg-green-600 px-4 py-2 text-sm font-medium text-white hover:bg-green-500 transition-colors">Resolve</button>
                <button wire:click="ignore" class="rounded-lg border border-gray-700 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 transition-colors">Ignore</button>
            @elseif($exceptionGroup->status === 'resolved')
                <button wire:click="unresolve" class="rounded-lg border border-gray-700 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 transition-colors">Reopen</button>
            @else
                <button wire:click="unresolve" class="rounded-lg border border-gray-700 px-4 py-2 text-sm font-medium text-gray-300 hover:bg-gray-800 transition-colors">Reopen</button>
            @endif
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-4">
        {{-- Occurrences sidebar --}}
        <div class="lg:col-span-1">
            <h2 class="text-sm font-medium text-gray-400 mb-3">Occurrences</h2>
            <div class="space-y-1 max-h-[600px] overflow-y-auto">
                @foreach($occurrences as $occ)
                    <button wire:click="selectOccurrence({{ $occ->id }})"
                        @class([
                            'w-full rounded-lg px-3 py-2 text-left text-sm transition-colors',
                            'bg-gray-800 text-white' => $selectedOccurrenceId === $occ->id,
                            'text-gray-400 hover:bg-gray-800/50 hover:text-white' => $selectedOccurrenceId !== $occ->id,
                        ])>
                        <p class="font-medium">{{ $occ->occurred_at->format('M j, H:i:s') }}</p>
                        <p class="text-xs text-gray-500 mt-0.5">
                            {{ $occ->environment ?? 'unknown' }}
                            @if($occ->hostname) &middot; {{ $occ->hostname }} @endif
                        </p>
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Occurrence detail --}}
        <div class="lg:col-span-3 space-y-6">
            @if($occurrence)
                {{-- Request Context --}}
                @if($occurrence->request_url)
                    <div class="rounded-lg border border-gray-800 bg-gray-900 p-5">
                        <h3 class="text-sm font-medium text-gray-400 mb-3">Request</h3>
                        <div class="space-y-2">
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center rounded bg-gray-800 px-2 py-0.5 text-xs font-mono font-bold text-vigil-400">{{ $occurrence->request_method }}</span>
                                <span class="text-sm text-gray-200 font-mono break-all">{{ $occurrence->request_url }}</span>
                            </div>
                            @if($occurrence->user_info)
                                <p class="text-sm text-gray-400">
                                    User: {{ $occurrence->user_info['email'] ?? $occurrence->user_info['id'] ?? 'Unknown' }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- Environment --}}
                <div class="rounded-lg border border-gray-800 bg-gray-900 p-5">
                    <h3 class="text-sm font-medium text-gray-400 mb-3">Environment</h3>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        @foreach([
                            'Environment' => $occurrence->environment,
                            'Hostname' => $occurrence->hostname,
                            'PHP' => $occurrence->php_version,
                            'Laravel' => $occurrence->laravel_version,
                        ] as $label => $value)
                            @if($value)
                                <div>
                                    <p class="text-xs text-gray-500">{{ $label }}</p>
                                    <p class="text-sm text-gray-200 mt-0.5">{{ $value }}</p>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>

                {{-- Stack Trace --}}
                <div class="rounded-lg border border-gray-800 bg-gray-900 overflow-hidden">
                    <div class="px-5 py-4 border-b border-gray-800">
                        <h3 class="text-sm font-medium text-gray-400">Stack Trace</h3>
                    </div>
                    <div class="divide-y divide-gray-800">
                        @foreach($occurrence->stack_trace ?? [] as $index => $frame)
                            <div x-data="{ open: {{ $index === 0 ? 'true' : 'false' }} }" class="group">
                                <button @click="open = !open" class="w-full px-5 py-3 text-left hover:bg-gray-800/50 transition-colors">
                                    <div class="flex items-center gap-3">
                                        <span class="shrink-0 text-xs text-gray-600 w-6 text-right">{{ $index }}</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-mono truncate">
                                                @if(!empty($frame['class']))
                                                    <span class="text-vigil-400">{{ class_basename($frame['class']) }}</span>
                                                    <span class="text-gray-500">::</span>
                                                @endif
                                                <span class="text-white">{{ $frame['function'] ?? '(unknown)' }}</span>
                                            </p>
                                            <p class="text-xs text-gray-500 truncate mt-0.5">
                                                {{ $frame['file'] ?? '(internal)' }}@if(!empty($frame['line'])):{{ $frame['line'] }}@endif
                                            </p>
                                        </div>
                                        <svg :class="open ? 'rotate-180' : ''" class="h-4 w-4 shrink-0 text-gray-500 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </button>
                                @if(!empty($frame['code_snippet']))
                                    <div x-show="open" x-cloak class="bg-gray-950 border-t border-gray-800 overflow-x-auto">
                                        <table class="w-full">
                                            @foreach($frame['code_snippet'] as $lineNum => $code)
                                                <tr @class([
                                                    'bg-red-500/10' => (int)$lineNum === (int)($frame['line'] ?? 0),
                                                ])>
                                                    <td class="select-none px-4 py-0 text-right text-xs font-mono text-gray-600 w-12 border-r border-gray-800">{{ $lineNum }}</td>
                                                    <td class="px-4 py-0 text-sm font-mono text-gray-300 whitespace-pre">{{ $code }}</td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Request Headers --}}
                @if($occurrence->request_headers)
                    <div class="rounded-lg border border-gray-800 bg-gray-900 p-5">
                        <h3 class="text-sm font-medium text-gray-400 mb-3">Request Headers</h3>
                        <div class="space-y-1">
                            @foreach($occurrence->request_headers as $name => $value)
                                <div class="flex gap-3 text-sm">
                                    <span class="shrink-0 font-mono text-gray-500">{{ $name }}:</span>
                                    <span class="font-mono text-gray-300 break-all">{{ is_array($value) ? implode(', ', $value) : $value }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Request Body --}}
                @if($occurrence->request_body)
                    <div class="rounded-lg border border-gray-800 bg-gray-900 p-5">
                        <h3 class="text-sm font-medium text-gray-400 mb-3">Request Body</h3>
                        <pre class="text-sm font-mono text-gray-300 overflow-x-auto whitespace-pre-wrap">{{ json_encode($occurrence->request_body, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) }}</pre>
                    </div>
                @endif
            @else
                <div class="rounded-lg border border-gray-800 bg-gray-900 p-12 text-center">
                    <p class="text-sm text-gray-400">Select an occurrence to view details</p>
                </div>
            @endif
        </div>
    </div>
</div>
