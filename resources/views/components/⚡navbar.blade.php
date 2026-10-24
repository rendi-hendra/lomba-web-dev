<?php

use Livewire\Component;

new class extends Component {
    //
};
?>

<header x-data="{ mobileMenu: false }" class="self-stretch w-full bg-white border-b border-slate-200 sticky top-0 z-40 font-['Inter']">
    <div class="h-16 sm:h-20 px-4 sm:px-8 lg:px-16 flex justify-between items-center max-w-[1440px] mx-auto">
        {{-- Brand Logo --}}
        <a href="/" class="flex items-center gap-2.5 hover:opacity-90 transition-opacity shrink-0">
            <div class="size-8 sm:size-9 bg-blue-800 rounded-xl flex justify-center items-center shadow-xs">
                <svg class="size-4 sm:size-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z">
                    </path>
                </svg>
            </div>
            <div>
                <span class="text-blue-800 text-lg sm:text-xl font-bold tracking-tight block">SuaraDesa</span>
            </div>
        </a>

        {{-- Desktop & Tablet Navigation (tetap terlihat di browser normal/medium >= sm, tidak langsung jadi hamburger) --}}
        <nav class="hidden sm:flex items-center gap-2 md:gap-6">
            <a href="/"
                class="px-2.5 py-1.5 rounded-lg text-xs md:text-sm transition-colors {{ request()->is('/') ? 'text-blue-800 font-bold bg-blue-50/70' : 'text-slate-600 hover:text-slate-900 font-medium hover:bg-slate-50' }}">
                Beranda
            </a>
            <a href="/aduan"
                class="px-2.5 py-1.5 rounded-lg text-xs md:text-sm transition-colors {{ request()->is('aduan') ? 'text-blue-800 font-bold bg-blue-50/70' : 'text-slate-600 hover:text-slate-900 font-medium hover:bg-slate-50' }}">
                Buat Pengaduan
            </a>
            <a href="/aduan/lacak"
                class="px-2.5 py-1.5 rounded-lg text-xs md:text-sm transition-colors {{ request()->is('aduan/lacak*') ? 'text-blue-800 font-bold bg-blue-50/70' : 'text-slate-600 hover:text-slate-900 font-medium hover:bg-slate-50' }}">
                Lacak Pengaduan
            </a>
        </nav>

        {{-- Right Side Auth Links (tetap terlihat berdampingan pada layar >= sm) --}}
        <div class="hidden sm:flex items-center gap-2">
            @auth
                <a href="/dashboard" class="px-3.5 py-2 bg-blue-800 hover:bg-blue-900 text-white rounded-lg text-xs md:text-sm font-semibold transition-colors flex items-center gap-1.5 shadow-xs">
                    <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                    <span>Dashboard</span>
                </a>
            @else
                <a href="/login" class="px-3.5 py-2 bg-white hover:bg-slate-50 text-slate-700 border border-slate-300 hover:border-slate-400 rounded-lg text-xs md:text-sm font-semibold transition-colors flex items-center gap-1.5">
                    <svg class="size-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                    <span>Masuk Petugas</span>
                </a>
            @endauth
        </div>

        {{-- Mobile Only Hamburger Button (hanya muncul saat ukuran layar benar-benar kecil < sm / ponsel) --}}
        <div class="sm:hidden flex items-center">
            <button @click="mobileMenu = !mobileMenu" type="button" class="p-2 text-slate-600 hover:text-slate-900 hover:bg-slate-100 rounded-lg focus:outline-none transition-colors cursor-pointer" aria-label="Toggle menu">
                <svg x-show="!mobileMenu" class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                </svg>
                <svg x-show="mobileMenu" x-cloak class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Mobile Menu Dropdown (hanya saat layar < sm dan tombol toggle diklik) --}}
    <div x-show="mobileMenu" 
         x-cloak 
         x-transition:enter="transition ease-out duration-150"
         x-transition:enter-start="opacity-0 -translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-2"
         class="sm:hidden border-t border-slate-100 bg-white px-6 py-4 flex flex-col gap-2 shadow-lg">
        <a href="/" class="text-sm font-medium py-2 px-3 rounded-lg {{ request()->is('/') ? 'text-blue-800 font-bold bg-blue-50' : 'text-slate-700 hover:bg-slate-50' }}">
            Beranda
        </a>
        <a href="/aduan" class="text-sm font-medium py-2 px-3 rounded-lg {{ request()->is('aduan') ? 'text-blue-800 font-bold bg-blue-50' : 'text-slate-700 hover:bg-slate-50' }}">
            Buat Pengaduan
        </a>
        <a href="/aduan/lacak" class="text-sm font-medium py-2 px-3 rounded-lg {{ request()->is('aduan/lacak*') ? 'text-blue-800 font-bold bg-blue-50' : 'text-slate-700 hover:bg-slate-50' }}">
            Lacak Pengaduan
        </a>
        <div class="pt-3 border-t border-slate-100 mt-1">
            @auth
                <a href="/dashboard" class="w-full text-center px-4 py-2.5 bg-blue-800 hover:bg-blue-900 text-white font-semibold rounded-lg text-sm block transition-colors">
                    Dashboard Petugas
                </a>
            @else
                <a href="/login" class="w-full text-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold rounded-lg text-sm block transition-colors">
                    Masuk Petugas
                </a>
            @endauth
        </div>
    </div>
</header>