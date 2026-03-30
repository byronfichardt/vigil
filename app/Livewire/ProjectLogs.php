<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ProjectLogs extends Component
{
    use WithPagination;

    public Project $project;

    #[Url]
    public string $level = '';

    #[Url]
    public string $channel = '';

    #[Url]
    public string $search = '';

    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedLevel()
    {
        $this->resetPage();
    }

    public function updatedChannel()
    {
        $this->resetPage();
    }

    public function updatedDateFrom()
    {
        $this->resetPage();
    }

    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function render()
    {
        $logs = $this->project->logEntries()
            ->when($this->level, fn ($q) => $q->where('level', $this->level))
            ->when($this->channel, fn ($q) => $q->where('channel', $this->channel))
            ->when($this->search, fn ($q) => $q->where('message', 'like', "%{$this->search}%"))
            ->when($this->dateFrom, fn ($q) => $q->where('logged_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('logged_at', '<=', $this->dateTo . ' 23:59:59'))
            ->latest('logged_at')
            ->paginate(50);

        $channels = $this->project->logEntries()
            ->distinct()
            ->pluck('channel')
            ->sort()
            ->values();

        return view('livewire.project-logs', [
            'logs' => $logs,
            'channels' => $channels,
            'levels' => ['debug', 'info', 'notice', 'warning', 'error', 'critical', 'alert', 'emergency'],
        ])->title($this->project->name . ' Logs - Vigil');
    }
}
