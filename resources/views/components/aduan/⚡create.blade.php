<?php

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Aduan;
use App\Services\PrioritasService;

new class extends Component {
    use WithFileUploads;

    public $judul_aduan = '';
    public $deskripsi_aduan = '';
    public $kontak = '';
    public $lokasi = '';
    public $foto;
    public $is_anonim = false;
    public $setuju = false;

    public function save()
    {
        // Validasi — jika tidak valid, tampilkan kesalahan (sesuai Activity Diagram: "Pengaduan Valid? → Tidak → Menampilkan Kesalahan")
        $this->validate([
            'judul_aduan' => 'required|min:5|max:100',
            'deskripsi_aduan' => 'required|min:10',
            'lokasi' => 'required',
            'setuju' => 'accepted',
        ], [
            'judul_aduan.required' => 'Judul pengaduan wajib diisi.',
            'judul_aduan.min' => 'Judul minimal 5 karakter.',
            'judul_aduan.max' => 'Judul maksimal 100 karakter.',
            'deskripsi_aduan.required' => 'Deskripsi kejadian wajib diisi.',
            'deskripsi_aduan.min' => 'Deskripsi minimal 10 karakter.',
            'lokasi.required' => 'Alamat / patokan lokasi wajib diisi.',
            'setuju.accepted' => 'Anda harus menyetujui pernyataan dan kebijakan privasi.',
        ]);

        try {
            // Membuat Kode Tiket (sesuai Activity Diagram: "Membuat Kode Tiket")
            $kodeTiket = 'SD-' . date('Y') . '-' . strtoupper(\Illuminate\Support\Str::random(6));

            // Menyimpan ke tabel ADUAN
            $aduan = Aduan::create([
                'kode_tiket' => $kodeTiket,
                'judul_aduan' => $this->judul_aduan,
                'deskripsi_aduan' => $this->deskripsi_aduan,
                'alamat' => $this->lokasi,
                'nohp' => ($this->is_anonim || empty($this->kontak)) ? null : $this->kontak,
                'status_aduan' => 'diajukan',
                'waktu_pengaduan' => now(),
            ]);

            // AI: Menilai urgensi, dampak, jumlah warga → hitung skor → tentukan prioritas → tentukan batas respon → simpan laporan
            $prioritasService = new PrioritasService();
            $prioritasService->proses($aduan);

            session()->flash('success_message', 'Pengaduan berhasil diajukan dengan kode tiket: ' . $kodeTiket);

            // Redirect ke halaman tiket (sesuai Activity Diagram: "Menerima Tiket")
            return $this->redirect("/aduan/tiket/{$kodeTiket}", navigate: true);
        } catch (\Throwable $th) {
            session()->flash('error_message', 'Gagal menyimpan pengaduan: ' . $th->getMessage());
        }
    }
};
?>

