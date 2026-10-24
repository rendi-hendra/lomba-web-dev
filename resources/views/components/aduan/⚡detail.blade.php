<?php

use Livewire\Component;
use App\Models\Aduan;

new class extends Component
{
    public ?int $id_aduan = null;
    public ?Aduan $aduan = null;

    public function mount(?int $id_aduan = null)
    {
        $this->id_aduan = $id_aduan;

        if (!$this->id_aduan) {
            return $this->redirect('/aduan/lacak', navigate: true);
        }

        $this->aduan = Aduan::with(['penilaianPrioritas.targetRespon'])
            ->find($this->id_aduan);

        if (!$this->aduan) {
            session()->flash('error', 'Pengaduan dengan ID tersebut tidak ditemukan.');
            return $this->redirect('/aduan/lacak', navigate: true);
        }
    }
};
?>

<div class="w-full max-w-[1440px] mx-auto min-h-screen bg-slate-50 flex flex-col justify-start items-start overflow-hidden font-['Inter']">
    {{-- Livewire Navbar --}}
    <livewire:navbar />

    <main class="self-stretch px-4 sm:px-12 lg:px-24 py-8 sm:py-10 flex flex-col justify-start items-start gap-6 overflow-hidden">
        {{-- Back Button --}}
        <div>
            <a href="/aduan/lacak" class="px-4 py-2 bg-white hover:bg-slate-50 text-blue-800 border border-slate-200 rounded-lg inline-flex items-center gap-2 text-sm font-semibold transition-colors shadow-xs">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                <span>Kembali ke Lacak Pengaduan</span>
            </a>
        </div>

        @if($aduan)
            {{-- Title & Status --}}
            <div class="self-stretch flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex flex-col gap-1.5">
                    <span class="text-blue-800 text-xs font-bold uppercase tracking-wider">
                        DETAIL PENGADUAN WARGA
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                        {{ $aduan->judul_aduan ?: 'Laporan Pengaduan Masalah' }}
                    </h1>
                    <p class="text-slate-500 text-xs sm:text-sm">
                        Nomor Tiket: <strong class="font-mono text-blue-900">{{ $aduan->kode_tiket }}</strong> · Dikirim {{ $aduan->waktu_pengaduan?->translatedFormat('d M Y, H:i') }} WIB
                    </p>
                </div>

                @php
                    $badgeStyle = match (strtolower($aduan->status_aduan)) {
                        'selesai' => 'bg-emerald-100 text-emerald-800 border-emerald-300',
                        'diproses' => 'bg-blue-100 text-blue-800 border-blue-300',
                        'diteruskan' => 'bg-indigo-100 text-indigo-800 border-indigo-300',
                        'ditolak' => 'bg-rose-100 text-rose-800 border-rose-300',
                        default => 'bg-amber-100 text-amber-800 border-amber-300',
                    };
                @endphp
                <div class="px-4 py-2 rounded-xl border text-sm font-bold uppercase tracking-wider {{ $badgeStyle }} self-start sm:self-center">
                    {{ $aduan->status_aduan }}
                </div>
            </div>

            {{-- Target Response SLA Banner --}}
            @php
                $target = $aduan->penilaianPrioritas?->targetRespon;
                $penilaian = $aduan->penilaianPrioritas;
            @endphp
            @if($target)
                @php
                    $isTerlambat = now()->greaterThan($target->tenggat_respon) && !$target->waktu_respon;
                    $isSelesai = !empty($target->waktu_respon);
                @endphp
                <div class="self-stretch p-4 rounded-xl border flex items-start gap-3 {{ $isTerlambat ? 'bg-rose-50 border-rose-200 text-rose-900' : ($isSelesai ? 'bg-emerald-50 border-emerald-200 text-emerald-900' : 'bg-blue-50 border-blue-200 text-blue-900') }}">
                    <svg class="size-5 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div class="flex-1 flex flex-col gap-0.5 text-xs sm:text-sm">
                        <span class="font-bold">
                            @if($isSelesai)
                                Tanggapan Selesai Diberikan ({{ $target->waktu_respon?->translatedFormat('d M Y, H:i') }} WIB)
                            @elseif($isTerlambat)
                                Waktu respons standar telah terlampaui. Petugas sedang memprioritaskan penanganan.
                            @else
                                Perkiraan Tenggat Respons: {{ $target->tenggat_respon?->translatedFormat('d M Y, H:i') }} WIB (Sisa {{ $target->tenggat_respon?->diffForHumans(null, true) }})
                            @endif
                        </span>
                        <span class="opacity-80">
                            Tingkat Prioritas: <strong class="capitalize">{{ $penilaian->tingkat_prioritas }}</strong> · Batas Maksimum Tindak Lanjut: {{ round($target->batas_respon_menit / 60) }} jam
                        </span>
                    </div>
                </div>
            @endif

            {{-- Content Section --}}
            <div class="self-stretch grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Left Detail --}}
                <div class="lg:col-span-8 flex flex-col gap-6">
                    <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-4">
                        <h2 class="text-base font-bold text-slate-900">Uraian Pengaduan</h2>
                        <div class="p-4 bg-slate-50 rounded-lg text-sm text-slate-800 leading-relaxed whitespace-pre-line border border-slate-200/80">
                            {{ $aduan->deskripsi_aduan }}
                        </div>

                        <div class="flex flex-col gap-1 text-xs pt-2">
                            <span class="text-slate-500 font-medium">Lokasi Permasalahan:</span>
                            <span class="text-slate-800 font-semibold">{{ $aduan->alamat ?: 'Tidak disertakan' }}</span>
                        </div>
                    </div>

                    {{-- Tanggapan Resmi Petugas --}}
                    <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <h2 class="text-base font-bold text-slate-900">Tanggapan Resmi Petugas</h2>
                            @if($aduan->petugas_penanggung_jawab)
                                <span class="text-xs text-blue-800 bg-blue-50 px-2 py-0.5 rounded font-medium">
                                    Oleh: {{ $aduan->petugas_penanggung_jawab }}
                                </span>
                            @endif
                        </div>

                        @if($aduan->tanggapan)
                            <div class="p-4 bg-emerald-50/70 border border-emerald-200/80 rounded-xl text-sm text-emerald-900 leading-relaxed whitespace-pre-line">
                                {{ $aduan->tanggapan }}
                            </div>
                        @else
                            <div class="p-6 text-center text-slate-500 bg-slate-50 rounded-xl border border-dashed border-slate-200 text-xs sm:text-sm">
                                Laporan sedang ditinjau. Petugas belum memberikan catatan atau tanggapan resmi.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column info --}}
                <div class="lg:col-span-4 flex flex-col gap-6">
                    <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-4">
                        <h3 class="text-sm font-bold text-slate-900">Informasi Pelapor</h3>
                        <div class="flex flex-col gap-2 text-xs">
                            <div class="p-3 bg-blue-50/70 rounded-lg border border-blue-100 flex items-start gap-2.5">
                                <svg class="size-4 text-blue-800 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                <span class="text-blue-900">
                                    Identitas terlindungi sesuai kebijakan privasi pelayanan desa.
                                </span>
                            </div>
                            <div class="pt-2">
                                <span class="text-slate-500 block">Status Pelapor:</span>
                                <span class="font-semibold text-slate-800">
                                    {{ $aduan->nohp ? 'Menyertakan nomor kontak' : 'Laporan Anonim' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="p-6 bg-white rounded-xl border border-slate-200 shadow-xs flex flex-col gap-3">
                        <h3 class="text-sm font-bold text-slate-900">Simpan Kode Tiket</h3>
                        <p class="text-xs text-slate-500 leading-relaxed">
                            Gunakan kode tiket ini kapan saja untuk memantau pembaruan tindak lanjut masalah Anda di portal SuaraDesa.
                        </p>
                        <div class="p-3 bg-slate-50 rounded-lg text-center font-mono font-bold text-blue-900 text-base border border-slate-200 select-all">
                            {{ $aduan->kode_tiket }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </main>
</div>