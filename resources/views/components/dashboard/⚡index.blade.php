<?php

use Livewire\Component;
use App\Models\Aduan;
use App\Models\PenilaianPrioritas;
use Illuminate\Support\Facades\DB;

new class extends Component {
    public $peran = 'Admin Desa';
    public $wilayah = 'Desa Sukamaju · Semua RT';
    public $periode = 'Semua Periode';

    public function with()
    {
        $totalCount = Aduan::count();
        $menungguCount = Aduan::where('status_aduan', 'diajukan')->count();
        $diprosesCount = Aduan::where('status_aduan', 'diproses')->count();
        $selesaiCount = Aduan::where('status_aduan', 'selesai')->count();
        $eskalasiCount = Aduan::whereIn('status_aduan', ['diteruskan', 'ditolak'])->count();

        // Recent aduans with relations
        $recentAduans = Aduan::with(['penilaianPrioritas.targetRespon'])
            ->latest('waktu_pengaduan')
            ->take(5)
            ->get();

        // Priority ticket for today
        $topPriority = Aduan::with(['penilaianPrioritas.targetRespon'])
            ->whereIn('status_aduan', ['diajukan', 'diproses'])
            ->leftJoin('penilaian_prioritas', 'aduan.id_aduan', '=', 'penilaian_prioritas.id_aduan')
            ->orderByDesc('penilaian_prioritas.skor_prioritas')
            ->select('aduan.*')
            ->first();

        // Category breakdown calculation
        $kategoriMap = [
            'Infrastruktur' => ['jalan', 'lampu', 'jembatan', 'aspal', 'lubang', 'gorong', 'drainase', 'rusak'],
            'Kebersihan' => ['sampah', 'limbah', 'bau', 'kotor', 'saluran', 'selokan', 'sungai', 'tersumbat'],
            'Kesehatan' => ['posyandu', 'puskesmas', 'sakit', 'wabah', 'jentik', 'balita', 'lansia', 'obat'],
            'Keamanan' => ['ronda', 'poskamling', 'maling', 'pencurian', 'ribut', 'kehilangan', 'kamtibmas'],
            'Administrasi' => ['surat', 'ktp', 'kk', 'domisili', 'pelayanan', 'antrean', 'bansos', 'bantuan'],
        ];

        $kategoriStats = [
            'Infrastruktur' => 0,
            'Kebersihan' => 0,
            'Kesehatan' => 0,
            'Keamanan' => 0,
            'Administrasi' => 0,
            'Lainnya' => 0,
        ];

        $allAduans = Aduan::all();
        foreach ($allAduans as $item) {
            $matched = false;
            $text = strtolower(($item->judul_aduan ?? '') . ' ' . $item->deskripsi_aduan);
            foreach ($kategoriMap as $kat => $keywords) {
                foreach ($keywords as $kw) {
                    if (str_contains($text, $kw)) {
                        $kategoriStats[$kat]++;
                        $matched = true;
                        break 2;
                    }
                }
            }
            if (!$matched) {
                $kategoriStats['Lainnya']++;
            }
        }

        // Monthly count for the last 6 months
        $months = [];
        $monthCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthName = $date->translatedFormat('M');
            $months[] = $monthName;
            $monthCounts[] = Aduan::whereYear('waktu_pengaduan', $date->year)
                ->whereMonth('waktu_pengaduan', $date->month)
                ->count();
        }

        return [
            'totalCount' => $totalCount,
            'menungguCount' => $menungguCount,
            'diprosesCount' => $diprosesCount,
            'selesaiCount' => $selesaiCount,
            'eskalasiCount' => $eskalasiCount,
            'recentAduans' => $recentAduans,
            'topPriority' => $topPriority,
            'kategoriStats' => $kategoriStats,
            'months' => $months,
            'monthCounts' => $monthCounts,
        ];
    }
};
?>