<div
    class="w-full max-w-[1440px] mx-auto min-h-screen bg-slate-50 flex flex-col justify-start items-start overflow-hidden">
    {{-- Livewire Navbar --}}
    <livewire:navbar />

    {{-- Main Container --}}
    <main
        class="self-stretch px-4 sm:px-12 lg:px-24 py-8 sm:py-10 flex flex-col justify-start items-start gap-6 overflow-hidden">
        {{-- Flash Message --}}
        @if (session()->has('success_message'))
            <div
                class="self-stretch p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="size-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium font-['Inter']">{{ session('success_message') }}</span>
                </div>
            </div>
        @endif

        @if (session()->has('error_message'))
            <div
                class="self-stretch p-4 rounded-xl bg-red-50 border border-red-200 text-red-800 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="size-5 text-red-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="text-sm font-medium font-['Inter']">{{ session('error_message') }}</span>
                </div>
            </div>
        @endif

        {{-- Title Section --}}
        <div class="self-stretch flex flex-col justify-start items-start gap-2 overflow-hidden">
            <span
                class="self-stretch justify-start text-blue-800 text-xs font-semibold font-['Inter'] tracking-wider uppercase">
                LAYANAN WARGA
            </span>
            <h1
                class="self-stretch justify-start text-slate-900 text-2xl sm:text-3xl font-semibold font-['Inter'] leading-tight sm:leading-[48px]">
                Ceritakan yang perlu diperbaiki
            </h1>
            <p
                class="self-stretch justify-start text-slate-600 text-sm sm:text-base font-normal font-['Inter'] leading-6">
                Isi satu formulir singkat. Kami bantu meneruskan ke petugas yang tepat.
            </p>
        </div>

        {{-- Stepper --}}
        <div class="self-stretch inline-flex justify-start items-start gap-2 overflow-hidden">
            <div class="flex-1 inline-flex flex-col justify-start items-start gap-2 overflow-hidden">
                <div class="self-stretch h-1 bg-blue-800 rounded-sm"></div>
                <span class="self-stretch justify-start text-blue-800 text-xs font-semibold font-['Inter'] leading-4">
                    1. Buat Pengaduan
                </span>
            </div>
            <div class="flex-1 inline-flex flex-col justify-start items-start gap-2 overflow-hidden">
                <div class="self-stretch h-1 bg-slate-200 rounded-sm"></div>
                <span class="self-stretch justify-start text-slate-500 text-xs font-semibold font-['Inter'] leading-4">
                    2. Isi form
                </span>
            </div>
            <div class="flex-1 inline-flex flex-col justify-start items-start gap-2 overflow-hidden">
                <div class="self-stretch h-1 bg-slate-200 rounded-sm"></div>
                <span class="self-stretch justify-start text-slate-500 text-xs font-semibold font-['Inter'] leading-4">
                    3. Dapat kode tiket
                </span>
            </div>
        </div>

        {{-- FORM ELEMENT --}}
        <form wire:submit="save" class="self-stretch flex flex-col gap-6">
            <div class="self-stretch flex flex-col lg:flex-row justify-start items-start gap-6">
                {{-- Left Column --}}
                <div class="flex-1 w-full flex flex-col justify-start items-start gap-6">
                    {{-- Card: Informasi Pengaduan --}}
                    <div
                        class="self-stretch p-6 bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-4">
                        <h2
                            class="self-stretch justify-start text-slate-900 text-xl font-semibold font-['Inter'] leading-8">
                            Informasi pengaduan
                        </h2>
                        <p
                            class="self-stretch justify-start text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                            Tanda * menunjukkan isian wajib.
                        </p>

                        {{-- Input: Judul Pengaduan --}}
                        <div class="self-stretch flex flex-col justify-start items-start gap-2">
                            <label for="judul_aduan"
                                class="self-stretch justify-start text-slate-900 text-sm font-semibold font-['Inter'] leading-5">
                                Judul pengaduan <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="judul_aduan" wire:model="judul_aduan"
                                placeholder="Contoh: Jalan berlubang di Jl. Melati" maxlength="100" required
                                class="self-stretch min-h-12 p-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 focus:outline-2 focus:outline-blue-800 text-slate-900 placeholder:text-slate-400 text-sm font-normal font-['Inter'] leading-5 transition-all" />
                            @error('judul_aduan')
                                <span class="text-xs text-red-600 font-['Inter']">{{ $message }}</span>
                            @enderror
                            <span
                                class="self-stretch justify-start text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                                Buat judul singkat dan jelas. Maksimal 100 karakter.
                            </span>
                        </div>

                        {{-- Textarea: Deskripsi Kejadian --}}
                        <div class="self-stretch flex flex-col justify-start items-start gap-2">
                            <label for="deskripsi_aduan"
                                class="self-stretch justify-start text-slate-900 text-sm font-semibold font-['Inter'] leading-5">
                                Deskripsi kejadian <span class="text-red-600">*</span>
                            </label>
                            <textarea id="deskripsi_aduan" wire:model="deskripsi_aduan" rows="4"
                                placeholder="Tiga lubang cukup dalam di depan pos ronda RT 03. Saat hujan lubang tertutup genangan dan membahayakan pengendara. Mohon diperbaiki agar warga dapat melintas dengan aman."
                                required
                                class="self-stretch min-h-32 p-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 focus:outline-2 focus:outline-blue-800 text-slate-900 placeholder:text-slate-400 text-sm font-normal font-['Inter'] leading-5 transition-all resize-y"></textarea>
                            @error('deskripsi_aduan')
                                <span class="text-xs text-red-600 font-['Inter']">{{ $message }}</span>
                            @enderror
                            <span
                                class="self-stretch justify-start text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                                Jelaskan kondisi, dampak, dan kapan terjadi. Minimal 10 karakter.
                            </span>
                        </div>

                        {{-- Input: Kontak --}}
                        <div class="self-stretch flex flex-col justify-start items-start gap-2">
                            <label for="kontak"
                                class="self-stretch justify-start text-slate-900 text-sm font-semibold font-['Inter'] leading-5">
                                Kontak untuk pemberitahuan
                            </label>
                            <input type="tel" id="kontak" wire:model="kontak" placeholder="Contoh: 0867676526382"
                                class="self-stretch min-h-12 p-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 focus:outline-2 focus:outline-blue-800 text-slate-900 placeholder:text-slate-400 text-sm font-normal font-['Inter'] leading-5 transition-all" />
                            <span
                                class="self-stretch justify-start text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                                Tidak ditampilkan kepada publik.
                            </span>
                        </div>
                    </div>

                    {{-- Card: Foto Pendukung --}}
                    <div
                        class="self-stretch p-6 bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-4">
                        <h2
                            class="self-stretch justify-start text-slate-900 text-xl font-semibold font-['Inter'] leading-8">
                            Foto pendukung
                        </h2>

                        {{-- File Upload Drop Area --}}
                        <div
                            class="self-stretch p-6 bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-center gap-3">
                            <div class="size-7 relative flex items-center justify-center">
                                <svg class="size-6 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                    </path>
                                </svg>
                            </div>
                            <span
                                class="self-stretch text-center justify-start text-slate-900 text-sm font-semibold font-['Inter'] leading-5">
                                Tambahkan foto kondisi
                            </span>
                            <span
                                class="self-stretch text-center justify-start text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                                JPG atau PNG · Maks. 5 MB per foto · Maks. 3 foto
                            </span>

                            <input type="file" id="foto" wire:model="foto" accept="image/png, image/jpeg, image/jpg"
                                class="hidden" />
                            <label for="foto"
                                class="cursor-pointer min-h-12 px-4 py-3 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex justify-center items-center gap-2 transition-colors">
                                <svg class="size-4 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 4v16m8-8H4"></path>
                                </svg>
                                <span class="text-center text-blue-800 text-sm font-semibold font-['Inter']">
                                    Pilih foto
                                </span>
                            </label>
                        </div>

                        {{-- Foto Preview / List --}}
                        @if ($foto)
                            <div
                                class="self-stretch inline-flex justify-start items-center gap-4 p-3 bg-slate-50 rounded-lg border border-slate-200">
                                <div
                                    class="size-16 rounded-lg bg-slate-200 overflow-hidden flex items-center justify-center">
                                    <img class="w-full h-full object-cover" src="{{ $foto->temporaryUrl() }}"
                                        alt="Preview" />
                                </div>
                                <div class="flex-1 flex flex-col justify-start items-start gap-1">
                                    <span
                                        class="text-slate-900 text-sm font-semibold font-['Inter'] leading-5 truncate max-w-[200px]">
                                        {{ $foto->getClientOriginalName() }}
                                    </span>
                                    <span class="text-slate-600 text-xs font-normal font-['Inter']">
                                        {{ number_format($foto->getSize() / 1024, 1) }} KB
                                    </span>
                                </div>
                                <button type="button" wire:click="$set('foto', null)"
                                    class="px-3 py-1.5 text-red-600 hover:text-red-700 hover:bg-red-50 rounded-md text-xs font-semibold font-['Inter'] transition-colors">
                                    Hapus
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Right Column --}}
                <div class="w-full lg:w-96 flex flex-col justify-start items-start gap-6">
                    {{-- Card: Lokasi Kejadian --}}
                    <div
                        class="self-stretch p-6 py-[56px] bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-4">
                        <h2
                            class="self-stretch justify-start text-slate-900 text-xl font-semibold font-['Inter'] leading-8">
                            Lokasi kejadian
                        </h2>

                        {{-- Input: Alamat --}}
                        <div class="self-stretch flex flex-col justify-start items-start gap-2">
                            <label for="lokasi"
                                class="self-stretch justify-start text-slate-900 text-sm font-semibold font-['Inter'] leading-5">
                                Alamat / patokan <span class="text-red-600">*</span>
                            </label>
                            <input type="text" id="lokasi" wire:model="lokasi"
                                placeholder="Contoh: Jl. Melati No. 12, RT 03" required
                                class="self-stretch min-h-12 p-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 focus:outline-2 focus:outline-blue-800 text-slate-900 placeholder:text-slate-400 text-sm font-normal font-['Inter'] leading-5 transition-all" />
                            @error('lokasi')
                                <span class="text-xs text-red-600 font-['Inter']">{{ $message }}</span>
                            @enderror
                        </div>

                        {{-- Peta Skematis Visual --}}
                        <div
                            class="self-stretch h-56 relative bg-sky-50 rounded-xl overflow-hidden border border-slate-200 flex items-center justify-center">
                            <div class="absolute inset-0 opacity-40">
                                <div
                                    class="w-full h-full bg-[radial-gradient(#93c5fd_1px,transparent_1px)] [background-size:16px_16px]">
                                </div>
                            </div>
                            <div class="relative z-10 flex flex-col items-center gap-2 text-center px-4">
                                <div
                                    class="size-10 bg-blue-800 text-white rounded-full flex items-center justify-center shadow-lg animate-bounce">
                                    <svg class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                </div>
                                <span class="text-slate-700 text-xs font-semibold font-['Inter']">Titik Penanda
                                    Lokasi</span>
                                <span class="text-slate-500 text-[11px] font-['Inter']">Pin: -6.8120, 107.6240 ·
                                    Desa Sukamaju</span>
                            </div>
                        </div>

                        <button type="button" onclick="alert('Fitur geolocation dapat dihubungkan ke GPS browser')"
                            class="w-full min-h-12 px-4 py-3 bg-white hover:bg-slate-50 rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 inline-flex justify-center items-center gap-2 transition-colors cursor-pointer">
                            <svg class="size-4 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 004 11a7.978 7.978 0 001.07 3.99">
                                </path>
                            </svg>
                            <span class="text-center text-blue-800 text-sm font-semibold font-['Inter']">
                                Gunakan lokasi saya
                            </span>
                        </button>
                    </div>
                </div>
            </div>

            {{-- Bottom Submit Card --}}
            <div
                class="self-stretch p-6 bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-4">
                <div class="self-stretch flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <label class="flex-1 flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" wire:model="setuju" required
                            class="size-5 rounded border-slate-300 text-blue-800 focus:ring-blue-800 cursor-pointer" />
                        <span class="text-slate-900 text-sm font-normal font-['Inter'] leading-5">
                            Saya memastikan informasi ini benar dan menyetujui kebijakan privasi. <span
                                class="text-red-600">*</span>
                        </span>
                    </label>
                    <button type="submit" wire:loading.attr="disabled"
                        class="w-full sm:w-auto min-h-12 px-6 py-3 bg-blue-800 hover:bg-blue-900 disabled:opacity-50 text-white rounded-lg inline-flex justify-center items-center gap-2 font-semibold font-['Inter'] text-sm transition-colors cursor-pointer shadow-sm">
                        <span wire:loading.remove>Kirim Pengaduan</span>
                        <span wire:loading class="inline-flex items-center gap-2">
                            <svg class="animate-spin size-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                    stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            Mengirim...
                        </span>
                    </button>
                </div>

                @error('setuju')
                    <span class="text-xs text-red-600 font-['Inter']">{{ $message }}</span>
                @enderror

                <p class="self-stretch justify-start text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                    Periksa isian yang ditandai sebelum mengirim. Setelah terkirim, simpan kode tiket untuk melacak.
                </p>
            </div>
        </form>
    </main>

    {{-- Footer --}}
    <footer class="self-stretch p-6 border-t border-slate-200 flex flex-col justify-start items-center gap-2">
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