<?php

use Livewire\Component;
use Illuminate\Support\Facades\DB;

/**
 * Lacak Pengaduan — sesuai Activity Diagram (lane WARGA):
 * Ingin Melacak Pengaduan? → Ya → Masukkan Kode Tiket
 * → Kode Ditemukan? → Tidak → Menampilkan pesan kode tiket tidak ditemukan
 * → Ya → Mengambil status aduan dan tenggat respons
 * → Menampilkan Status, Prioritas dan Countdown
 */
new class extends Component {
    public string $kode_tiket = '';
    public bool $searched = false;
    public ?array $result = null;
    public ?string $errorMessage = null;

    public function lacak()
    {
        $this->validate([
            'kode_tiket' => 'required|min:3',
        ], [
            'kode_tiket.required' => 'Kode tiket wajib diisi.',
            'kode_tiket.min' => 'Kode tiket minimal 3 karakter.',
        ]);

        $this->searched = true;
        $this->errorMessage = null;
        $this->result = null;

        $aduan = \App\Models\Aduan::where('kode_tiket', trim($this->kode_tiket))->first();

        if (!$aduan) {
            // Kode Ditemukan? → Tidak → Menampilkan pesan kode tiket tidak ditemukan
            $this->errorMessage = 'Kode tiket tidak ditemukan. Periksa kembali kode pada bukti pengiriman Anda.';
            return;
        }

        $data = DB::table('v_countdown_respon')
            ->where('kode_tiket', trim($this->kode_tiket))
            ->first();

        // Kode Ditemukan? → Ya → Menampilkan Status, Prioritas dan Countdown
        $this->result = [
            'id_aduan' => $aduan->id_aduan,
            'kode_tiket' => $aduan->kode_tiket,
            'deskripsi' => $aduan->deskripsi_aduan,
            'judul' => $aduan->judul_aduan ?: (explode("\n\n", $aduan->deskripsi_aduan)[0] ?? $aduan->deskripsi_aduan),
            'status_aduan' => $aduan->status_aduan,
            'tanggapan' => $aduan->tanggapan,
            'petugas_penanggung_jawab' => $aduan->petugas_penanggung_jawab,
            'tingkat_prioritas' => $data->tingkat_prioritas ?? 'sedang',
            'skor_prioritas' => $data->skor_prioritas ?? 0,
            'batas_respon_menit' => $data->batas_respon_menit ?? 0,
            'tenggat_respon' => $data->tenggat_respon ?? null,
            'waktu_respon' => $data->waktu_respon ?? null,
            'status_countdown' => $data->status_countdown ?? 'menunggu',
            'sisa_detik' => $data->sisa_detik ?? 0,
        ];
    }
};
?>

