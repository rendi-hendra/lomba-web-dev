<?php

use Livewire\Component;
use App\Models\Aduan;
use App\Models\PenilaianPrioritas;
use App\Models\TargetRespon;

new class extends Component {
    public ?int $id_aduan = null;
    public ?Aduan $aduan = null;

    public string $status_aduan = 'diajukan';
    public string $tanggapan = '';
    public string $petugas_penanggung_jawab = '';

    public function mount(?int $id_aduan = null)
    {
        $this->id_aduan = $id_aduan;

        if (!$this->id_aduan) {
            $latest = Aduan::latest('waktu_pengaduan')->first();
            if ($latest) {
                return $this->redirect("/dashboard/aduan/{$latest->id_aduan}", navigate: true);
            }
            return $this->redirect('/dashboard/aduan', navigate: true);
        }

        $this->loadAduan();
    }

    public function loadAduan()
    {
        $this->aduan = Aduan::with(['penilaianPrioritas.targetRespon'])
            ->find($this->id_aduan);

        if (!$this->aduan) {
            session()->flash('error', 'Data pengaduan tidak ditemukan.');
            return $this->redirect('/dashboard/aduan', navigate: true);
        }

        $this->status_aduan = $this->aduan->status_aduan ?? 'diajukan';
        $this->tanggapan = $this->aduan->tanggapan ?? '';
        $this->petugas_penanggung_jawab = $this->aduan->petugas_penanggung_jawab ?? (auth()->user()->name ?? 'Petugas Desa');
    }

    public function simpanTindakLanjut()
    {
        $this->validate([
            'status_aduan' => 'required|in:diajukan,diproses,diteruskan,selesai,ditolak',
            'tanggapan' => 'nullable|string|max:1000',
            'petugas_penanggung_jawab' => 'nullable|string|max:100',
        ]);

        $this->aduan->update([
            'status_aduan' => $this->status_aduan,
            'tanggapan' => $this->tanggapan,
            'petugas_penanggung_jawab' => $this->petugas_penanggung_jawab,
        ]);

        // Update target respon if moving to diproses / selesai
        if (in_array($this->status_aduan, ['diproses', 'selesai'])) {
            $target = $this->aduan->penilaianPrioritas?->targetRespon;
            if ($target && !$target->waktu_respon) {
                $target->update([
                    'waktu_respon' => now(),
                    'status_respon' => now()->lessThanOrEqualTo($target->tenggat_respon) ? 'direspons' : 'terlambat',
                ]);
            }
        }

        $this->loadAduan();
        session()->flash('success', 'Tindak lanjut pengaduan berhasil disimpan dan diperbarui.');
    }

    public function eskalasiKeTingkatAtas()
    {
        $this->status_aduan = 'diteruskan';
        if (empty($this->tanggapan)) {
            $this->tanggapan = 'Laporan telah dikoordinasikan dan diteruskan ke instansi tingkat Kecamatan untuk penanganan spesifik lebih lanjut.';
        }
        $this->simpanTindakLanjut();
    }
};
?>

