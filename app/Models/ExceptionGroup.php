<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'project_id',
    'exception_class',
    'fingerprint',
    'message',
    'file',
    'line',
    'status',
    'first_seen_at',
    'last_seen_at',
    'occurrence_count',
])]
class ExceptionGroup extends Model
{
    protected function casts(): array
    {
        return [
            'first_seen_at' => 'datetime',
            'last_seen_at' => 'datetime',
            'occurrence_count' => 'integer',
            'line' => 'integer',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function occurrences(): HasMany
    {
        return $this->hasMany(ExceptionOccurrence::class);
    }
}