<div class="w-full max-w-[1440px] mx-auto min-h-screen bg-slate-50 flex flex-col justify-start items-start overflow-hidden">
    {{-- Livewire Navbar --}}
    <livewire:navbar />

    {{-- Main Container --}}
    <main class="self-stretch px-4 sm:px-12 lg:px-24 py-8 sm:py-10 flex flex-col justify-start items-start gap-6 overflow-hidden">
        {{-- Title Section --}}
        <div class="self-stretch flex flex-col justify-start items-start gap-2 overflow-hidden">
            <span class="self-stretch justify-start text-blue-800 text-xs font-semibold font-['Inter'] tracking-wider uppercase">
                LACAK PENGADUAN
            </span>
            <h1 class="self-stretch justify-start text-slate-900 text-2xl sm:text-3xl font-semibold font-['Inter'] leading-tight sm:leading-[48px]">
                Pantau setiap langkah
            </h1>
            <p class="self-stretch justify-start text-slate-600 text-sm sm:text-base font-normal font-['Inter'] leading-6">
                Masukkan kode tiket. Anda tidak perlu masuk untuk melihat perkembangan pengaduan.
            </p>
        </div>

        {{-- Search Card --}}
        <form wire:submit.prevent="lacak" class="self-stretch p-6 bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-4 overflow-hidden">
            <div class="self-stretch flex flex-col justify-start items-start gap-2 overflow-hidden">
                <label for="kode_tiket" class="self-stretch text-slate-900 text-sm font-semibold font-['Inter'] leading-5">
                    Kode tiket
                </label>
                <input type="text" id="kode_tiket" wire:model="kode_tiket" placeholder="Contoh: SD-2026-ABCDEF"
                    class="self-stretch min-h-12 p-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 focus:outline-2 focus:outline-blue-800 text-slate-900 placeholder:text-slate-400 text-sm font-normal font-['Inter'] leading-5 transition-all" />
                @error('kode_tiket')
                    <span class="text-xs text-red-600 font-['Inter']">{{ $message }}</span>
                @enderror
                <span class="self-stretch text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                    Periksa kode pada bukti pengiriman.
                </span>
            </div>
            <button type="submit" wire:loading.attr="disabled"
                class="min-h-12 px-6 py-3 bg-blue-800 hover:bg-blue-900 disabled:opacity-50 rounded-lg shadow-[2px_4px_4px_0px_rgba(30,90,168,0.25)] inline-flex justify-center items-center gap-2 overflow-hidden transition-colors cursor-pointer">
                <svg class="size-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <span wire:loading.remove class="text-center text-white text-sm font-semibold font-['Inter']">Lacak Pengaduan</span>
                <span wire:loading class="text-center text-white text-sm font-semibold font-['Inter']">Mencari...</span>
            </button>
        </form>

        {{-- Error Message: Kode tiket tidak ditemukan --}}
        @if($searched && $errorMessage)
        <div class="self-stretch p-4 bg-red-50 rounded-lg inline-flex justify-start items-start gap-3 overflow-hidden">
            <svg class="size-5 text-red-600 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1 inline-flex flex-col justify-start items-start gap-1 overflow-hidden">
                <span class="self-stretch text-red-800 text-sm font-semibold font-['Inter'] leading-5">
                    Kode tiket tidak ditemukan
                </span>
                <span class="self-stretch text-red-800 text-sm font-normal font-['Inter'] leading-5">
                    {{ $errorMessage }}
                </span>
            </div>
        </div>
        @endif

        {{-- Result: Status, Prioritas dan Countdown --}}
        @if($result)
        @php
            // Status badge styling
            $statusColors = match($result['status_aduan']) {
                'diajukan' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'dot' => 'bg-slate-500'],
                'diproses' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-800', 'dot' => 'bg-amber-500'],
                'diteruskan' => ['bg' => 'bg-red-50', 'text' => 'text-red-800', 'dot' => 'bg-red-600'],
                'selesai' => ['bg' => 'bg-green-50', 'text' => 'text-green-800', 'dot' => 'bg-green-600'],
                'ditolak' => ['bg' => 'bg-red-50', 'text' => 'text-red-800', 'dot' => 'bg-red-600'],
                default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'dot' => 'bg-slate-500'],
            };
            $statusLabel = match($result['status_aduan']) {
                'diajukan' => 'Diterima',
                'diproses' => 'Diproses',
                'diteruskan' => 'Dieskalasi',
                'selesai' => 'Selesai',
                'ditolak' => 'Ditolak',
                default => ucfirst($result['status_aduan']),
            };

            // Priority badge styling
            $prioColors = match($result['tingkat_prioritas'] ?? '') {
                'darurat' => ['bg' => 'bg-red-50', 'text' => 'text-red-800', 'dot' => 'bg-red-600'],
                'tinggi' => ['bg' => 'bg-amber-50', 'text' => 'text-amber-800', 'dot' => 'bg-amber-500'],
                'sedang' => ['bg' => 'bg-blue-50', 'text' => 'text-blue-800', 'dot' => 'bg-blue-500'],
                'rendah' => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400'],
                default => ['bg' => 'bg-slate-100', 'text' => 'text-slate-600', 'dot' => 'bg-slate-400'],
            };

            // Countdown styling
            $sisaDetik = (int) $result['sisa_detik'];
            $jam = floor($sisaDetik / 3600);
            $menit = floor(($sisaDetik % 3600) / 60);
            $countdownText = $sisaDetik > 0 ? "Sisa {$jam} jam {$menit} menit" : 'Waktu habis';
            $countdownColors = match(true) {
                $result['status_countdown'] === 'direspons' => ['bg' => 'bg-green-50', 'text' => 'text-green-800', 'icon' => 'text-green-800'],
                $result['status_countdown'] === 'terlambat' => ['bg' => 'bg-red-50', 'text' => 'text-red-800', 'icon' => 'text-red-600'],
                $sisaDetik < 3600 => ['bg' => 'bg-red-50', 'text' => 'text-red-800', 'icon' => 'text-red-600'],
                $sisaDetik < 21600 => ['bg' => 'bg-amber-50', 'text' => 'text-amber-800', 'icon' => 'text-amber-600'],
                default => ['bg' => 'bg-green-50', 'text' => 'text-green-800', 'icon' => 'text-green-800'],
            };
            if ($result['status_countdown'] === 'direspons') {
                $countdownText = 'Sudah direspons';
            }
        @endphp

        {{-- Status Banner --}}
        <div class="self-stretch p-4 {{ $statusColors['bg'] }} rounded-lg inline-flex justify-start items-start gap-3 overflow-hidden">
            <svg class="size-5 {{ $statusColors['text'] }} shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
            </svg>
            <div class="flex-1 inline-flex flex-col justify-start items-start gap-1 overflow-hidden">
                <span class="self-stretch {{ $statusColors['text'] }} text-sm font-semibold font-['Inter'] leading-5">
                    Pengaduan {{ strtolower($statusLabel) }}
                </span>
                <span class="self-stretch {{ $statusColors['text'] }} text-sm font-normal font-['Inter'] leading-5">
                    Status terkini pengaduan Anda dengan kode {{ $result['kode_tiket'] }}.
                </span>
            </div>
        </div>

        {{-- Detail Cards --}}
        <div class="self-stretch inline-flex flex-col lg:flex-row justify-start items-start gap-4">
            {{-- Left: Detail Pengaduan --}}
            <div class="flex-1 w-full inline-flex flex-col justify-start items-start gap-6">
                <div class="self-stretch p-6 bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-4 overflow-hidden">
                    <h2 class="self-stretch text-slate-900 text-xl font-semibold font-['Inter'] leading-8">
                        {{ $result['judul'] }}
                    </h2>
                    <div class="self-stretch inline-flex justify-start items-center gap-4 flex-wrap">
                        <span class="text-blue-800 text-sm font-semibold font-['Inter'] leading-5">
                            {{ $result['kode_tiket'] }}
                        </span>
                        {{-- Status Badge --}}
                        <div class="px-3 py-1.5 {{ $statusColors['bg'] }} rounded-full flex items-center gap-2">
                            <div class="size-1.5 {{ $statusColors['dot'] }} rounded-full"></div>
                            <span class="{{ $statusColors['text'] }} text-xs font-semibold font-['Inter']">{{ $statusLabel }}</span>
                        </div>
                        {{-- Priority Badge --}}
                        <div class="px-3 py-1.5 {{ $prioColors['bg'] }} rounded-full flex items-center gap-2">
                            <div class="size-1.5 {{ $prioColors['dot'] }} rounded-full"></div>
                            <span class="{{ $prioColors['text'] }} text-xs font-semibold font-['Inter']">Prioritas {{ ucfirst($result['tingkat_prioritas'] ?? '-') }}</span>
                        </div>
                    </div>

                    {{-- Detail Info --}}
                    <div class="self-stretch flex flex-col gap-3">
                        <div class="flex items-start gap-3">
                            <span class="text-slate-600 text-sm font-normal font-['Inter'] w-32 shrink-0">Skor Prioritas</span>
                            <span class="text-slate-900 text-sm font-semibold font-['Inter']">{{ number_format((float)($result['skor_prioritas'] ?? 0), 1) }} / 10</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-slate-600 text-sm font-normal font-['Inter'] w-32 shrink-0">Batas Respon</span>
                            <span class="text-slate-900 text-sm font-semibold font-['Inter']">{{ round(($result['batas_respon_menit'] ?? 0) / 60) }} jam</span>
                        </div>
                        <div class="flex items-start gap-3">
                            <span class="text-slate-600 text-sm font-normal font-['Inter'] w-32 shrink-0">Tenggat Respon</span>
                            <span class="text-slate-900 text-sm font-semibold font-['Inter']">
                                {{ $result['tenggat_respon'] ? \Carbon\Carbon::parse($result['tenggat_respon'])->format('d M Y · H:i') . ' WIB' : '-' }}
                            </span>
                        </div>
                    </div>

                    {{-- Tanggapan Petugas Jika Ada --}}
                    @if(!empty($result['tanggapan']))
                        <div class="p-4 bg-emerald-50 rounded-xl border border-emerald-200 flex flex-col gap-1.5">
                            <div class="flex items-center justify-between">
                                <span class="text-xs font-bold text-emerald-900 uppercase">Tanggapan Resmi Petugas</span>
                                @if(!empty($result['petugas_penanggung_jawab']))
                                    <span class="text-[11px] text-emerald-700 font-medium">Oleh: {{ $result['petugas_penanggung_jawab'] }}</span>
                                @endif
                            </div>
                            <p class="text-xs sm:text-sm text-emerald-950 whitespace-pre-line leading-relaxed">
                                {{ $result['tanggapan'] }}
                            </p>
                        </div>
                    @endif

                    <div class="pt-2">
                        <a href="/aduan/detail/{{ $result['id_aduan'] }}"
                            class="px-4 py-2.5 bg-blue-800 hover:bg-blue-900 text-white rounded-lg text-xs font-semibold inline-flex items-center gap-2 transition-colors shadow-xs">
                            <span>Lihat Rincian Lengkap Pengaduan</span>
                            <svg class="size-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Right: Countdown & Prioritas --}}
            <div class="w-full lg:w-96 flex flex-col justify-start items-start gap-6">
                {{-- Countdown Card --}}
                <div class="self-stretch p-6 bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-4 overflow-hidden">
                    <h3 class="self-stretch text-slate-900 text-xl font-semibold font-['Inter'] leading-8">
                        Countdown Respons
                    </h3>
                    <div class="self-stretch p-4 {{ $countdownColors['bg'] }} rounded-lg flex flex-col items-center gap-2">
                        <svg class="size-8 {{ $countdownColors['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="{{ $countdownColors['text'] }} text-2xl font-semibold font-['Inter']" @if($result['status_countdown'] === 'menunggu' && $sisaDetik > 0) x-data="{ sisa: {{ $sisaDetik }} }" x-init="setInterval(() => { if(sisa > 0) sisa--; }, 1000)" x-text="Math.floor(sisa/3600) + ' jam ' + Math.floor((sisa%3600)/60) + ' mnt ' + (sisa%60) + ' dtk'" @endif>
                            @if($result['status_countdown'] === 'menunggu' && $sisaDetik > 0)
                                {{ $jam }} jam {{ $menit }} mnt
                            @else
                                {{ $countdownText }}
                            @endif
                        </span>
                        <span class="{{ $countdownColors['text'] }} text-xs font-normal font-['Inter']">
                            @if($result['status_countdown'] === 'menunggu')
                                Hingga {{ $result['tenggat_respon'] ? \Carbon\Carbon::parse($result['tenggat_respon'])->format('d M Y H:i') : '-' }}
                            @elseif($result['status_countdown'] === 'direspons')
                                Direspons pada {{ $result['waktu_respon'] ? \Carbon\Carbon::parse($result['waktu_respon'])->format('d M Y H:i') : '-' }}
                            @else
                                Batas waktu telah terlewati
                            @endif
                        </span>
                    </div>
                </div>

                {{-- Prioritas Summary Card --}}
                <div class="self-stretch p-6 bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-4 overflow-hidden">
                    <h3 class="self-stretch text-slate-900 text-xl font-semibold font-['Inter'] leading-8">
                        Penilaian Prioritas
                    </h3>
                    <div class="self-stretch flex flex-col gap-3">
                        {{-- Skor Bar --}}
                        <div class="flex flex-col gap-1">
                            <div class="flex justify-between">
                                <span class="text-slate-600 text-xs font-normal font-['Inter']">Skor Prioritas</span>
                                <span class="text-slate-900 text-xs font-semibold font-['Inter']">{{ number_format((float)($result['skor_prioritas'] ?? 0), 1) }}</span>
                            </div>
                            <div class="w-full h-2 bg-slate-100 rounded-full overflow-hidden">
                                @php
                                    $percentage = min(((float)($result['skor_prioritas'] ?? 0) / 10) * 100, 100);
                                    $barColor = match($result['tingkat_prioritas'] ?? '') {
                                        'darurat' => 'bg-red-600',
                                        'tinggi' => 'bg-amber-500',
                                        'sedang' => 'bg-blue-500',
                                        'rendah' => 'bg-slate-400',
                                        default => 'bg-slate-400',
                                    };
                                @endphp
                                <div class="h-full {{ $barColor }} rounded-full transition-all" style="width: {{ $percentage }}%"></div>
                            </div>
                        </div>

                        {{-- Tingkat Prioritas --}}
                        <div class="flex items-center gap-2">
                            <span class="text-slate-600 text-sm font-normal font-['Inter']">Tingkat:</span>
                            <div class="px-3 py-1.5 {{ $prioColors['bg'] }} rounded-full flex items-center gap-2">
                                <div class="size-1.5 {{ $prioColors['dot'] }} rounded-full"></div>
                                <span class="{{ $prioColors['text'] }} text-xs font-semibold font-['Inter']">{{ ucfirst($result['tingkat_prioritas'] ?? '-') }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Privacy info --}}
                <div class="self-stretch p-4 bg-blue-50 rounded-lg inline-flex justify-start items-start gap-3 overflow-hidden">
                    <svg class="size-5 text-blue-800 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1 inline-flex flex-col justify-start items-start gap-1 overflow-hidden">
                        <span class="self-stretch text-blue-800 text-sm font-normal font-['Inter'] leading-5">
                            Pelacakan tidak menampilkan NIK, email, atau nomor telepon pelapor.
                        </span>
                    </div>
                </div>
            </div>
        </div>
        @endif
    </main>

    {{-- Footer --}}
    <footer class="self-stretch p-6 border-t border-slate-200 flex flex-col justify-start items-center gap-2 mt-auto">
        <span class="text-center text-slate-600 text-xs font-normal font-['Inter'] leading-4">
            © 2026 SuaraDesa · Wilayah fiktif untuk demonstrasi
        </span>
        <div class="flex gap-2 text-center text-blue-800 text-xs font-normal font-['Inter'] leading-4">
            <a href="#" class="hover:underline">Bantuan layanan</a>
            <span>•</span>
            <a href="#" class="hover:underline">Kebijakan privasi</a>
            <span>•</span>
            <a href="#" class="hover:underline">Aksesibilitas</a>
        </div>
    </footer>
</div>