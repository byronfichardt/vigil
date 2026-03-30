<div class="mx-auto max-w-2xl">
    <div class="mb-8">
        <div class="flex items-center gap-2 text-sm text-gray-400 mb-4">
            <a href="{{ route('projects.show', $project) }}" wire:navigate class="hover:text-white">{{ $project->name }}</a>
            <span>/</span>
            <span class="text-gray-300">Settings</span>
        </div>
        <h1 class="text-2xl font-bold text-white">Project Settings</h1>
    </div>

    {{-- API Key --}}
    <div class="rounded-lg border border-gray-800 bg-gray-900 p-6 mb-6">
        <h2 class="text-lg font-semibold text-white mb-4">API Key</h2>
        <p class="text-sm text-gray-400 mb-4">Use this key in your client application to send exceptions to Vigil.</p>

        <div class="flex items-center gap-3">
            <code class="flex-1 rounded-lg bg-gray-800 border border-gray-700 px-4 py-3 text-sm text-gray-200 font-mono select-all">{{ $project->api_key }}</code>
            <button wire:click="regenerateKey" wire:confirm="Regenerate API key? The old key will stop working immediately."
                class="shrink-0 rounded-lg border border-gray-700 px-4 py-3 text-sm font-medium text-gray-300 hover:bg-gray-800 hover:text-white transition-colors">
                Regenerate
            </button>
        </div>
    </div>

    {{-- Log Retention --}}
    <div class="rounded-lg border border-gray-800 bg-gray-900 p-6 mb-6">
        <h2 class="text-lg font-semibold text-white mb-4">Log Retention</h2>
        <p class="text-sm text-gray-400 mb-4">Configure how long log entries are kept before automatic pruning. Leave empty to use the global default ({{ config('vigil.log_retention_days', 30) }} days).</p>

        <form wire:submit="saveLogRetention" class="flex items-end gap-3">
            <div>
                <label for="logRetentionDays" class="block text-sm font-medium text-gray-300 mb-1">Retention (days)</label>
                <input type="number" id="logRetentionDays" wire:model="logRetentionDays" min="1" max="365" placeholder="{{ config('vigil.log_retention_days', 30) }}"
                    class="w-32 rounded-lg border border-gray-700 bg-gray-800 px-4 py-2.5 text-sm text-white placeholder-gray-500 focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
            </div>
            <button type="submit"
                class="rounded-lg bg-vigil-600 px-4 py-2.5 text-sm font-medium text-white hover:bg-vigil-500 transition-colors">
                Save
            </button>
        </form>
        @error('logRetentionDays')
            <p class="mt-2 text-sm text-red-400">{{ $message }}</p>
        @enderror
    </div>

    {{-- Setup Instructions --}}
    <div class="rounded-lg border border-gray-800 bg-gray-900 p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Setup Instructions</h2>

        <div class="space-y-4">
            <div>
                <p class="text-sm font-medium text-gray-300 mb-2">1. Install the client package</p>
                <pre class="rounded-lg bg-gray-800 border border-gray-700 p-4 text-sm text-gray-200 overflow-x-auto"><code>composer require vigil/client</code></pre>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-300 mb-2">2. Add to your <code class="text-vigil-400">.env</code></p>
                <pre class="rounded-lg bg-gray-800 border border-gray-700 p-4 text-sm text-gray-200 overflow-x-auto"><code>VIGIL_URL={{ url('/') }}
VIGIL_KEY={{ $project->api_key }}</code></pre>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-300 mb-2">3. That's it!</p>
                <p class="text-sm text-gray-400">Exceptions and logs will be automatically captured and sent to Vigil. By default, logs at <code class="text-vigil-400">warning</code> level and above are forwarded. Publish the config to customize:</p>
                <pre class="mt-2 rounded-lg bg-gray-800 border border-gray-700 p-4 text-sm text-gray-200 overflow-x-auto"><code>php artisan vendor:publish --tag=vigil-config</code></pre>
            </div>
        </div>
    </div>
</div>