<div class="w-full max-w-[1440px] mx-auto min-h-screen bg-slate-50 flex flex-col justify-start items-start font-['Inter']">
    <div class="self-stretch flex flex-col md:flex-row justify-start items-stretch min-h-screen">
        {{-- Reusable Sidebar --}}
        <x-sidebar />

        {{-- Main Content --}}
        <main class="flex-1 flex flex-col min-w-0 bg-slate-50">
            {{-- Reusable Dashboard Topbar with mobile toggle and back link --}}
            <x-dashboard-header title="Detail & Penanganan Aduan" :badge="$aduan?->kode_tiket" backUrl="/dashboard/aduan" />

            <div class="p-6 sm:p-8 flex flex-col gap-6">
                {{-- Flash Message --}}
                @if (session()->has('success'))
                    <div class="p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2">
                            <svg class="size-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <span class="text-sm font-medium">{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                @if (session()->has('error'))
                    <div class="p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 flex items-center justify-between shadow-xs">
                        <div class="flex items-center gap-2">
                            <svg class="size-5 text-rose-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            <span class="text-sm font-medium">{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                @if($aduan)
                    {{-- Header Summary Box --}}
                    <div class="flex flex-col gap-3">
                        <div class="flex flex-wrap items-center justify-between gap-4">
                            <div>
                                <span class="text-blue-800 text-xs font-bold uppercase tracking-wider">
                                    TIKET PENGADUAN RESMI
                                </span>
                                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 mt-1">
                                    {{ $aduan->judul_aduan ?: 'Laporan Masalah Warga' }}
                                </h1>
                                <p class="text-slate-500 text-xs sm:text-sm mt-1">
                                    Diterima pada {{ $aduan->waktu_pengaduan?->translatedFormat('d F Y, H:i') }} WIB · ID: #{{ $aduan->id_aduan }}
                                </p>
                            </div>

                            @php
                                $badgeStyle = match (strtolower($aduan->status_aduan)) {
                                    'selesai' => 'bg-emerald-100 text-emerald-900 border-emerald-300',
                                    'diproses' => 'bg-blue-100 text-blue-900 border-blue-300',
                                    'diteruskan' => 'bg-indigo-100 text-indigo-900 border-indigo-300',
                                    'ditolak' => 'bg-rose-100 text-rose-900 border-rose-300',
                                    default => 'bg-amber-100 text-amber-900 border-amber-300',
                                };
                            @endphp
                            <div class="px-4 py-2 rounded-xl border text-sm font-bold uppercase tracking-wider {{ $badgeStyle }}">
                                Status: {{ $aduan->status_aduan }}
                            </div>
                        </div>

                        {{-- Target Respon SLA Banner --}}
                        @php
                            $target = $aduan->penilaianPrioritas?->targetRespon;
                            $penilaian = $aduan->penilaianPrioritas;
                        @endphp

                        @if($target)
                            @php
                                $isTerlambat = now()->greaterThan($target->tenggat_respon) && !$target->waktu_respon;
                                $isSelesai = !empty($target->waktu_respon);
                            @endphp
                            <div class="p-4 rounded-xl border flex items-start gap-3 {{ $isTerlambat ? 'bg-rose-50 border-rose-200 text-rose-900' : ($isSelesai ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-blue-50 border-blue-200 text-blue-900') }}">
                                <svg class="size-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <div class="flex-1 flex flex-col gap-0.5 text-xs sm:text-sm">
                                    <span class="font-bold">
                                        @if($isSelesai)
                                            Sudah Direspons ({{ $target->waktu_respon?->translatedFormat('d M Y, H:i') }} WIB)
                                        @elseif($isTerlambat)
                                            Melewati Batas Respons Waktu (SLA Terlampaui)
                                        @else
                                            Batas Target Waktu Respons: {{ $target->tenggat_respon?->translatedFormat('d M Y, H:i') }} WIB (Sisa {{ $target->tenggat_respon?->diffForHumans(null, true) }})
                                        @endif
                                    </span>
                                    <span class="opacity-80">
                                        Prioritas AI: <strong class="capitalize">{{ $penilaian->tingkat_prioritas }}</strong> (Skor: {{ $penilaian->skor_prioritas }}/10) · Batas Maks: {{ round($target->batas_respon_menit / 60) }} jam.
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Main 2-Column Content --}}
                    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                        {{-- Left Column: Complaint Detail & Action Form --}}
                        <div class="lg:col-span-8 flex flex-col gap-6">
                            {{-- Card 1: Isi Laporan Masuk --}}
                            <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-4">
                                <h2 class="text-base font-bold text-slate-900">Isi Pengaduan dari Warga</h2>
                                
                                <div class="p-4 bg-slate-50 rounded-lg border border-slate-200/80 text-sm text-slate-800 leading-relaxed whitespace-pre-line">
                                    {{ $aduan->deskripsi_aduan }}
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2">
                                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex flex-col gap-1">
                                        <span class="text-slate-500 font-medium">Lokasi / Alamat Warga</span>
                                        <span class="font-semibold text-slate-800">{{ $aduan->alamat ?: 'Tidak disertakan' }}</span>
                                    </div>
                                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200 flex flex-col gap-1">
                                        <span class="text-slate-500 font-medium">Kontak Pelapor</span>
                                        <span class="font-semibold text-slate-800">
                                            @if($aduan->nohp)
                                                {{ $aduan->nohp }} (Dapat dihubungi)
                                            @else
                                                <span class="text-slate-400 italic">Anonim / Tanpa nomor telepon</span>
                                            @endif
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Card 2: Form Tindak Lanjut Petugas --}}
                            <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-5">
                                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                                    <div>
                                        <h2 class="text-base font-bold text-slate-900">Perbarui Tindak Lanjut</h2>
                                        <p class="text-xs text-slate-500">Tentukan status penanganan dan berikan tanggapan resmi.</p>
                                    </div>
                                    <span class="text-xs font-semibold text-blue-800 bg-blue-50 px-2.5 py-1 rounded">Panel Petugas</span>
                                </div>

                                <form wire:submit.prevent="simpanTindakLanjut" class="flex flex-col gap-4">
                                    {{-- Status Selection --}}
                                    <div class="flex flex-col gap-1.5">
                                        <label for="status_aduan" class="text-xs font-semibold text-slate-700">Status Penanganan</label>
                                        <select id="status_aduan" wire:model="status_aduan"
                                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm font-medium text-slate-900 focus:bg-white focus:outline-2 focus:outline-blue-800">
                                            <option value="diajukan">Diajukan (Belum diproses)</option>
                                            <option value="diproses">Diproses (Sedang dikerjakan)</option>
                                            <option value="diteruskan">Diteruskan (Eskalasi ke tingkat atas)</option>
                                            <option value="selesai">Selesai (Masalah terselesaikan)</option>
                                            <option value="ditolak">Ditolak (Tidak memenuhi syarat / fiktif)</option>
                                        </select>
                                        <span class="text-[11px] text-slate-500">Status ini akan langsung tampil saat warga melacak pengaduannya.</span>
                                    </div>

                                    {{-- Petugas Name --}}
                                    <div class="flex flex-col gap-1.5">
                                        <label for="petugas_penanggung_jawab" class="text-xs font-semibold text-slate-700">Petugas / Tim Penanggung Jawab</label>
                                        <input type="text" id="petugas_penanggung_jawab" wire:model="petugas_penanggung_jawab"
                                            placeholder="Contoh: Rina Pratiwi / Tim Sarana Desa"
                                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-2 focus:outline-blue-800" />
                                    </div>

                                    {{-- Tanggapan Resmi --}}
                                    <div class="flex flex-col gap-1.5">
                                        <label for="tanggapan" class="text-xs font-semibold text-slate-700">Tanggapan / Catatan untuk Warga</label>
                                        <textarea id="tanggapan" wire:model="tanggapan" rows="4"
                                            placeholder="Tuliskan keterangan tindakan, progres perbaikan, atau instruksi kepada warga..."
                                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-lg text-sm text-slate-900 focus:bg-white focus:outline-2 focus:outline-blue-800 resize-y"></textarea>
                                        <span class="text-[11px] text-slate-500">Tanggapan ini akan dibaca oleh pelapor saat mengecek status tiket mereka.</span>
                                    </div>

                                    {{-- Action Buttons --}}
                                    <div class="flex flex-wrap items-center gap-3 pt-2">
                                        <button type="submit"
                                            class="px-5 py-2.5 bg-blue-800 hover:bg-blue-900 text-white rounded-lg text-sm font-semibold transition-colors flex items-center gap-2 shadow-xs">
                                            <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                            <span>Simpan Tindak Lanjut</span>
                                        </button>

                                        <button type="button" wire:click="eskalasiKeTingkatAtas"
                                            class="px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                                            <svg class="size-4 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                            <span>Eskalasi ke Kecamatan</span>
                                        </button>

                                        @if($aduan->nohp)
                                            @php
                                                $cleanHp = preg_replace('/[^0-9]/', '', $aduan->nohp);
                                                if (str_starts_with($cleanHp, '0')) {
                                                    $cleanHp = '62' . substr($cleanHp, 1);
                                                }
                                                $waText = rawurlencode("Halo Warga Desa Sukamaju, mengenai laporan nomor tiket {$aduan->kode_tiket} ('{$aduan->judul_aduan}'), saat ini berstatus: " . strtoupper($status_aduan) . ". Tanggapan Petugas: " . ($tanggapan ?: 'Sedang ditindaklanjuti.'));
                                                $waUrl = "https://wa.me/{$cleanHp}?text={$waText}";
                                            @endphp
                                            <a href="{{ $waUrl }}" target="_blank" rel="noopener noreferrer"
                                                class="px-4 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
                                                <svg class="size-4" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12.031 6.172c-3.181 0-5.767 2.586-5.768 5.766-.001 1.298.38 2.27 1.019 3.287l-.711 2.598 2.668-.699c.969.539 1.942.825 2.792.825 3.183 0 5.769-2.586 5.77-5.767.001-3.181-2.587-5.77-5.77-5.77zm3.385 8.169c-.144.405-.837.774-1.17.824-.312.045-.634.074-1.782-.405-1.469-.613-2.42-2.115-2.493-2.213-.074-.098-.598-.797-.598-1.521 0-.723.376-1.079.51-1.226.134-.148.293-.185.391-.185.098 0 .196.002.281.006.09.004.21.034.321.301.12.287.409 1.003.445 1.077.037.074.062.16.012.257-.049.098-.074.16-.147.247-.074.086-.156.193-.223.259-.074.074-.151.155-.065.303.086.148.383.633.821 1.023.565.503 1.042.658 1.19.732.148.074.234.062.321-.037.086-.099.37-.432.469-.58.098-.148.196-.123.327-.074.134.049.85.401.996.475.148.074.246.111.282.173.037.062.037.358-.107.763z"/>
                                                </svg>
                                                <span>Kirim WhatsApp</span>
                                            </a>
                                        @endif
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Right Column: AI Scoring & Ticket Timeline --}}
                        <div class="lg:col-span-4 flex flex-col gap-6">
                            {{-- Penilaian AI Card --}}
                            <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-sm font-bold text-slate-900">Analisis Otomatis AI</h3>
                                    <span class="text-[10px] font-bold uppercase bg-blue-50 text-blue-800 px-2 py-0.5 rounded">Sistem Pintar</span>
                                </div>

                                @if($penilaian)
                                    <div class="flex flex-col gap-3 text-xs">
                                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg">
                                            <span class="text-slate-600">Tingkat Urgensi</span>
                                            <span class="font-bold text-slate-800">{{ $penilaian->nilai_urgensi }} / 10</span>
                                        </div>
                                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg">
                                            <span class="text-slate-600">Dampak Permasalahan</span>
                                            <span class="font-bold text-slate-800">{{ $penilaian->nilai_dampak }} / 10</span>
                                        </div>
                                        <div class="flex items-center justify-between p-2.5 bg-slate-50 rounded-lg">
                                            <span class="text-slate-600">Estimasi Warga Terdampak</span>
                                            <span class="font-bold text-slate-800">{{ $penilaian->nilai_jumlah_warga }} / 10</span>
                                        </div>
                                        <div class="flex items-center justify-between p-3 bg-blue-50/80 rounded-lg border border-blue-100">
                                            <div>
                                                <span class="block text-slate-600 text-[11px]">Skor Prioritas Gabungan</span>
                                                <span class="text-lg font-bold text-blue-900">{{ $penilaian->skor_prioritas }}</span>
                                            </div>
                                            <span class="px-2.5 py-1 text-xs font-bold uppercase rounded-md bg-blue-800 text-white">
                                                {{ $penilaian->tingkat_prioritas }}
                                            </span>
                                        </div>
                                    </div>
                                @else
                                    <p class="text-xs text-slate-500">Penilaian prioritas sedang diproses oleh sistem.</p>
                                @endif
                            </div>

                            {{-- Riwayat Status Card --}}
                            <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-4">
                                <h3 class="text-sm font-bold text-slate-900">Alur Progres Penanganan</h3>

                                <div class="relative pl-6 flex flex-col gap-4 text-xs before:absolute before:left-2 before:top-2 before:bottom-2 before:w-0.5 before:bg-slate-200">
                                    {{-- Tahap Diajukan --}}
                                    <div class="relative flex flex-col gap-0.5">
                                        <div class="absolute -left-6 top-0.5 size-3 rounded-full bg-emerald-600 ring-4 ring-white"></div>
                                        <span class="font-bold text-slate-900">1. Laporan Diajukan</span>
                                        <span class="text-slate-500">{{ $aduan->waktu_pengaduan?->translatedFormat('d M Y, H:i') }} WIB</span>
                                    </div>

                                    {{-- Tahap Diproses --}}
                                    <div class="relative flex flex-col gap-0.5">
                                        <div class="absolute -left-6 top-0.5 size-3 rounded-full {{ in_array($aduan->status_aduan, ['diproses', 'diteruskan', 'selesai']) ? 'bg-emerald-600' : 'bg-slate-300' }} ring-4 ring-white"></div>
                                        <span class="font-bold {{ in_array($aduan->status_aduan, ['diproses', 'diteruskan', 'selesai']) ? 'text-slate-900' : 'text-slate-400' }}">
                                            2. Diverifikasi & Diproses
                                        </span>
                                        <span class="text-slate-500">
                                            {{ in_array($aduan->status_aduan, ['diproses', 'diteruskan', 'selesai']) ? 'Petugas menangani di lapangan' : 'Menunggu tindakan' }}
                                        </span>
                                    </div>

                                    {{-- Tahap Selesai / Diteruskan --}}
                                    <div class="relative flex flex-col gap-0.5">
                                        <div class="absolute -left-6 top-0.5 size-3 rounded-full {{ in_array($aduan->status_aduan, ['diteruskan', 'selesai']) ? 'bg-emerald-600' : 'bg-slate-300' }} ring-4 ring-white"></div>
                                        <span class="font-bold {{ in_array($aduan->status_aduan, ['diteruskan', 'selesai']) ? 'text-slate-900' : 'text-slate-400' }}">
                                            3. {{ $aduan->status_aduan == 'diteruskan' ? 'Diteruskan ke Instansi Terkait' : 'Selesai Ditindaklanjuti' }}
                                        </span>
                                        <span class="text-slate-500">
                                            {{ in_array($aduan->status_aduan, ['diteruskan', 'selesai']) ? ($aduan->tanggapan ? 'Tanggapan sudah diberikan' : 'Tahap akhir') : 'Belum selesai' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="p-12 text-center text-slate-500 bg-white rounded-xl border border-slate-200">
                        Pengaduan tidak ditemukan atau telah dihapus.
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <footer class="mt-auto p-6 border-t border-slate-200 bg-white flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500">
                <span>© 2026 SuaraDesa · Panel Pengelolaan Aduan & Layanan Masyarakat</span>
                <span>Desa Sukamaju</span>
            </footer>
        </main>
    </div>
</div>