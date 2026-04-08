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
        Schema::create('file_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dak_file_id')->constrained('dak_files')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users'); // Who moved it?
            
            // Where did it come from and go to? (Nullable because creation has no 'from', archival has no 'to')
            $table->foreignId('from_department_id')->nullable()->constrained('departments');
            $table->foreignId('to_department_id')->nullable()->constrained('departments');
            
            $table->string('action'); // e.g., 'Initiated', 'Forwarded', 'Archived'
            $table->text('remarks')->nullable(); // Optional note from the officer
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('file_movements');
    }
};
