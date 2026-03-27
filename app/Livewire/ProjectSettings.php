<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Project Settings - Vigil')]
class ProjectSettings extends Component
{
    public Project $project;

    public function regenerateKey()
    {
        $this->project->update(['api_key' => (string) Str::uuid()]);
    }

    public function render()
    {
        return view('livewire.project-settings');
    }
}
