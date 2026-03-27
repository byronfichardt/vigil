<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('layouts.app')]
#[Title('New Project - Vigil')]
class ProjectCreate extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    public function save()
    {
        $this->validate();

        $project = Project::create([
            'name' => $this->name,
            'created_by' => auth()->id(),
        ]);

        return $this->redirect(route('projects.settings', $project), navigate: true);
    }

    public function render()
    {
        return view('livewire.project-create');
    }
}
