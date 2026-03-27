<div class="flex min-h-full items-center justify-center px-4 py-12">
    <div class="w-full max-w-sm space-y-8">
        <div class="text-center">
            <svg class="mx-auto h-12 w-12 text-pink-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2z"/>
                <circle cx="12" cy="12" r="3" fill="currentColor"/>
                <path d="M12 2v4M12 18v4M2 12h4M18 12h4" stroke-linecap="round"/>
            </svg>
            <h2 class="mt-4 text-2xl font-bold text-white">Welcome to Vigil</h2>
            <p class="mt-2 text-sm text-gray-400">Create your admin account to get started</p>
        </div>

        @if ($errors->any())
            <div class="rounded-lg bg-red-500/10 border border-red-500/20 p-4">
                @foreach ($errors->all() as $error)
                    <p class="text-sm text-red-400">{{ $error }}</p>
                @endforeach
            </div>
        @endif

        <form wire:submit="save" class="space-y-6">
            <div>
                <label for="name" class="block text-sm font-medium text-gray-300">Name</label>
                <input type="text" wire:model="name" id="name" required autofocus
                    class="mt-2 block w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-gray-300">Email</label>
                <input type="email" wire:model="email" id="email" required
                    class="mt-2 block w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-gray-300">Password</label>
                <input type="password" wire:model="password" id="password" required
                    class="mt-2 block w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-300">Confirm Password</label>
                <input type="password" wire:model="password_confirmation" id="password_confirmation" required
                    class="mt-2 block w-full rounded-lg border border-gray-700 bg-gray-800 px-4 py-3 text-white placeholder-gray-500 focus:border-pink-500 focus:outline-none focus:ring-1 focus:ring-pink-500">
            </div>

            <button type="submit"
                class="w-full rounded-lg bg-pink-600 px-4 py-3 text-sm font-semibold text-white hover:bg-pink-500 focus:outline-none focus:ring-2 focus:ring-pink-500 focus:ring-offset-2 focus:ring-offset-gray-950 transition-colors">
                Create Account
            </button>
        </form>
    </div>
</div>
