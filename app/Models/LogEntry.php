<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'project_id',
    'level',
    'channel',
    'message',
    'context',
    'extra',
    'environment',
    'hostname',
    'request_url',
    'request_method',
    'logged_at',
])]
class LogEntry extends Model
{
    const UPDATED_AT = null;

    protected function casts(): array
    {
        return [
            'context' => 'array',
            'extra' => 'array',
            'logged_at' => 'datetime',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function levelColor(): string
    {
        return match ($this->level) {
            'debug' => 'bg-gray-500/10 text-gray-400',
            'info' => 'bg-blue-500/10 text-blue-400',
            'notice' => 'bg-cyan-500/10 text-cyan-400',
            'warning' => 'bg-yellow-500/10 text-yellow-400',
            'error' => 'bg-red-500/10 text-red-400',
            'critical' => 'bg-red-500/20 text-red-300',
            'alert' => 'bg-orange-500/10 text-orange-400',
            'emergency' => 'bg-red-500/30 text-red-200',
            default => 'bg-gray-500/10 text-gray-400',
        };
    }
}
