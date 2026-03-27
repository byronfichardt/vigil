<div>
    <div class="mb-8 flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-white">Projects</h1>
            <p class="mt-1 text-sm text-gray-400">Manage your monitored applications</p>
        </div>
        <a href="{{ route('projects.create') }}" wire:navigate
            class="rounded-lg bg-vigil-600 px-4 py-2 text-sm font-medium text-white hover:bg-vigil-500 transition-colors">
            New Project
        </a>
    </div>

    <div class="overflow-hidden rounded-lg border border-gray-800">
        <table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-900">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">API Key</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Exceptions</th>
                    <th class="px-6 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-400">Created</th>
                    <th class="px-6 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-400">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-800 bg-gray-950">
                @forelse($projects as $project)
                    <tr class="hover:bg-gray-900/50">
                        <td class="whitespace-nowrap px-6 py-4">
                            <a href="{{ route('projects.show', $project) }}" wire:navigate class="font-medium text-white hover:text-vigil-400">
                                {{ $project->name }}
                            </a>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4">
                            <code class="rounded bg-gray-800 px-2 py-1 text-xs text-gray-300">{{ Str::limit($project->api_key, 16) }}</code>
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-300">
                            {{ $project->exception_groups_count }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-sm text-gray-400">
                            {{ $project->created_at->diffForHumans() }}
                        </td>
                        <td class="whitespace-nowrap px-6 py-4 text-right text-sm">
                            <a href="{{ route('projects.settings', $project) }}" wire:navigate class="text-gray-400 hover:text-white mr-4">Settings</a>
                            <button wire:click="delete({{ $project->id }})" wire:confirm="Delete {{ $project->name }}? This removes all exception data."
                                class="text-red-400 hover:text-red-300">Delete</button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-sm text-gray-400">
                            No projects yet. Create one to get started.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
