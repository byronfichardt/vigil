<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('log_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('level', 16);
            $table->string('channel');
            $table->text('message');
            $table->json('context')->nullable();
            $table->json('extra')->nullable();
            $table->string('environment', 50)->nullable();
            $table->string('hostname')->nullable();
            $table->string('request_url', 2048)->nullable();
            $table->string('request_method', 10)->nullable();
            $table->dateTime('logged_at');
            $table->timestamp('created_at')->nullable();

            $table->index(['project_id', 'logged_at']);
            $table->index('logged_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('log_entries');
    }
};
