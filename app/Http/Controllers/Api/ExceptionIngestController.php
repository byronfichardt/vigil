<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ExceptionGroup;
use App\Models\ExceptionOccurrence;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ExceptionIngestController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'exception_class' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'file' => ['required', 'string', 'max:255'],
            'line' => ['required', 'integer'],
            'stack_trace' => ['required', 'array'],
            'stack_trace.*.file' => ['nullable', 'string'],
            'stack_trace.*.line' => ['nullable', 'integer'],
            'stack_trace.*.class' => ['nullable', 'string'],
            'stack_trace.*.function' => ['nullable', 'string'],
            'stack_trace.*.code_snippet' => ['nullable', 'array'],
            'request_url' => ['nullable', 'string', 'max:2048'],
            'request_method' => ['nullable', 'string', 'max:10'],
            'request_headers' => ['nullable', 'array'],
            'request_body' => ['nullable', 'array'],
            'request_query_params' => ['nullable', 'array'],
            'user_info' => ['nullable', 'array'],
            'environment' => ['nullable', 'string', 'max:50'],
            'hostname' => ['nullable', 'string', 'max:255'],
            'app_version' => ['nullable', 'string', 'max:50'],
            'php_version' => ['nullable', 'string', 'max:20'],
            'laravel_version' => ['nullable', 'string', 'max:20'],
            'occurred_at' => ['nullable', 'date'],
        ]);

        $project = $request->vigil_project;

        $fingerprint = md5($validated['exception_class'].$validated['file'].$validated['line']);

        $now = Carbon::parse($validated['occurred_at'] ?? now());

        $group = ExceptionGroup::firstOrCreate(
            [
                'project_id' => $project->id,
                'fingerprint' => $fingerprint,
            ],
            [
                'exception_class' => $validated['exception_class'],
                'message' => $validated['message'],
                'file' => $validated['file'],
                'line' => $validated['line'],
                'status' => 'unresolved',
                'first_seen_at' => $now,
                'last_seen_at' => $now,
                'occurrence_count' => 0,
            ]
        );

        $group->update([
            'message' => $validated['message'],
            'last_seen_at' => $now,
            'occurrence_count' => $group->occurrence_count + 1,
        ]);

        // Re-open resolved exceptions if they recur
        if ($group->status === 'resolved') {
            $group->update(['status' => 'unresolved']);
        }

        $occurrence = ExceptionOccurrence::create([
            'exception_group_id' => $group->id,
            'message' => $validated['message'],
            'stack_trace' => $validated['stack_trace'],
            'request_url' => $validated['request_url'] ?? null,
            'request_method' => $validated['request_method'] ?? null,
            'request_headers' => $validated['request_headers'] ?? null,
            'request_body' => $validated['request_body'] ?? null,
            'request_query_params' => $validated['request_query_params'] ?? null,
            'user_info' => $validated['user_info'] ?? null,
            'environment' => $validated['environment'] ?? null,
            'hostname' => $validated['hostname'] ?? null,
            'app_version' => $validated['app_version'] ?? null,
            'php_version' => $validated['php_version'] ?? null,
            'laravel_version' => $validated['laravel_version'] ?? null,
            'occurred_at' => $now,
        ]);

        return response()->json([
            'message' => 'Exception recorded.',
            'group_id' => $group->id,
            'occurrence_id' => $occurrence->id,
        ], 201);
    }
}
