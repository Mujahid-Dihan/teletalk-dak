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
        Schema::table('dak_files', function (Blueprint $table) {
            $table->string('scanned_pdf_path')->nullable()->after('physical_location');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dak_files', function (Blueprint $table) {
            $table->dropColumn('scanned_pdf_path');
        });
    }
};
