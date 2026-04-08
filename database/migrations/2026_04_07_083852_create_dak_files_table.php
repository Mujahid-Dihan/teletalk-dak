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
        Schema::create('dak_files', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_id')->unique(); // The pre-printed barcode
            $table->string('subject');
            $table->enum('priority', ['Normal', 'High', 'Urgent'])->default('Normal');
            $table->enum('status', ['Pending', 'In-Transit', 'Completed'])->default('Pending');
            
            // Foreign Keys linking to the departments table
            $table->foreignId('origin_department_id')->constrained('departments');
            $table->foreignId('current_department_id')->constrained('departments');
            
            $table->string('physical_location')->nullable(); // For the archive step
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dak_files');
    }
};
