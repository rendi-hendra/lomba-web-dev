<?php

use Livewire\Component;
use Livewire\Attributes\Url;
use App\Models\Aduan;

new class extends Component {
    #[Url(as: 'kode')]
    public string $kode_tiket = '';

    public ?array $aduanData = null;

    public function mount(?string $kode_tiket = null)
    {
        if ($kode_tiket) {
            $this->kode_tiket = $kode_tiket;
        }

        if ($this->kode_tiket) {
            $aduan = Aduan::where('kode_tiket', $this->kode_tiket)
                ->with(['penilaianPrioritas.targetRespon'])
                ->first();

            if ($aduan) {
                $penilaian = $aduan->penilaianPrioritas;
                $target = $penilaian?->targetRespon;

                $this->aduanData = [
                    'kode_tiket' => $aduan->kode_tiket,
                    'judul_aduan' => $aduan->judul_aduan,
                    'deskripsi_aduan' => $aduan->deskripsi_aduan,
                    'alamat' => $aduan->alamat,
                    'waktu' => $aduan->waktu_pengaduan->format('d M Y · H:i') . ' WIB',
                    'status' => $aduan->status_aduan,
                    'prioritas' => $penilaian?->tingkat_prioritas ?? '-',
                    'skor' => $penilaian?->skor_prioritas ?? 0,
                    'tenggat' => $target?->tenggat_respon?->format('d M Y · H:i') . ' WIB',
                    'batas_jam' => $target ? round($target->batas_respon_menit / 60) : 0,
                ];
            }
        }
    }
};
?>

