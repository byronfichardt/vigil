<?php

namespace App\Livewire;

use App\Models\ExceptionGroup;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Dashboard - Vigil')]
class Dashboard extends Component
{
    public function render()
    {
        $projects = Project::withCount([
            'exceptionGroups',
            'exceptionGroups as unresolved_count' => fn ($q) => $q->where('status', 'unresolved'),
        ])->latest()->get();

        $totalUnresolved = ExceptionGroup::where('status', 'unresolved')->count();
        $last24h = ExceptionGroup::where('last_seen_at', '>=', now()->subDay())->count();

        return view('livewire.dashboard', [
            'projects' => $projects,
            'totalUnresolved' => $totalUnresolved,
            'last24h' => $last24h,
        ]);
    }
}
