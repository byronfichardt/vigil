<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">Dashboard</h1>
        <p class="mt-1 text-sm text-gray-400">Overview of all monitored applications</p>
    </div>

    {{-- Stats --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-800 bg-gray-900 p-6">
            <p class="text-sm font-medium text-gray-400">Total Projects</p>
            <p class="mt-2 text-3xl font-bold text-white">{{ $projects->count() }}</p>
        </div>
        <div class="rounded-lg border border-gray-800 bg-gray-900 p-6">
            <p class="text-sm font-medium text-gray-400">Unresolved Exceptions</p>
            <p class="mt-2 text-3xl font-bold text-red-400">{{ $totalUnresolved }}</p>
        </div>
        <div class="rounded-lg border border-gray-800 bg-gray-900 p-6">
            <p class="text-sm font-medium text-gray-400">Active Last 24h</p>
            <p class="mt-2 text-3xl font-bold text-yellow-400">{{ $last24h }}</p>
        </div>
        <div class="rounded-lg border border-gray-800 bg-gray-900 p-6">
            <p class="text-sm font-medium text-gray-400">Logs Last 24h</p>
            <p class="mt-2 text-3xl font-bold text-blue-400">{{ $logsLast24h }}</p>
        </div>
    </div>

    {{-- Project Cards --}}
    <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-semibold text-white">Projects</h2>
        <a href="{{ route('projects.create') }}" wire:navigate
            class="rounded-lg bg-vigil-600 px-4 py-2 text-sm font-medium text-white hover:bg-vigil-500 transition-colors">
            New Project
        </a>
    </div>

    @if($projects->isEmpty())
        <div class="rounded-lg border border-gray-800 bg-gray-900 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
            </svg>
            <h3 class="mt-4 text-sm font-medium text-white">No projects yet</h3>
            <p class="mt-2 text-sm text-gray-400">Get started by creating your first project.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($projects as $project)
                <a href="{{ route('projects.show', $project) }}" wire:navigate
                    class="group rounded-lg border border-gray-800 bg-gray-900 p-6 hover:border-gray-700 transition-colors">
                    <div class="flex items-center justify-between">
                        <h3 class="font-semibold text-white group-hover:text-vigil-400 transition-colors">{{ $project->name }}</h3>
                        @if($project->unresolved_count > 0)
                            <span class="inline-flex items-center rounded-full bg-red-500/10 px-2.5 py-0.5 text-xs font-medium text-red-400">
                                {{ $project->unresolved_count }}
                            </span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-green-500/10 px-2.5 py-0.5 text-xs font-medium text-green-400">
                                Clear
                            </span>
                        @endif
                    </div>
                    <p class="mt-3 text-sm text-gray-400">
                        {{ $project->exception_groups_count }} exception{{ $project->exception_groups_count !== 1 ? 's' : '' }} tracked
                    </p>
                </a>
            @endforeach
        </div>
    @endif
</div>
