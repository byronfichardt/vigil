<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('Projects - Vigil')]
class ProjectIndex extends Component
{
    public function delete(Project $project)
    {
        $project->exceptionGroups()->each(function ($group) {
            $group->occurrences()->delete();
        });
        $project->exceptionGroups()->delete();
        $project->delete();
    }

    public function render()
    {
        return view('livewire.project-index', [
            'projects' => Project::withCount('exceptionGroups')->latest()->get(),
        ]);
    }
}
