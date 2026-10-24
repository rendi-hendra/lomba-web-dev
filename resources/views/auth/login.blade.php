<x-layouts.app title="Masuk - SuaraDesa">
    <div
        class="w-full max-w-[1440px] mx-auto min-h-screen bg-slate-50 flex flex-col justify-start items-start overflow-hidden">
        {{-- Livewire Navbar --}}
        <livewire:navbar />

        <div
            class="self-stretch px-6 sm:px-12 lg:px-24 py-10 flex flex-col justify-start items-start gap-6 overflow-hidden">
            <div class="self-stretch flex flex-col lg:flex-row justify-start items-start gap-8">
                {{-- Info Sisi Kiri --}}
                <div class="flex-1 w-full flex flex-col justify-start items-start gap-6">
                    <div
                        class="self-stretch h-64 sm:h-96 rounded-xl overflow-hidden bg-slate-200 border border-slate-200 relative">
                        <img class="w-full h-full object-cover"
                            src="https://images.unsplash.com/photo-1577495508048-b635879837f1?auto=format&fit=crop&w=1000&q=80"
                            alt="Suara Warga Desa" />
                        <div
                            class="absolute inset-0 bg-gradient-to-t from-slate-900/60 to-transparent flex items-end p-6">
                            <span class="text-white text-lg font-medium font-['Inter']">Bersama membangun desa yang
                                lebih transparan dan responsif.</span>
                        </div>
                    </div>
                    <div
                        class="self-stretch text-slate-900 text-2xl sm:text-3xl font-semibold font-['Inter'] leading-tight sm:leading-[48px]">
                        Suara Anda membantu desa bertumbuh.
                    </div>
                    <div class="self-stretch text-slate-600 text-sm sm:text-base font-normal font-['Inter'] leading-6">
                        Satu akun untuk melapor, memantau tindak lanjut, dan memberi masukan bagi pelayanan yang lebih
                        baik.
                    </div>
                    <div class="self-stretch p-4 bg-blue-50 rounded-lg inline-flex justify-start items-start gap-3">
                        <div class="size-5 relative overflow-hidden shrink-0 mt-0.5">
                            <svg class="size-5 text-blue-800" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="flex-1 flex flex-col justify-start items-start gap-1">
                            <div class="self-stretch text-blue-800 text-sm font-normal font-['Inter'] leading-5">
                                Identitas digunakan untuk verifikasi, bukan ditampilkan kepada publik.
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Login Sisi Kanan --}}
                <div
                    class="w-full lg:w-[480px] p-6 sm:p-8 bg-white rounded-xl shadow-[0px_4px_16px_0px_rgba(15,23,42,0.03)] outline outline-1 outline-offset-[-1px] outline-slate-200 flex flex-col justify-start items-start gap-6">
                    <div class="self-stretch flex flex-col justify-start items-start gap-2">
                        <h1
                            class="self-stretch text-slate-900 text-2xl sm:text-3xl font-semibold font-['Inter'] leading-tight sm:leading-[48px]">
                            Selamat datang kembali
                        </h1>
                        <p
                            class="self-stretch text-slate-600 text-sm sm:text-base font-normal font-['Inter'] leading-6">
                            Masuk untuk melihat pengaduan dan perkembangannya.
                        </p>
                    </div>

                    {{-- Form Nyata --}}
                    <form method="POST" action="/login" class="self-stretch flex flex-col gap-4">
                        @csrf

                        {{-- Alert Error Global --}}
                        @if ($errors->any())
                            <div class="p-3 bg-red-50 border border-red-200 rounded-lg text-red-700 text-xs font-['Inter']">
                                <ul class="list-disc list-inside">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        {{-- Input: Username --}}
                        <div class="self-stretch flex flex-col justify-start items-start gap-2">
                            <label for="username"
                                class="self-stretch text-slate-900 text-sm font-semibold font-['Inter'] leading-5">
                                Username
                            </label>
                            <input type="username" id="username" name="username" value="{{ old('username') }}" required
                                autofocus placeholder="username"
                                class="self-stretch min-h-12 p-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 focus:outline-2 focus:outline-blue-800 text-slate-900 placeholder:text-slate-400 text-sm font-normal font-['Inter'] transition-all" />
                            <span class="self-stretch text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                                Gunakan username aktif yang terdaftar di sistem.
                            </span>
                        </div>

                        {{-- Input: Kata Sandi --}}
                        <div class="self-stretch flex flex-col justify-start items-start gap-2">
                            <label for="password"
                                class="self-stretch text-slate-900 text-sm font-semibold font-['Inter'] leading-5">
                                Kata sandi
                            </label>
                            <input type="password" id="password" name="password" required placeholder="••••••••••••"
                                class="self-stretch min-h-12 p-3 bg-white rounded-lg outline outline-1 outline-offset-[-1px] outline-slate-200 focus:outline-2 focus:outline-blue-800 text-slate-900 placeholder:text-slate-400 text-sm font-normal font-['Inter'] transition-all" />
                            <span class="self-stretch text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                                Minimal 8 karakter.
                            </span>
                        </div>

                        {{-- Ingat Saya & Lupa Sandi --}}
                        <div class="self-stretch flex justify-between items-center gap-4">
                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                <input type="checkbox" name="remember"
                                    class="size-4 rounded border-slate-300 text-blue-800 focus:ring-blue-800" />
                                <span class="text-slate-900 text-sm font-normal font-['Inter']">Ingat saya</span>
                            </label>
                            <a href="/forgot-password"
                                class="text-right text-blue-800 hover:underline text-sm font-normal font-['Inter']">
                                Lupa kata sandi?
                            </a>
                        </div>

                        {{-- Tombol Submit --}}
                        <button type="submit"
                            class="self-stretch min-h-12 px-6 py-3 bg-blue-800 hover:bg-blue-900 text-white rounded-lg flex justify-center items-center gap-2 font-semibold font-['Inter'] text-sm transition-colors cursor-pointer shadow-sm mt-2">
                            <span>Masuk ke Akun</span>
                        </button>
                    </form>

                    {{-- Link Daftar --}}
                    <div class="self-stretch text-center text-slate-900 text-sm font-normal font-['Inter'] leading-5">
                        Belum punya akun?
                        <a href="/register" class="text-blue-800 font-semibold hover:underline">Daftar sekarang</a>
                    </div>

                    {{-- Info Petugas --}}
                    <div class="self-stretch p-4 bg-blue-50 rounded-lg flex justify-start items-start gap-3">
                        <svg class="size-5 text-blue-800 shrink-0 mt-0.5" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z">
                            </path>
                        </svg>
                        <div class="flex-1 flex flex-col justify-start items-start gap-1">
                            <span class="self-stretch text-blue-800 text-sm font-semibold font-['Inter'] leading-5">
                                Akses petugas dan admin
                            </span>
                            <span class="self-stretch text-blue-800 text-xs font-normal font-['Inter'] leading-4">
                                Gunakan akun yang terdaftar. Petugas RT, Admin Desa, atau Admin Kecamatan akan diarahkan
                                ke dashboard wilayah.
                            </span>
                        </div>
                    </div>

                    <p class="self-stretch text-slate-600 text-xs font-normal font-['Inter'] leading-4">
                        Anda tetap bisa melacak pengaduan dengan kode tiket tanpa harus masuk.
                    </p>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <footer
            class="self-stretch p-6 border-t border-slate-200 flex flex-col justify-start items-center gap-2 mt-auto">
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
</x-layouts.app>