<div
    class="w-full max-w-[1440px] mx-auto min-h-screen bg-slate-50 flex flex-col justify-start items-start overflow-hidden">
    {{-- Livewire Navbar --}}
    <livewire:navbar />

    <main
        class="self-stretch px-4 sm:px-12 lg:px-24 py-8 sm:py-10 flex flex-col justify-start items-start gap-6 overflow-hidden">
        {{-- Stepper (all steps completed) --}}
        <div class="self-stretch inline-flex justify-start items-start gap-2 overflow-hidden">
            <div class="flex-1 inline-flex flex-col justify-start items-start gap-2 overflow-hidden">
                <div class="self-stretch h-1 bg-blue-800 rounded-sm"></div>
                <span class="self-stretch justify-start text-blue-800 text-xs font-semibold font-['Inter'] leading-4">
                    1. Buat Pengaduan
                </span>
            </div>
            <div class="flex-1 inline-flex flex-col justify-start items-start gap-2 overflow-hidden">
                <div class="self-stretch h-1 bg-blue-800 rounded-sm"></div>
                <span class="self-stretch justify-start text-blue-800 text-xs font-semibold font-['Inter'] leading-4">
                    2. Isi form
                </span>
            </div>
            <div class="flex-1 inline-flex flex-col justify-start items-start gap-2 overflow-hidden">
                <div class="self-stretch h-1 bg-blue-800 rounded-sm"></div>
                <span class="self-stretch justify-start text-blue-800 text-xs font-semibold font-['Inter'] leading-4">
                    3. Dapat kode tiket
                </span>
            </div>
        </div>

        @if($aduanData)
            {{-- Success Icon & Title --}}
            <div class="self-stretch py-8 flex flex-col justify-start items-center gap-6 overflow-hidden">
                <div class="size-20 bg-green-50 rounded-full inline-flex justify-center items-center overflow-hidden">
                    <svg class="size-11 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1
                    class="self-stretch text-center text-slate-900 text-2xl sm:text-3xl font-semibold font-['Inter'] leading-tight sm:leading-[48px]">
                    Pengaduan Anda berhasil dikirim
                </h1>
                <p
                    class="self-stretch text-center text-slate-600 text-sm sm:text-base font-normal font-['Inter'] leading-6">
                    Terima kasih sudah peduli pada lingkungan. Simpan kode tiket ini untuk memantau tindak lanjut.
                </p>
            </div>

            {{-- Ticket Card --}}
            <div
                class="self-stretch p-6 bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-4 overflow-hidden">
                <span class="self-stretch text-center text-slate-600 text-sm font-normal font-['Inter'] leading-5">
                    KODE TIKET ANDA
                </span>
                <h2
                    class="self-stretch text-center text-blue-800 text-2xl sm:text-3xl font-semibold font-['Inter'] leading-tight sm:leading-[48px]">
                    {{ $aduanData['kode_tiket'] }}
                </h2>
                <p class="self-stretch text-center text-slate-900 text-base font-semibold font-['Inter'] leading-6">
                    {{ $aduanData['judul_aduan'] }}
                </p>
                <p class="self-stretch text-center text-slate-600 text-sm font-normal font-['Inter'] leading-5">
                    Diterima {{ $aduanData['waktu'] }} · {{ $aduanData['alamat'] }}
                </p>

                {{-- Priority Badge --}}
                <div class="self-stretch flex justify-center items-center gap-3">
                    @php
                        $badgeColors = match ($aduanData['prioritas']) {
                            'darurat' => 'bg-red-50 text-red-800',
                            'tinggi' => 'bg-amber-50 text-amber-800',
                            'sedang' => 'bg-blue-50 text-blue-800',
                            'rendah' => 'bg-slate-100 text-slate-600',
                            default => 'bg-slate-100 text-slate-600',
                        };
                        $dotColors = match ($aduanData['prioritas']) {
                            'darurat' => 'bg-red-600',
                            'tinggi' => 'bg-amber-500',
                            'sedang' => 'bg-blue-500',
                            'rendah' => 'bg-slate-400',
                            default => 'bg-slate-400',
                        };
                    @endphp
                    <div class="px-3 py-1.5 {{ $badgeColors }} rounded-full flex items-center gap-2">
                        <div class="size-1.5 {{ $dotColors }} rounded-full"></div>
                        <span class="text-xs font-semibold font-['Inter']">Prioritas
                            {{ ucfirst($aduanData['prioritas']) }}</span>
                    </div>
                    <span class="text-slate-600 text-xs font-normal font-['Inter']">
                        Skor: {{ number_format($aduanData['skor'], 1) }}/10
                    </span>
                </div>

                {{-- Action Buttons --}}
                <div class="self-stretch inline-flex justify-start items-start gap-4">
                    <button
                        onclick="navigator.clipboard.writeText('{{ $aduanData['kode_tiket'] }}').then(()=>alert('Kode tiket disalin!'))"
                        class="flex-1 min-h-12 px-4 py-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 hover:bg-slate-50 flex justify-center items-center gap-2 overflow-hidden transition-colors cursor-pointer">
                        <svg class="size-5 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v3m2 4H10m0 0l3-3m-3 3l3 3">
                            </path>
                        </svg>
                        <span class="text-center text-blue-800 text-sm font-semibold font-['Inter']">Salin kode</span>
                    </button>
                    <button onclick="window.print()"
                        class="flex-1 min-h-12 px-4 py-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 hover:bg-slate-50 flex justify-center items-center gap-2 overflow-hidden transition-colors cursor-pointer">
                        <svg class="size-5 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                            </path>
                        </svg>
                        <span class="text-center text-blue-800 text-sm font-semibold font-['Inter']">Simpan bukti</span>
                    </button>
                </div>

                {{-- Info Box: What happens next --}}
                <div
                    class="self-stretch p-4 bg-blue-50 rounded-lg inline-flex justify-start items-start gap-3 overflow-hidden">
                    <svg class="size-5 text-blue-800 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <div class="flex-1 inline-flex flex-col justify-start items-start gap-1 overflow-hidden">
                        <span class="self-stretch text-blue-800 text-sm font-semibold font-['Inter'] leading-5">
                            Apa yang terjadi selanjutnya?
                        </span>
                        <span class="self-stretch text-blue-800 text-sm font-normal font-['Inter'] leading-5">
                            Pengaduan Anda dinilai prioritas <strong>{{ ucfirst($aduanData['prioritas']) }}</strong>.
                            Batas respons awal adalah {{ $aduanData['batas_jam'] }} jam,
                            hingga {{ $aduanData['tenggat'] }}.
                        </span>
                    </div>
                </div>

                {{-- Navigation Buttons --}}
                <div class="self-stretch inline-flex justify-start items-start gap-4">
                    <a href="/aduan/lacak" wire:navigate
                        class="flex-1 min-h-12 px-4 py-3 bg-blue-800 hover:bg-blue-900 rounded-lg flex justify-center items-center gap-2 overflow-hidden transition-colors">
                        <svg class="size-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        <span class="text-center text-white text-sm font-semibold font-['Inter']">Lacak Pengaduan</span>
                    </a>
                    <a href="/" wire:navigate
                        class="flex-1 min-h-12 px-4 py-3 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 flex justify-center items-center gap-2 overflow-hidden transition-colors">
                        <span class="text-center text-blue-800 text-sm font-semibold font-['Inter']">Ke Beranda</span>
                    </a>
                </div>
            </div>

            {{-- Bottom Info --}}
            <div class="self-stretch p-4 bg-blue-50 rounded-lg inline-flex justify-start items-start gap-3 overflow-hidden">
                <svg class="size-5 text-blue-800 shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd"
                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                        clip-rule="evenodd"></path>
                </svg>
                <div class="flex-1 inline-flex flex-col justify-start items-start gap-1 overflow-hidden">
                    <span class="self-stretch text-blue-800 text-sm font-normal font-['Inter'] leading-5">
                        Jaga kode tiket Anda. Pelacakan menampilkan perkembangan laporan, tanpa NIK atau kontak pribadi.
                    </span>
                </div>
            </div>
        @else
            {{-- Tiket Tidak Ditemukan --}}
            <div class="self-stretch py-16 flex flex-col justify-start items-center gap-6 overflow-hidden">
                <div class="size-20 bg-red-50 rounded-full inline-flex justify-center items-center overflow-hidden">
                    <svg class="size-11 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z">
                        </path>
                    </svg>
                </div>
                <h1 class="text-slate-900 text-2xl font-semibold font-['Inter']">Tiket tidak ditemukan</h1>
                <p class="text-slate-600 text-sm font-normal font-['Inter'] text-center">Kode tiket tidak valid atau belum
                    tersedia.</p>
                <a href="/aduan" wire:navigate
                    class="min-h-12 px-6 py-3 bg-blue-800 hover:bg-blue-900 text-white rounded-lg inline-flex justify-center items-center gap-2 font-semibold font-['Inter'] text-sm transition-colors">
                    Buat Pengaduan Baru
                </a>
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