<div class="w-full max-w-[1440px] mx-auto min-h-screen bg-slate-50 flex flex-col justify-start items-start">
    <div class="self-stretch flex flex-col md:flex-row justify-start items-stretch min-h-screen">
        {{-- Reusable Refactored Sidebar --}}
        <x-sidebar />

        {{-- Main Dashboard Body --}}
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50">
            {{-- Reusable Dashboard Topbar with mobile toggle --}}
            <x-dashboard-header title="Dashboard Pelayanan Warga" badge="Desa Sukamaju" />

            <div class="p-6 sm:p-8 flex flex-col gap-6 font-['Inter']">
                {{-- Banner Title --}}
                <div class="flex flex-col gap-1.5">
                    <span class="text-blue-800 text-xs font-semibold tracking-wider uppercase font-['Inter']">
                        ADMIN DESA · SUKAMAJU
                    </span>
                    <h1 class="text-slate-900 text-2xl sm:text-3xl font-bold tracking-tight">
                        Pelayanan yang terus bergerak
                    </h1>
                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        Pantau pengaduan masyarakat secara real-time, prioritas respons, dan penyelesaian di wilayah Anda.
                    </p>
                </div>

                {{-- Filter bar --}}
                <div class="p-4 sm:p-5 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-3">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-700">Tingkat / Peran</label>
                            <select wire:model.live="peran" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:bg-white focus:outline-2 focus:outline-blue-800">
                                <option value="Admin Desa">Admin Desa</option>
                                <option value="Kepala Desa">Kepala Desa</option>
                                <option value="Petugas Lapangan">Petugas Lapangan</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-700">Cakupan Wilayah</label>
                            <select wire:model.live="wilayah" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:bg-white focus:outline-2 focus:outline-blue-800">
                                <option value="Desa Sukamaju · Semua RT">Desa Sukamaju · Semua RT</option>
                                <option value="RT 01">RT 01</option>
                                <option value="RT 02">RT 02</option>
                                <option value="RT 03">RT 03</option>
                                <option value="RT 04">RT 04</option>
                                <option value="RT 05">RT 05</option>
                            </select>
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label class="text-xs font-semibold text-slate-700">Periode Data</label>
                            <select wire:model.live="periode" class="px-3 py-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-800 focus:bg-white focus:outline-2 focus:outline-blue-800">
                                <option value="Semua Periode">Semua Periode</option>
                                <option value="Bulan Ini">Bulan Ini</option>
                                <option value="Tahun Ini">Tahun Ini</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- Real Statistics Metric Cards --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                    {{-- Total --}}
                    <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-slate-500">Total Masuk</span>
                            <div class="size-7 rounded-lg bg-slate-100 flex items-center justify-center text-slate-600">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-3xl font-bold text-slate-900 tracking-tight">{{ $totalCount }}</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Semua laporan warga</span>
                        </div>
                    </div>

                    {{-- Menunggu --}}
                    <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-amber-600">Menunggu</span>
                            <div class="size-7 rounded-lg bg-amber-50 flex items-center justify-center text-amber-600">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-3xl font-bold text-amber-600 tracking-tight">{{ $menungguCount }}</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Perlu diverifikasi</span>
                        </div>
                    </div>

                    {{-- Diproses --}}
                    <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-blue-600">Diproses</span>
                            <div class="size-7 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-3xl font-bold text-blue-600 tracking-tight">{{ $diprosesCount }}</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Sedang tindak lanjut</span>
                        </div>
                    </div>

                    {{-- Selesai --}}
                    <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-emerald-600">Selesai</span>
                            <div class="size-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-3xl font-bold text-emerald-600 tracking-tight">{{ $selesaiCount }}</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">
                                {{ $totalCount > 0 ? round(($selesaiCount / $totalCount) * 100, 1) : 0 }}% penyelesaian
                            </span>
                        </div>
                    </div>

                    {{-- Dieskalasi / Ditolak --}}
                    <div class="p-5 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col justify-between col-span-2 sm:col-span-1">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-indigo-600">Diteruskan/Lain</span>
                            <div class="size-7 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600">
                                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <span class="text-3xl font-bold text-indigo-600 tracking-tight">{{ $eskalasiCount }}</span>
                            <span class="block text-[11px] text-slate-500 mt-0.5">Diteruskan / evaluasi</span>
                        </div>
                    </div>
                </div>

                {{-- Status Alert Box --}}
                @if($menungguCount > 0)
                    <div class="p-4 bg-amber-50 rounded-xl border border-amber-200/80 flex items-start gap-3">
                        <div class="size-5 text-amber-600 shrink-0 mt-0.5">
                            <svg class="size-5" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                        <div class="flex-1 flex flex-col gap-0.5">
                            <span class="text-amber-900 text-sm font-semibold">{{ $menungguCount }} pengaduan memerlukan respons awal</span>
                            <span class="text-amber-800/80 text-xs leading-relaxed">
                                Pastikan segera memverifikasi dan memperbarui status laporan agar memenuhi target waktu respons sesuai SLA desa.
                            </span>
                        </div>
                        <a href="/dashboard/aduan?status=diajukan" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold rounded-lg shrink-0 transition-colors">
                            Buka Sekarang
                        </a>
                    </div>
                @endif

                {{-- Visual Insights Grid --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Tren Pengaduan --}}
                    <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Aktivitas Laporan Warga</h3>
                                <p class="text-xs text-slate-500">6 bulan terakhir (database riil)</p>
                            </div>
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-semibold rounded-md">
                                Realtime
                            </span>
                        </div>

                        {{-- Dynamic Bar Chart representation --}}
                        <div class="h-48 pt-6 pb-2 flex items-end justify-between gap-3 px-2 border-b border-slate-100">
                            @php
                                $maxVal = max(array_merge($monthCounts, [5]));
                            @endphp
                            @foreach($months as $idx => $mName)
                                @php
                                    $c = $monthCounts[$idx] ?? 0;
                                    $pct = $maxVal > 0 ? max(round(($c / $maxVal) * 100), 12) : 12;
                                @endphp
                                <div class="flex-1 flex flex-col items-center gap-2 group">
                                    <span class="text-[11px] font-bold text-slate-700 group-hover:text-blue-800 transition-colors">{{ $c }}</span>
                                    <div class="w-full max-w-[42px] bg-blue-100 hover:bg-blue-800 rounded-t-lg transition-all" style="height: {{ $pct }}%;"></div>
                                    <span class="text-[11px] text-slate-500 font-medium">{{ $mName }}</span>
                                </div>
                            @endforeach
                        </div>
                        <div class="flex items-center justify-between text-xs text-slate-500">
                            <span>Total terdata: {{ $totalCount }} pengaduan</span>
                            <span class="text-blue-800 font-medium">Berdasarkan tanggal masuk</span>
                        </div>
                    </div>

                    {{-- Sebaran Kategori --}}
                    <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Distribusi Kategori Masalah</h3>
                                <p class="text-xs text-slate-500">Analisis teks laporan masuk</p>
                            </div>
                            <span class="text-xs font-semibold text-slate-600">{{ $totalCount }} Total</span>
                        </div>

                        <div class="flex flex-col gap-3 mt-1">
                            @php
                                $catColors = [
                                    'Infrastruktur' => 'bg-blue-600',
                                    'Kebersihan' => 'bg-teal-600',
                                    'Kesehatan' => 'bg-emerald-600',
                                    'Keamanan' => 'bg-amber-600',
                                    'Administrasi' => 'bg-indigo-600',
                                    'Lainnya' => 'bg-slate-400',
                                ];
                            @endphp

                            @foreach($kategoriStats as $catName => $count)
                                @php
                                    $catPct = $totalCount > 0 ? round(($count / $totalCount) * 100) : 0;
                                @endphp
                                <div class="flex flex-col gap-1">
                                    <div class="flex items-center justify-between text-xs font-medium">
                                        <span class="text-slate-700">{{ $catName }}</span>
                                        <span class="text-slate-500">{{ $count }} laporan ({{ $catPct }}%)</span>
                                    </div>
                                    <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                        <div class="h-full {{ $catColors[$catName] ?? 'bg-blue-600' }} rounded-full" style="width: {{ $catPct }}%;"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Pengaduan Terbaru Table (From Real DB) --}}
                <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-base font-bold text-slate-900">Pengaduan Terbaru</h3>
                            <p class="text-xs text-slate-500">Data live dari database sistem SuaraDesa</p>
                        </div>
                        <a href="/dashboard/aduan" class="px-3.5 py-2 bg-slate-50 hover:bg-slate-100 text-blue-800 border border-slate-200 rounded-lg text-xs font-semibold transition-colors flex items-center gap-1.5">
                            <span>Lihat Semua</span>
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>

                    <div class="overflow-x-auto -mx-6">
                        <table class="w-full text-left border-collapse text-sm">
                            <thead>
                                <tr class="bg-slate-50/80 border-y border-slate-200 text-[11px] font-semibold text-slate-600 uppercase tracking-wider">
                                    <th class="py-3 px-6">Tiket & Judul</th>
                                    <th class="py-3 px-6">Status</th>
                                    <th class="py-3 px-6">Prioritas</th>
                                    <th class="py-3 px-6">Lokasi</th>
                                    <th class="py-3 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @forelse ($recentAduans as $item)
                                    <tr class="hover:bg-slate-50/60 transition-colors">
                                        <td class="py-3.5 px-6">
                                            <a href="/dashboard/aduan/{{ $item->id_aduan }}" class="flex flex-col gap-0.5 group">
                                                <div class="flex items-center gap-2">
                                                    <span class="text-xs font-mono font-semibold text-blue-800 bg-blue-50 px-2 py-0.5 rounded">
                                                        {{ $item->kode_tiket }}
                                                    </span>
                                                    <span class="text-xs text-slate-400">
                                                        {{ $item->waktu_pengaduan?->diffForHumans() }}
                                                    </span>
                                                </div>
                                                <span class="font-medium text-slate-900 group-hover:text-blue-800 transition-colors text-sm line-clamp-1">
                                                    {{ $item->judul_aduan ?: $item->deskripsi_aduan }}
                                                </span>
                                            </a>
                                        </td>
                                        <td class="py-3.5 px-6">
                                            @php
                                                $badgeStyle = match (strtolower($item->status_aduan)) {
                                                    'selesai' => 'bg-emerald-50 text-emerald-800 border-emerald-200',
                                                    'diproses' => 'bg-blue-50 text-blue-800 border-blue-200',
                                                    'diteruskan' => 'bg-indigo-50 text-indigo-800 border-indigo-200',
                                                    'ditolak' => 'bg-rose-50 text-rose-800 border-rose-200',
                                                    default => 'bg-amber-50 text-amber-800 border-amber-200',
                                                };
                                            @endphp
                                            <span class="px-2.5 py-1 text-xs font-semibold rounded-full border {{ $badgeStyle }} inline-block capitalize">
                                                {{ $item->status_aduan }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-6">
                                            @php
                                                $prio = $item->penilaianPrioritas?->tingkat_prioritas ?? 'sedang';
                                                $prioStyle = match(strtolower($prio)) {
                                                    'darurat' => 'text-rose-700 bg-rose-50',
                                                    'tinggi' => 'text-orange-700 bg-orange-50',
                                                    'sedang' => 'text-amber-700 bg-amber-50',
                                                    default => 'text-slate-700 bg-slate-100',
                                                };
                                            @endphp
                                            <span class="px-2 py-0.5 text-xs font-semibold rounded capitalize {{ $prioStyle }}">
                                                {{ $prio }}
                                            </span>
                                        </td>
                                        <td class="py-3.5 px-6 text-xs text-slate-600 max-w-[200px] truncate">
                                            {{ $item->alamat ?: 'Tidak disertakan' }}
                                        </td>
                                        <td class="py-3.5 px-6 text-right">
                                            <a href="/dashboard/aduan/{{ $item->id_aduan }}" class="text-blue-800 hover:text-blue-950 font-semibold text-xs inline-flex items-center gap-1">
                                                Tindak Lanjut &rarr;
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="py-8 text-center text-slate-500 text-sm">
                                            Belum ada pengaduan masyarakat yang terdaftar.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                {{-- Prioritas Utama Box --}}
                @if($topPriority)
                    <div class="p-6 bg-gradient-to-r from-blue-900 to-indigo-900 rounded-xl text-white shadow-md flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                        <div class="flex flex-col gap-2">
                            <div class="flex items-center gap-2">
                                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-rose-500/30 text-rose-200 border border-rose-400/40">
                                    Prioritas AI Tertinggi
                                </span>
                                <span class="text-xs text-slate-300 font-mono">{{ $topPriority->kode_tiket }}</span>
                            </div>
                            <h4 class="text-lg font-bold text-white">
                                {{ $topPriority->judul_aduan ?: 'Aduan Prioritas' }}
                            </h4>
                            <p class="text-xs text-slate-300 max-w-2xl line-clamp-2">
                                {{ $topPriority->deskripsi_aduan }}
                            </p>
                            <span class="text-xs text-slate-400">
                                Lokasi: {{ $topPriority->alamat ?: 'Tidak disertakan' }} · Status: <span class="font-semibold text-amber-300 uppercase">{{ $topPriority->status_aduan }}</span>
                            </span>
                        </div>
                        <a href="/dashboard/aduan/{{ $topPriority->id_aduan }}" class="px-5 py-3 bg-white hover:bg-slate-100 text-blue-950 font-bold text-sm rounded-lg shrink-0 transition-all shadow-sm">
                            Buka & Tangani Kasus
                        </a>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <footer class="mt-auto p-6 border-t border-slate-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 font-['Inter']">
                <span>© 2026 SuaraDesa · Platform Transparansi & Layanan Aduan Warga</span>
                <span>Desa Sukamaju · Kecamatan Harapan</span>
            </footer>
        </main>
    </div>
</div>