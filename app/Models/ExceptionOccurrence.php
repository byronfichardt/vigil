<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'exception_group_id',
    'message',
    'stack_trace',
    'request_url',
    'request_method',
    'request_headers',
    'request_body',
    'request_query_params',
    'user_info',
    'environment',
    'hostname',
    'app_version',
    'php_version',
    'laravel_version',
    'occurred_at',
])]
class ExceptionOccurrence extends Model
{
    protected function casts(): array
    {
        return [
            'stack_trace' => 'array',
            'request_headers' => 'array',
            'request_body' => 'array',
            'request_query_params' => 'array',
            'user_info' => 'array',
            'occurred_at' => 'datetime',
        ];
    }

    public function exceptionGroup(): BelongsTo
    {
        return $this->belongsTo(ExceptionGroup::class);
    }
}
