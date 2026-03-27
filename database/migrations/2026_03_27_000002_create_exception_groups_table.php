<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exception_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->string('exception_class')->index();
            $table->string('fingerprint');
            $table->text('message');
            $table->string('file');
            $table->integer('line');
            $table->string('status')->default('unresolved')->index();
            $table->dateTime('first_seen_at');
            $table->dateTime('last_seen_at');
            $table->integer('occurrence_count')->default(0);
            $table->timestamps();

            $table->unique(['project_id', 'fingerprint']);
            $table->index('fingerprint');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exception_groups');
    }
};
