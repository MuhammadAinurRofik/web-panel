<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('deployment_logs', function (Blueprint $table) {
            $table->id('log_id'); // Primary Key [cite: 2526]
            $table->foreignId('project_id')->constrained('projects', 'project_id')->onDelete('cascade');
            $table->string('process'); // Contoh: 'Extract ZIP', 'Create DB' [cite: 2530]
            $table->string('status'); // 'Success' atau 'Failed' [cite: 2531]
            $table->text('message')->nullable(); // Detail error [cite: 2532]
            $table->timestamp('timestamp')->useCurrent(); // Waktu kejadian [cite: 2533]
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deployment_logs');
    }
};
