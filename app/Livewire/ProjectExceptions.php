<?php

namespace App\Livewire;

use App\Models\ExceptionGroup;
use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProjectExceptions extends Component
{
    use WithPagination;

    public Project $project;

    #[Url]
    public string $status = 'unresolved';

    #[Url]
    public string $search = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function resolve(ExceptionGroup $group)
    {
        $group->update(['status' => 'resolved']);
    }

    public function unresolve(ExceptionGroup $group)
    {
        $group->update(['status' => 'unresolved']);
    }

    public function ignore(ExceptionGroup $group)
    {
        $group->update(['status' => 'ignored']);
    }

    public function render()
    {
        $exceptions = $this->project->exceptionGroups()
            ->when($this->status !== 'all', fn ($q) => $q->where('status', $this->status))
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('exception_class', 'like', "%{$this->search}%")
                    ->orWhere('message', 'like', "%{$this->search}%")
                    ->orWhere('file', 'like', "%{$this->search}%");
            }))
            ->latest('last_seen_at')
            ->paginate(25);

        return view('livewire.project-exceptions', [
            'exceptions' => $exceptions,
        ])->title($this->project->name . ' - Vigil');
    }
}
