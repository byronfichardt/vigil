<?php

namespace App\Livewire;

use App\Models\ExceptionGroup;
use App\Models\ExceptionOccurrence;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class ExceptionDetail extends Component
{
    public ExceptionGroup $exceptionGroup;
    public ?int $selectedOccurrenceId = null;

    public function mount()
    {
        $this->exceptionGroup->load('project');
        $latest = $this->exceptionGroup->occurrences()->latest('occurred_at')->first();
        $this->selectedOccurrenceId = $latest?->id;
    }

    public function selectOccurrence(int $id)
    {
        $this->selectedOccurrenceId = $id;
    }

    public function resolve()
    {
        $this->exceptionGroup->update(['status' => 'resolved']);
    }

    public function unresolve()
    {
        $this->exceptionGroup->update(['status' => 'unresolved']);
    }

    public function ignore()
    {
        $this->exceptionGroup->update(['status' => 'ignored']);
    }

    public function render()
    {
        $occurrence = $this->selectedOccurrenceId
            ? ExceptionOccurrence::find($this->selectedOccurrenceId)
            : null;

        $occurrences = $this->exceptionGroup->occurrences()
            ->latest('occurred_at')
            ->limit(50)
            ->get(['id', 'occurred_at', 'environment', 'hostname']);

        return view('livewire.exception-detail', [
            'occurrence' => $occurrence,
            'occurrences' => $occurrences,
        ])->title($this->exceptionGroup->exception_class . ' - Vigil');
    }
}
