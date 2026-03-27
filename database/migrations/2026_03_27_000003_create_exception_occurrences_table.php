<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exception_occurrences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exception_group_id')->constrained()->cascadeOnDelete();
            $table->text('message');
            $table->json('stack_trace');
            $table->string('request_url')->nullable();
            $table->string('request_method')->nullable();
            $table->json('request_headers')->nullable();
            $table->json('request_body')->nullable();
            $table->json('request_query_params')->nullable();
            $table->json('user_info')->nullable();
            $table->string('environment')->nullable();
            $table->string('hostname')->nullable();
            $table->string('app_version')->nullable();
            $table->string('php_version')->nullable();
            $table->string('laravel_version')->nullable();
            $table->dateTime('occurred_at');
            $table->timestamps();

            $table->index('exception_group_id');
            $table->index('occurred_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exception_occurrences');
    }
};
