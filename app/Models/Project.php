<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

#[Fillable(['name', 'created_by'])]
class Project extends Model
{
    protected static function booted(): void
    {
        static::creating(function (Project $project) {
            if (empty($project->api_key)) {
                $project->api_key = (string) Str::uuid();
            }
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function exceptionGroups(): HasMany
    {
        return $this->hasMany(ExceptionGroup::class);
    }
}
