<?php

use Livewire\Component;
use App\Models\Aduan;

new class extends Component {
    public function with()
    {
        $totalLaporan = Aduan::count();
        $selesaiCount = Aduan::where('status_aduan', 'selesai')->count();
        $selesaiPct = $totalLaporan > 0 ? round(($selesaiCount / $totalLaporan) * 100, 1) : 0;

        return [
            'totalLaporan' => $totalLaporan,
            'selesaiCount' => $selesaiCount,
            'selesaiPct' => $selesaiPct,
        ];
    }
};
?>

<div class="w-full max-w-[1440px] mx-auto min-h-screen bg-slate-50 flex flex-col justify-start items-start overflow-hidden font-['Inter']">
    {{-- Responsive Navbar --}}
    <livewire:navbar />

    <!-- Hero Section -->
    <div class="self-stretch px-6 sm:px-12 lg:px-16 py-12 lg:py-16 bg-white border-b border-slate-200 flex flex-col lg:flex-row justify-between items-center gap-12 overflow-hidden">
        <div class="flex-1 flex flex-col justify-start items-start gap-8 max-w-2xl">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-blue-50 border border-blue-200 rounded-full text-blue-800 text-xs font-medium">
                <span class="size-2 rounded-full bg-blue-600 animate-pulse"></span>
                Platform Resmi Layanan & Pengaduan Warga Desa Sukamaju
            </div>
            <div class="flex flex-col gap-4">
                <h1 class="text-3xl sm:text-5xl font-bold text-slate-900 tracking-tight leading-tight">
                    Sampaikan Aspirasi & Masalah di Desa Anda Lebih Cepat
                </h1>
                <p class="text-slate-600 text-base sm:text-lg leading-relaxed">
                    Laporkan keluhan fasilitas jalan, sampah, posyandu, atau pelayanan publik secara transparan, mudah dilacak dengan kode tiket, dan langsung dievaluasi batas waktu responsnya oleh aparat desa.
                </p>
            </div>
            <div class="flex flex-wrap items-center gap-4">
                <a href="/aduan" class="px-6 py-3.5 bg-blue-800 hover:bg-blue-900 text-white rounded-lg font-semibold text-sm shadow-sm transition-colors flex items-center gap-2">
                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    <span>Buat Pengaduan Sekarang</span>
                </a>
                <a href="/aduan/lacak" class="px-6 py-3.5 bg-white hover:bg-slate-50 border border-slate-300 text-slate-700 rounded-lg font-semibold text-sm transition-colors flex items-center gap-2">
                    <svg class="size-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <span>Lacak Pengaduan</span>
                </a>
            </div>
            <div class="flex items-center gap-2 text-slate-500 text-sm">
                <svg class="size-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                <span>Gratis · Identitas Terlindungi · Bisa Kirim Anonim</span>
            </div>
        </div>

        <div class="w-full lg:w-[480px] bg-slate-100 rounded-2xl overflow-hidden border border-slate-200 shadow-sm flex flex-col shrink-0">
            <img class="w-full h-72 sm:h-80 object-cover" src="https://images.unsplash.com/photo-1544654803-b69140b285a1?auto=format&fit=crop&w=800&q=80" alt="Suara Desa" />
            <div class="p-5 bg-white flex items-center gap-4 border-t border-slate-100">
                <div class="size-12 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center shrink-0">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h4 class="text-sm font-semibold text-slate-900">Lebih dekat. Lebih didengar.</h4>
                    <p class="text-xs text-slate-500 mt-0.5">Warga dan petugas bersama membangun desa yang lebih baik.</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Stats Section -->
    <div class="self-stretch px-6 sm:px-12 lg:px-16 py-12 flex flex-col gap-12">
        <div class="flex flex-col gap-4">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-2">
                <h2 class="text-slate-900 text-2xl font-bold tracking-tight">Suara warga, perubahan nyata</h2>
                <span class="text-slate-500 text-xs">Data statistik terkini sistem</span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 bg-white rounded-xl shadow-xs border border-slate-200 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-sm font-medium">Total Laporan Warga</span>
                        <div class="size-8 rounded-lg bg-blue-50 text-blue-800 flex items-center justify-center">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                    </div>
                    <span class="text-slate-900 text-3xl font-bold tracking-tight">{{ $totalLaporan }}</span>
                    <span class="text-blue-800 text-xs font-medium">Pengaduan tercatat di sistem</span>
                </div>

                <div class="p-6 bg-white rounded-xl shadow-xs border border-slate-200 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-sm font-medium">Terselesaikan</span>
                        <div class="size-8 rounded-lg bg-emerald-50 text-emerald-800 flex items-center justify-center">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                    </div>
                    <span class="text-slate-900 text-3xl font-bold tracking-tight">{{ $selesaiCount }}</span>
                    <span class="text-emerald-700 text-xs font-medium">{{ $selesaiPct }}% tingkat penyelesaian</span>
                </div>

                <div class="p-6 bg-white rounded-xl shadow-xs border border-slate-200 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-500 text-sm font-medium">Penilaian AI Otomatis</span>
                        <div class="size-8 rounded-lg bg-indigo-50 text-indigo-800 flex items-center justify-center">
                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                    </div>
                    <span class="text-slate-900 text-3xl font-bold tracking-tight">Otomatis</span>
                    <span class="text-indigo-800 text-xs font-medium">Evaluasi urgensi & SLA waktu respons</span>
                </div>
            </div>
        </div>

        {{-- Langkah Mudah Melapor --}}
        <div class="flex flex-col gap-6">
            <div class="flex flex-col gap-1">
                <h2 class="text-slate-900 text-2xl font-bold tracking-tight">
                    Satu laporan, langkah yang jelas
                </h2>
                <p class="text-slate-600 text-sm leading-relaxed">
                    Tak perlu datang berkali-kali ke kantor desa. Setiap perkembangan bisa Anda pantau dari ponsel di mana saja.
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="p-5 bg-white rounded-xl border border-slate-200 flex flex-col gap-3 shadow-xs">
                    <div class="flex items-center gap-2 text-blue-800 text-xs font-bold uppercase tracking-wider">
                        <span class="size-6 rounded-full bg-blue-100 flex items-center justify-center font-bold">1</span>
                        Buat Pengaduan
                    </div>
                    <p class="text-slate-600 text-xs leading-relaxed">
                        Tuliskan masalah dan sertakan lokasi serta foto pendukung kejadian.
                    </p>
                </div>

                <div class="p-5 bg-white rounded-xl border border-slate-200 flex flex-col gap-3 shadow-xs">
                    <div class="flex items-center gap-2 text-blue-800 text-xs font-bold uppercase tracking-wider">
                        <span class="size-6 rounded-full bg-blue-100 flex items-center justify-center font-bold">2</span>
                        Simpan Kode Tiket
                    </div>
                    <p class="text-slate-600 text-xs leading-relaxed">
                        Dapatkan kode tiket unik langsung setelah mengirimkan formulir.
                    </p>
                </div>

                <div class="p-5 bg-white rounded-xl border border-slate-200 flex flex-col gap-3 shadow-xs">
                    <div class="flex items-center gap-2 text-blue-800 text-xs font-bold uppercase tracking-wider">
                        <span class="size-6 rounded-full bg-blue-100 flex items-center justify-center font-bold">3</span>
                        Pantau Progres
                    </div>
                    <p class="text-slate-600 text-xs leading-relaxed">
                        Lacak respons petugas dan sisa waktu target SLA secara transparan.
                    </p>
                </div>

                <div class="p-5 bg-white rounded-xl border border-slate-200 flex flex-col gap-3 shadow-xs">
                    <div class="flex items-center gap-2 text-blue-800 text-xs font-bold uppercase tracking-wider">
                        <span class="size-6 rounded-full bg-blue-100 flex items-center justify-center font-bold">4</span>
                        Masalah Tuntas
                    </div>
                    <p class="text-slate-600 text-xs leading-relaxed">
                        Lihat tanggapan resmi petugas dan pastikan lingkungan Anda aman kembali.
                    </p>
                </div>
            </div>
        </div>

        {{-- Eskalasi Otomatis Box --}}
        <div class="p-6 bg-blue-50 rounded-2xl border border-blue-200 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
            <div class="flex flex-col gap-2 max-w-2xl">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-800">SISTEM DISIPLIN WAKTU</span>
                <h3 class="text-xl font-bold text-blue-950">
                    Tidak dijawab? Laporan otomatis naik tingkat.
                </h3>
                <p class="text-xs sm:text-sm text-blue-900/80 leading-relaxed">
                    Jika belum ada respons hingga batas waktu yang ditentukan, pengaduan diprioritaskan dan dapat diteruskan ke tingkat yang lebih tinggi. Riwayat dan penanggung jawab tetap tercatat.
                </p>
            </div>
            <a href="/aduan" class="px-5 py-3 bg-blue-800 hover:bg-blue-900 text-white rounded-xl text-sm font-semibold shrink-0 transition-colors shadow-xs">
                Mulai Lapor Sekarang
            </a>
        </div>
    </div>

    {{-- Footer --}}
    <footer class="self-stretch px-6 sm:px-12 lg:px-16 py-12 bg-white border-t border-slate-200 flex flex-col gap-8">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="flex flex-col gap-3">
                <div class="flex items-center gap-2">
                    <div class="size-8 bg-blue-800 rounded-lg flex items-center justify-center">
                        <svg class="size-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"></path></svg>
                    </div>
                    <span class="text-blue-800 text-lg font-bold">SuaraDesa</span>
                </div>
                <p class="text-xs text-slate-500 leading-relaxed">
                    Sistem Pelayanan & Pengaduan Warga Desa Sukamaju, Kecamatan Harapan. Membangun desa yang aman, bersih, dan berkeadilan.
                </p>
            </div>

            <div class="flex flex-col gap-2 text-xs">
                <span class="font-bold text-slate-900 text-sm">Navigasi Cepat</span>
                <a href="/aduan" class="text-slate-600 hover:text-blue-800 transition-colors">Buat Pengaduan</a>
                <a href="/aduan/lacak" class="text-slate-600 hover:text-blue-800 transition-colors">Lacak Status Pengaduan</a>
                <a href="/login" class="text-slate-600 hover:text-blue-800 transition-colors">Masuk Petugas Desa</a>
            </div>

            <div class="flex flex-col gap-2 text-xs">
                <span class="font-bold text-slate-900 text-sm">Privasi & Keamanan</span>
                <span class="text-slate-500">Identitas warga terlindungi secara aman.</span>
                <span class="text-slate-500">Pilihan laporan dapat dikirimkan secara anonim.</span>
            </div>
        </div>

        <div class="pt-6 border-t border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-400">
            <span>© 2026 SuaraDesa · Desa Sukamaju</span>
            <span>Platform Pengaduan Publik</span>
        </div>
    </footer>
</div>