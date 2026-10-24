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
        Schema::table('aduan', function (Blueprint $table) {
            $table->text('tanggapan')->nullable()->after('status_aduan');
            $table->string('petugas_penanggung_jawab', 100)->nullable()->after('tanggapan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aduan', function (Blueprint $table) {
            $table->dropColumn(['tanggapan', 'petugas_penanggung_jawab']);
        });
    }
};
