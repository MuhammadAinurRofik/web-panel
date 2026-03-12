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
        Schema::create('projects', function (Blueprint $table) {
            $table->id('project_id'); // Primary Key [cite: 2511]
            $table->unsignedBigInteger('user_id')->nullable(); 
            $table->string('author_name')->nullable();
            $table->string('project_name');
            $table->string('project_type'); // Laravel, PHP Native, Flask

            // --- TAMBAHAN UNTUK MULTI VERSION ---
            $table->string('php_version')->nullable();    // Contoh: 7.4, 8.1, 8.3
            $table->string('python_version')->nullable(); // Contoh: 3.10, 3.12

            // --- KOLOM TAMBAHAN UNTUK OTOMATISASI ---
            $table->string('entry_point')->nullable();    // Contoh: app.py atau index.php
            $table->string('flask_instance')->nullable(); // Contoh: app atau server
            $table->boolean('need_db')->default(false);   // Apakah butuh database?
            // ----------------------------------------

            $table->string('file_path');
            $table->string('sql_path')->nullable();
            $table->string('extract_path')->nullable();
            $table->string('subdomain')->unique()->nullable(); // Unik untuk banyak data
            $table->string('db_name')->unique()->nullable();
            $table->string('db_user')->nullable();
            $table->string('db_password')->nullable();
            $table->string('status')->default('pending'); 
            $table->timestamps();

            $table->foreign('user_id')
                ->references('user_id')
                ->on('users')
                ->onDelete('set null'); // FK ke users [cite: 2512]

            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
