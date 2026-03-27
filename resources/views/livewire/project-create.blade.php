<div class="mx-auto max-w-lg">
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-white">New Project</h1>
        <p class="mt-1 text-sm text-gray-400">Add an application to monitor</p>
    </div>

    <form wire:submit="save" class="space-y-6">
        <div>
            <label for="name" class="block text-sm font-medium text-gray-300">Project Name</label>
            <input type="text" wire:model="name" id="name" placeholder="e.g. My Laravel App"
                class="mt-2 block w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
            @error('name')
                <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex items-center gap-4">
            <button type="submit"
                class="rounded-lg bg-vigil-600 px-6 py-3 text-sm font-semibold text-white hover:bg-vigil-500 transition-colors">
                Create Project
            </button>
            <a href="{{ route('projects.index') }}" wire:navigate class="text-sm text-gray-400 hover:text-white">Cancel</a>
        </div>
    </form>
</div>
