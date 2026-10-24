<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 3. ADUAN
        Schema::create('aduan', function (Blueprint $table) {
            $table->id('id_aduan');
            $table->string('kode_tiket', 40)->unique();
            $table->text('judul')->nullable();
            $table->text('deskripsi_aduan');
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->text('alamat')->nullable();
            $table->string('nohp', 20)->nullable();
            $table->enum('status_aduan', [
                'diajukan',
                'diproses',
                'diteruskan',
                'selesai',
                'ditolak',
            ])->default('diajukan')->index();
            $table->dateTime('waktu_pengaduan')->useCurrent()->index();
            $table->timestamps();

            $table->index(['latitude', 'longitude'], 'idx_aduan_lokasi');
        });

        // 4. PENILAIAN_PRIORITAS
        Schema::create('penilaian_prioritas', function (Blueprint $table) {
            $table->id('id_penilaian');
            $table->foreignId('id_aduan')
                ->unique()
                ->constrained('aduan', 'id_aduan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('nilai_urgensi', 5, 2);
            $table->decimal('nilai_dampak', 5, 2);
            $table->decimal('nilai_jumlah_warga', 5, 2);
            $table->decimal('bobot_urgensi', 5, 4)->default(0.4000);
            $table->decimal('bobot_dampak', 5, 4)->default(0.3500);
            $table->decimal('bobot_jumlah_warga', 5, 4)->default(0.2500);
            $table->decimal('skor_prioritas', 6, 3);
            $table->enum('tingkat_prioritas', [
                'rendah',
                'sedang',
                'tinggi',
                'darurat',
            ]);
            $table->dateTime('waktu_penilaian')->useCurrent();
            $table->timestamps();
        });

        // 5. TARGET_RESPON
        Schema::create('target_respon', function (Blueprint $table) {
            $table->id('id_target');
            $table->foreignId('id_penilaian')
                ->unique()
                ->constrained('penilaian_prioritas', 'id_penilaian')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->unsignedInteger('batas_respon_menit');
            $table->dateTime('tenggat_respon')->index();
            $table->dateTime('waktu_respon')->nullable();
            $table->enum('status_respon', [
                'menunggu',
                'direspons',
                'terlambat',
            ])->default('menunggu')->index();
            $table->timestamps();
        });

        // 6. LAPORAN
        Schema::create('laporan', function (Blueprint $table) {
            $table->id('id_laporan');
            $table->foreignId('id_aduan')
                ->unique()
                ->constrained('aduan', 'id_aduan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('id_penilaian')
                ->unique()
                ->constrained('penilaian_prioritas', 'id_penilaian')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('id_target')
                ->unique()
                ->constrained('target_respon', 'id_target')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->timestamps();
        });

        // 8. VIEW COUNTDOWN
        DB::statement("
            CREATE OR REPLACE VIEW v_countdown_respon AS
            SELECT
                a.id_aduan,
                a.kode_tiket,
                a.deskripsi_aduan,
                a.status_aduan,
                pp.id_penilaian,
                pp.skor_prioritas,
                pp.tingkat_prioritas,
                tr.id_target,
                tr.batas_respon_menit,
                tr.tenggat_respon,
                tr.waktu_respon,
                CASE
                    WHEN tr.waktu_respon IS NOT NULL THEN 'direspons'
                    WHEN CURRENT_TIMESTAMP >= tr.tenggat_respon THEN 'terlambat'
                    ELSE 'menunggu'
                END AS status_countdown,
                CASE
                    WHEN tr.waktu_respon IS NOT NULL THEN 0
                    ELSE GREATEST(TIMESTAMPDIFF(SECOND, CURRENT_TIMESTAMP, tr.tenggat_respon), 0)
                END AS sisa_detik
            FROM aduan a
            LEFT JOIN penilaian_prioritas pp ON pp.id_aduan = a.id_aduan
            LEFT JOIN target_respon tr ON tr.id_penilaian = pp.id_penilaian
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP VIEW IF EXISTS v_countdown_respon');
        Schema::dropIfExists('laporan');
        Schema::dropIfExists('target_respon');
        Schema::dropIfExists('penilaian_prioritas');
        Schema::dropIfExists('aduan');
    }
};
