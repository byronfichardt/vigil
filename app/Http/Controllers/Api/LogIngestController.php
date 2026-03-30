<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LogEntry;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;

class LogIngestController extends Controller
{
    private const LEVELS = [
        'debug', 'info', 'notice', 'warning',
        'error', 'critical', 'alert', 'emergency',
    ];

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'logs' => ['required', 'array', 'max:200'],
            'logs.*.level' => ['required', 'string', Rule::in(self::LEVELS)],
            'logs.*.channel' => ['required', 'string', 'max:255'],
            'logs.*.message' => ['required', 'string', 'max:65535'],
            'logs.*.context' => ['nullable', 'array'],
            'logs.*.extra' => ['nullable', 'array'],
            'logs.*.logged_at' => ['nullable', 'date'],
            'environment' => ['nullable', 'string', 'max:50'],
            'hostname' => ['nullable', 'string', 'max:255'],
            'request_url' => ['nullable', 'string', 'max:2048'],
            'request_method' => ['nullable', 'string', 'max:10'],
        ]);

        $project = $request->vigil_project;
        $now = now();

        try {
            $entries = collect($validated['logs'])->map(fn (array $log) => [
                'project_id' => $project->id,
                'level' => $log['level'],
                'channel' => $log['channel'],
                'message' => $log['message'],
                'context' => isset($log['context']) ? json_encode($log['context']) : null,
                'extra' => isset($log['extra']) ? json_encode($log['extra']) : null,
                'environment' => $validated['environment'] ?? null,
                'hostname' => $validated['hostname'] ?? null,
                'request_url' => $validated['request_url'] ?? null,
                'request_method' => $validated['request_method'] ?? null,
                'logged_at' => Carbon::parse($log['logged_at'] ?? $now),
                'created_at' => $now,
            ]);

            LogEntry::insert($entries->toArray());
        } catch (\Throwable $e) {
            return response()->json([
                'message' => 'Failed to store logs. Please try again later.',
            ], 503);
        }

        return response()->json([
            'message' => 'Logs recorded.',
            'count' => $entries->count(),
        ], 201);
    }
}
