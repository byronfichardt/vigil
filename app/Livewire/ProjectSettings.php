<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Project Settings - Vigil')]
class ProjectSettings extends Component
{
    public Project $project;

    #[Validate('nullable|integer|min:1|max:365')]
    public ?int $logRetentionDays = null;

    public function mount()
    {
        $this->logRetentionDays = $this->project->log_retention_days;
    }

    public function regenerateKey()
    {
        $this->project->update(['api_key' => (string) Str::uuid()]);
    }

    public function saveLogRetention()
    {
        $this->validate();

        $this->project->update(['log_retention_days' => $this->logRetentionDays ?: null]);
    }

    public function render()
    {
        return view('livewire.project-settings');
    }
}
