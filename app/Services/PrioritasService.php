<?php

namespace App\Services;

use App\Models\Aduan;
use App\Models\PenilaianPrioritas;
use App\Models\TargetRespon;
use App\Models\Laporan;

/**
 * Service AI: Menilai urgensi, dampak, jumlah warga terdampak,
 * menghitung skor prioritas berdasarkan bobot,
 * menentukan tingkat prioritas, dan menentukan batas waktu respons.
 *
 * Alur sesuai Activity Diagram (lane AI):
 *  1. Menilai Tingkat Urgensi, Dampak Permasalahan, Menilai Jumlah Warga Terdampak
 *  2. Menghitung skor prioritas berdasarkan bobot
 *  3. Menentukan tingkat prioritas
 *  4. Menentukan batas waktu respons
 *  5. Menyimpan ke tabel penilaian_prioritas, target_respon, laporan
 */
class PrioritasService
{
    // Bobot default sesuai schema
    const BOBOT_URGENSI = 0.4000;
    const BOBOT_DAMPAK = 0.3500;
    const BOBOT_JUMLAH_WARGA = 0.2500;

    // Kata kunci untuk penilaian AI berbasis teks deskripsi
    protected array $keywordsUrgensi = [
        'darurat' => 10, 'bahaya' => 9, 'berbahaya' => 9, 'membahayakan' => 9,
        'mendesak' => 8, 'segera' => 8, 'kritis' => 9, 'ambruk' => 9,
        'roboh' => 9, 'banjir' => 8, 'longsor' => 9, 'kebakaran' => 10,
        'runtuh' => 9, 'putus' => 7, 'bocor' => 6, 'rusak' => 5,
        'berlubang' => 5, 'padam' => 6, 'mati' => 5, 'tersumbat' => 5,
        'retak' => 6, 'kotor' => 3, 'bau' => 4, 'menumpuk' => 4,
        'antrean' => 3, 'lambat' => 3, 'lama' => 3,
    ];

    protected array $keywordsDampak = [
        'kecelakaan' => 9, 'cedera' => 8, 'sakit' => 7, 'penyakit' => 7,
        'terluka' => 8, 'meninggal' => 10, 'korban' => 9, 'terjebak' => 8,
        'terisolasi' => 7, 'terputus' => 7, 'tidak bisa lewat' => 6,
        'tidak bisa melintas' => 6, 'genangan' => 5, 'tergenang' => 5,
        'mengancam' => 7, 'merugikan' => 5, 'mengganggu' => 4,
        'terhambat' => 4, 'macet' => 5, 'gelap' => 4, 'tidak aman' => 6,
    ];

    protected array $keywordsJumlahWarga = [
        'seluruh warga' => 9, 'semua warga' => 9, 'banyak warga' => 7,
        'seluruh rt' => 8, 'semua rt' => 8, 'puluhan' => 6,
        'ratusan' => 8, 'ribuan' => 10, 'umum' => 5, 'publik' => 5,
        'masyarakat' => 6, 'pengendara' => 5, 'pejalan kaki' => 4,
        'anak-anak' => 5, 'lansia' => 5, 'sekolah' => 5,
        'pasar' => 6, 'masjid' => 5, 'desa' => 6,
    ];

    /**
     * Proses penilaian otomatis untuk sebuah aduan.
     * Sesuai Activity Diagram: AI menilai → hitung skor → tentukan prioritas → tentukan batas respon → simpan ke DB.
     */
    public function proses(Aduan $aduan): Laporan
    {
        // 1. Menilai Tingkat Urgensi, Dampak, Jumlah Warga Terdampak
        $teks = strtolower($aduan->deskripsi_aduan . ' ' . $aduan->alamat);

        $nilaiUrgensi = $this->hitungNilai($teks, $this->keywordsUrgensi);
        $nilaiDampak = $this->hitungNilai($teks, $this->keywordsDampak);
        $nilaiJumlahWarga = $this->hitungNilai($teks, $this->keywordsJumlahWarga);

        // 2. Menghitung skor prioritas berdasarkan bobot
        $skorPrioritas = round(
            ($nilaiUrgensi * self::BOBOT_URGENSI) +
            ($nilaiDampak * self::BOBOT_DAMPAK) +
            ($nilaiJumlahWarga * self::BOBOT_JUMLAH_WARGA),
            3
        );

        // 3. Menentukan tingkat prioritas
        $tingkatPrioritas = $this->tentukanTingkat($skorPrioritas);

        // 4. Menentukan batas waktu respons
        $batasResponMenit = $this->tentukanBatasRespon($tingkatPrioritas);
        $tenggat = now()->addMinutes($batasResponMenit);

        // Simpan ke tabel penilaian_prioritas
        $penilaian = PenilaianPrioritas::create([
            'id_aduan' => $aduan->id_aduan,
            'nilai_urgensi' => $nilaiUrgensi,
            'nilai_dampak' => $nilaiDampak,
            'nilai_jumlah_warga' => $nilaiJumlahWarga,
            'bobot_urgensi' => self::BOBOT_URGENSI,
            'bobot_dampak' => self::BOBOT_DAMPAK,
            'bobot_jumlah_warga' => self::BOBOT_JUMLAH_WARGA,
            'skor_prioritas' => $skorPrioritas,
            'tingkat_prioritas' => $tingkatPrioritas,
            'waktu_penilaian' => now(),
        ]);

        // Simpan ke tabel target_respon
        $target = TargetRespon::create([
            'id_penilaian' => $penilaian->id_penilaian,
            'batas_respon_menit' => $batasResponMenit,
            'tenggat_respon' => $tenggat,
            'status_respon' => 'menunggu',
        ]);

        // 5. Menyimpan hubungan data pada LAPORAN
        $laporan = Laporan::create([
            'id_aduan' => $aduan->id_aduan,
            'id_penilaian' => $penilaian->id_penilaian,
            'id_target' => $target->id_target,
        ]);

        return $laporan;
    }

    /**
     * Hitung nilai (1-10) berdasarkan keyword matching dari teks deskripsi.
     */
    protected function hitungNilai(string $teks, array $keywords): float
    {
        $maxScore = 1; // Minimum score

        foreach ($keywords as $keyword => $score) {
            if (str_contains($teks, $keyword)) {
                $maxScore = max($maxScore, $score);
            }
        }

        // Tambahkan sedikit variasi berdasarkan panjang teks (lebih detail = skor sedikit lebih tinggi)
        $lengthBonus = min(strlen($teks) / 500, 1.0); // max +1
        $finalScore = min($maxScore + $lengthBonus, 10.0);

        return round($finalScore, 2);
    }

    /**
     * Menentukan tingkat prioritas berdasarkan skor.
     */
    protected function tentukanTingkat(float $skor): string
    {
        if ($skor >= 8.0) return 'darurat';
        if ($skor >= 5.5) return 'tinggi';
        if ($skor >= 3.0) return 'sedang';
        return 'rendah';
    }

    /**
     * Menentukan batas waktu respons (dalam menit) berdasarkan tingkat prioritas.
     */
    protected function tentukanBatasRespon(string $tingkat): int
    {
        return match ($tingkat) {
            'darurat' => 60 * 6,         // 6 jam
            'tinggi'  => 60 * 24,        // 24 jam (1 hari)
            'sedang'  => 60 * 24 * 3,    // 72 jam (3 hari)
            'rendah'  => 60 * 24 * 7,    // 168 jam (7 hari)
            default   => 60 * 24 * 3,
        };
    }
}
